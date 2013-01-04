# -*- coding: utf-8 -*-
from decimal import Decimal, InvalidOperation
import datetime
import time
import logging

from django.db import models
from django.db.models import Sum, Q, query
from django.utils.translation import ugettext_lazy as _
from django.conf import settings

from twosell.managers import (FinalsManager, PrelsManager, UpdateManager,
    TwosellSalesManagerMixin)
from utils.dt import full_day_datetimes
from utils.vat import calc_excl_vat, calc_incl_vat
from twosell.models.inventory import (DISCOUNT_TYPES, Product, ProductInStore, 
    DEFAULT_VALID, ProductGroup)
from twosell.models.locations import Chain, Store, PointOfSale, Seller
from twosell.models.conf import TwosellConf

logger = logging.getLogger('pocada.%s' %(__name__))


class CalculationException(Exception):
    pass


def get_twosell_positemid(title=None):
    if title:
        # Check to see if there is already a product with twosell generated 
        # article number and the same title and then use that article number.
        try:
            product = Product.objects.get(title=title, articlenum__startswith='twosell-')
        except Product.DoesNotExist:
            pass
        else:
            return product.articlenum
    
    # No product with twosell generated article number and the same title exists.
    positemid = 'twosell-%f' %(time.time())
    
    try:
        Product.objects.get(articlenum=positemid)
    except Product.DoesNotExist:
        return positemid
    else:
        return get_twosell_positemid()


class PurchaseManager(UpdateManager):
    
    def get_by_natural_key(self, transactionid):
        return self.get(transactionid=transactionid)
    
    def find(self, **kwargs):
        start_dt, end_dt = full_day_datetimes(kwargs.pop('start_date', None), 
            kwargs.pop('end_date', None))
        location = kwargs.pop('location', None)
        filters = []
        
        if isinstance(location, query.QuerySet):
            location_model = getattr(location, 'model', None)
            
            if location_model == Chain:
                filters.append(Q(pos__store__chain__in=location))
            elif location_model == Store:
                filters.append(Q(pos__store__in=location))
            elif location_model == PointOfSale:
                filters.append(Q(pos__in=location))
            elif location_model == Seller:
                filters.append(Q(seller__in=location))
        elif location:
            location_model = location.__class__
            
            if location_model == Chain:
                filters.append(Q(pos__store__chain=location))
            elif location_model == Store:
                filters.append(Q(pos__store=location))
            elif location_model == PointOfSale:
                filters.append(Q(pos=location))
            elif location_model == Seller:
                filters.append(Q(seller=location))
        
        if start_dt:
            filters.append(Q(time_of_purchase__gte=start_dt))
        
        if end_dt:
            filters.append(Q(time_of_purchase__lte=end_dt))
        
        if filters:
            return self.get_query_set().filter(*filters)
        
        return self.get_query_set()
    
    def save_basic_receipt(self, receipt_dct, allow_updates, excl_vat=False):
        """
        Creates a basic Purchase object from receipt_dct. There is no guarantee
        that the object is saved to the database.
        
        This method should not be called directly, is called on save_receipt in 
        PrelReceiptManager and FinalReceiptManager. The Purchase object is saved
        in these methods.
        
        Expects receipt_dct to be a dictionary on the following form. 
        ('key': value_type (default_value)) Keys without default value must
        be supplied.
        
        Everything but 'extra' keys is handled by this method.
        
        receipt_dct = {
            'transactionid': string,
            'datetime': datetime,
            'final': Boolean,
            'chain_id': string,
            'store_id': string,
            'device_id': string,
            'seller_id': string,
            'discounts': [] ([]),
            'items': [{
                'title': string, 
                'id': string,
                'quantity': Decimal,
                'price': Decimal,
                'tax_rate': Decimal,
                'discounts': [{
                    'amount': Decimal,
                    'twosell_id': string ("")
                }, ...] ([]),
                'extra': {
                    'saleitem_type': string ("valid"),
                    'supplier_name': string (""),
                    'supplier_id': string (""),
                    'categories': [{
                        'title': string,
                        'id': string,
                        'parent': {
                            'name': string,
                            'id': string,
                            'parent': {...},
                        }
                    }],
                }
            },...],
            'extra': {
                'store_title': string,
                'store_address': string,
                'store_postal_code': string,
                'store_city': string,
                'store_phone': string,
            }
        }
        """
        if not allow_updates:
            try:
                self.get(
                    transactionid=receipt_dct['transactionid'], 
                    final=receipt_dct['final']
                )
            except self.model.DoesNotExist:
                pass
            else:
                # Purchase already exists, do not update
                return None
        
        purchase = self._save_purchase_info(receipt_dct, excl_vat)
        self._save_rows(purchase, receipt_dct, excl_vat)
        purchase.update_totals()
        purchase.save()
        
        return purchase
    
    def _save_purchase_info(self, receipt_dct, excl_vat=False):
        chain, created = Chain.objects.get_or_create(
            internal_id=receipt_dct['chain_id']
        )
        store, created = Store.objects.get_or_create(chain=chain,
            internal_id=receipt_dct['store_id']
        )
        pos, created = PointOfSale.objects.get_or_create(store=store,
            idnum=receipt_dct['device_id']
        )
        seller, created = Seller.objects.get_or_create(store=store,
            idnum=receipt_dct['seller_id']
        )
        
        purchase, created = self.create_or_update(
            transactionid=receipt_dct['transactionid'], final=receipt_dct['final'], 
            time_of_purchase=receipt_dct['datetime'], pos=pos, seller=seller
        )
        
        return purchase
    
    def _save_rows(self, purchase, receipt_dct, excl_vat=False):
        """ 
        Save all saleitems in receipt_dct.
        """
        store = purchase.pos.store
        chain = store.chain
        total_cost = 0
        total_cost_excl_vat = 0
        count_extra = settings.COUNT_EXTRA_AS_TWOSELL
        
        for item in receipt_dct['items']:
            if not item['id']:
                item['id'] = get_twosell_positemid(title=item['title'])
            
            positemid = item['id']
            vat_rate = item['tax_rate']
            price = calc_incl_vat(vat_rate, item['price']) if excl_vat else item['price']
            quantity = item['quantity']
            row_cost = price * quantity
            row_discount, twosell_coupon_used, twosell_coupon_discount = self._handle_row_discounts(item, purchase, vat_rate, excl_vat)
            
            modified = Product.objects.is_modified(positemid, item['title'])
            
            # Product data
            product, created = Product.objects.create_or_update(chain=chain, 
                articlenum=positemid, title=item['title'], vat_rate=vat_rate,
                modified=modified
            )
            
            # Product in store data
            product_in_store, created = ProductInStore.objects.create_or_update(
                product=product, store=store, price=price, active=True
            )
            
            # Purchased product data
            purchased_prod, created = PurchasedProduct.objects.get_or_create(
                product=product, purchase=purchase, final=receipt_dct['final']
            )
            purchased_prod.n_items = quantity
            purchased_prod.total_discount = row_discount
            purchased_prod.total_cost = (row_cost - row_discount)
            
            if twosell_coupon_used:
                purchased_prod.twosell_coupon = row_cost - twosell_coupon_discount
            
            purchased_prod.save()
            total_cost += purchased_prod.total_cost
            total_cost_excl_vat += purchased_prod.total_cost_excl_vat
        
        try:
            total_vat_rate = (total_cost - total_cost_excl_vat / total_cost_excl_vat)
        except (InvalidOperation, ZeroDivisionError):
            pass
        else:
            self._handle_gen_discount(purchase, receipt_dct, total_vat_rate, excl_vat)
        
        return purchase
    
    def _handle_row_discounts(self, dct_item, purchase, vat_rate, excl_vat=False):
        row_discount = 0
        twosell_coupon_discount = 0
        twosell_coupon_used = False
        for discount in dct_item.get('discounts', []):
            twosell_id = discount.get('twosell_id', '')
            amount = calc_incl_vat(vat_rate, discount['amount']) if excl_vat else discount['amount']
            row_discount += amount
            
            if Offer.objects.use_code(twosell_id, purchase):
                twosell_coupon_used = True
                twosell_coupon_discount += amount
            
        return row_discount, twosell_coupon_used, twosell_coupon_discount
    
    def _handle_gen_discount(self, purchase, receipt_dct, vat_rate, excl_vat):
        if excl_vat:
            purchase.gen_discount = calc_incl_vat(vat_rate, sum(receipt_dct.get('discounts', [])))
        else:
            purchase.gen_discount = sum(receipt_dct.get('discounts', []))
    

class PrelPurchaseManager(PurchaseManager):
    def get_query_set(self):
        return super(PrelPurchaseManager, self).get_query_set().filter(final=False)
    
    def save_receipt(self, receipt_dct, allow_updates=False, excl_vat=False):
        """
        See PurchaseManager._create_basic_receipt for expected receipt_dct format.
        """
        return self.save_basic_receipt(receipt_dct, allow_updates, excl_vat)
    

class FinalPurchaseManager(PurchaseManager, TwosellSalesManagerMixin):
    def get_query_set(self):
        return super(FinalPurchaseManager, self).get_query_set().filter(final=True)
    
    def save_receipt(self, receipt_dct, allow_updates=False, excl_vat=False):
        """ 
        See PurchaseManager._create_basic_receipt for expected receipt_dct format.
        """
        purchase = self.save_basic_receipt(receipt_dct, allow_updates, excl_vat)
        
        if purchase:
            self._save_extra_info(purchase, receipt_dct)
        
        return purchase
    
    def _save_extra_info(self, purchase, receipt_dct):
        """
        Save information about product groups, product type, suppliers etc. 
        """
        extra = receipt_dct['extra']
        store = purchase.pos.store
        chain = store.chain
        
        store_title = extra.get('store_title', '')
        if not store_title and not store.title:
            store.title = store.internal_id
        elif not store.title or store.title == store.internal_id:
            store.title = store_title
        
        store.address = extra.get('store_address', '')
        store.postal_code = extra.get('store_postal_code', '')
        store.city = extra.get('store_city', '')
        store.phone = extra.get('store_phone', '')
        store.save()
        
        for item in receipt_dct['items']:
            extra = item['extra']
            supplier_id = extra.get('supplier_id', '')
            
            product = Product.objects.get(articlenum=item['id'], chain=chain)
            product.product_type = extra.get('saleitem_type', DEFAULT_VALID)
            
            if supplier_id:
                supplier, created = Supplier.objects.create_or_update(
                    idnum=supplier_id, 
                    name=extra.get('supplier_name', '')
                )
                product.supplier = supplier
            product.save()
            
            for pg_dct in extra.get('categories', []):
                pg = self._save_product_group(pg_dct, chain)
                
                if pg:
                    product.product_groups.add(pg)
                    pur_pg, created = PurchasedProductGroup.objects.get_or_create(
                        product_group=pg,
                        purchase=purchase
                    )
                    pur_pg.n_items += item['quantity']
                    pur_pg.final = True
                    pur_pg.save()
            
            stock_quantity = extra.get('stock_quantity', Decimal(0))
            if stock_quantity:
                pis, created = ProductInStore.objects.create_or_update(
                    product=product, store=store, stock_quantity=stock_quantity
                )
                
        return purchase
    
    def _save_product_group(self, pg_dct, chain):
        """ 
        Recursively save ProductGroup and any parents from info in pg_dct.
        
        Returns saved ProductGroup or None.
        """
        idnum = pg_dct.get('idnum', '')
        
        if idnum:
            pg, created = ProductGroup.objects.get_or_create(
                idnum=idnum,
                is_internal=False,
                chain=chain
            )
            title = pg_dct.get('name', '')
            if title:
                pg.title = title
            elif not pg.title:
                pg.title = idnum
            
            pg.parent_group = self._save_product_group(pg_dct.get('parent', {}), chain)
            pg.save()        
            return pg
    

class TwosellSalesBase(models.Model):
    """
    Base model for models that need to implement common interface
    for tracking Twosell sales.
    """
    total_cost = models.DecimalField( _("total cost (incl. VAT)"), default=0, 
            max_digits=60, decimal_places=2,
            help_text=_('Total cost including discounts (incl. VAT)'))
    total_cost_excl_vat = models.DecimalField( _("total cost (excl. VAT)"), default=0, 
            max_digits=60, decimal_places=2, 
            help_text=_('Total cost including discounts (excl. VAT)'))
    direct_gross_incl_vat = models.DecimalField( _('Twosell direct sales (gross, incl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell sales from on-screen suggestions (gross, incl. VAT)'))
    direct_gross_excl_vat = models.DecimalField( _('Twosell direct sales (gross, excl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell sales from on-screen suggestions (gross, excl. VAT)'))
    coupon_gross_incl_vat = models.DecimalField( _('Twosell coupon sales (gross, incl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell coupon sales (gross, incl. VAT)'))
    coupon_gross_excl_vat = models.DecimalField( _('Twosell coupon sales (gross, excl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell coupon sales (gross, excl. VAT)'))
            
    class Meta:
        abstract = True
        app_label = 'twosell'
    
    def twosell_direct(self, incl_vat=False, gross=False):
        if not incl_vat and not gross:
            try:
                return self.direct_net_excl_vat
            except AttributeError:
                raise NotImplementedError('Only gross values available for this model.')
        elif not incl_vat and gross:
            return self.direct_gross_excl_vat
        elif incl_vat and not gross:
            try:
                return self.direct_net_incl_vat
            except AttributeError:
                raise NotImplementedError('Only gross values available for this model.')
        elif incl_vat and gross:
            return self.direct_gross_incl_vat
    
    def twosell_coupon(self, incl_vat=False, gross=False):
        if not incl_vat and not gross:
            try:
                return self.coupon_net_excl_vat
            except AttributeError:
                raise NotImplementedError('Only gross values available for this model.')
        elif not incl_vat and gross:
            return self.coupon_gross_excl_vat
        elif incl_vat and not gross:
            try:
                return self.coupon_net_incl_vat
            except AttributeError:
                raise NotImplementedError('Only gross values available for this model.')
        elif incl_vat and gross:
            return self.coupon_gross_incl_vat
    
    def twosell_total(self, incl_vat=False, gross=False):
        return (self.twosell_direct(incl_vat=incl_vat, gross=gross) +
                self.twosell_coupon(incl_vat=incl_vat, gross=gross))
    
    def total_sales(self, incl_vat=False):
        if incl_vat:
            return self.total_cost
        elif not incl_vat:
            return self.total_cost_excl_vat
    

class Purchase(TwosellSalesBase):
    transactionid = models.CharField(_('transaction id'), max_length=255)
    time_of_purchase = models.DateTimeField(_("time of purchase"))
    time_received = models.DateTimeField(auto_now_add=True, null=True, blank=True)
    final = models.BooleanField(_('final receipt'), default=False, 
            help_text=_('Final or preliminary purchase.'))
    pos = models.ForeignKey('twosell.PointOfSale', verbose_name=_("point of sale"))
    seller = models.ForeignKey('twosell.Seller', null=True, blank=True)
    
    # Sales data
    products = models.ManyToManyField('Product', verbose_name=_('products'), 
            through='PurchasedProduct')
    product_groups = models.ManyToManyField('ProductGroup', blank=True, null=True,
            verbose_name=_('product groups'), through="PurchasedProductGroup")
    n_rows = models.IntegerField(_("number of rows"), default=0,
            help_text=_('Number of rows, one per sale item with unique positemid'))
    gen_discount = models.DecimalField( _("general discount (incl. VAT)"), default=0,
            decimal_places=2, max_digits=60,
            help_text=_('General discount on whole purchase, not including item discounts (incl. VAT)'))
    total_discount = models.DecimalField( _("total discount (incl. VAT)"), default=0, 
            max_digits=60, decimal_places=2,  
            help_text=_('Sum of general purchase discount and item discounts (incl. VAT)'))
    
    # Twosell data
    time_for_twosell = models.IntegerField(blank=True, null=True, 
            help_text=_('Time of purchase difference between preliminary and final receipt.'))
    
    direct_reported_shown = models.BooleanField(_('Offers reported shown'), default=False,
        help_text=_('Direct offers reported shown on screen by PoS'))
    direct_net_incl_vat = models.DecimalField( _('Twosell direct sales (net, incl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell sales from on-screen suggestions (net, incl. VAT)'))
    direct_net_excl_vat = models.DecimalField( _('Twosell direct sales (net, excl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell sales from on-screen suggestions (net, excl. VAT)'))
    coupon_net_incl_vat = models.DecimalField( _('Twosell coupon sales (net, incl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell coupon sales (net, incl. VAT)'))
    coupon_net_excl_vat = models.DecimalField( _('Twosell coupon sales (net, excl. VAT)'), 
            max_digits=60, decimal_places=2, default=0, 
            help_text=_('Twosell coupon sales (net, excl. VAT)'))
    
    objects = PurchaseManager()
    finals = FinalPurchaseManager()
    prels = PrelPurchaseManager()
    
    class Meta:
        unique_together = ('transactionid', 'final')
        ordering = ['-time_of_purchase']
        app_label = 'twosell'
    
    def __unicode__(self):
        if self.final:
            return "%s, final" %(self.transactionid)
        else:
            return "%s, preliminary" %(self.transactionid)
    
    def natural_key(self):
        return self.transactionid
    
    def update_totals(self):
        """
        Update all automatically calculated sale stats.
        """
        self.update_sales_data()
        self.update_twosell_coupon()
        
        # Update twosell direct on final purchase if exists
        if self.final:
            final_purchase = self
        else:
            try:
                final_purchase = Purchase.finals.get(transactionid=self.transactionid)
            except Purchase.DoesNotExist:
                final_purchase = None
        
        if final_purchase:
            final_purchase.update_twosell_direct()
            final_purchase.update_twosell_time()
    
    def update_sales_data(self):
        """
        Update total_discont, total_cost and total_cost_excl_vat.
        
        """
        purprods = self.purchasedproduct_set.all()
        
        self.n_rows = purprods.count()
        row_discounts = purprods.aggregate(Sum('total_discount')).values()[0] or Decimal(0)
        self.total_discount = self.gen_discount + row_discounts
        
        total_row_cost = (purprods.aggregate(Sum('total_cost')).values()[0] or Decimal(0))
        total_row_cost_excl_vat = (purprods.aggregate(Sum('total_cost_excl_vat')).values()[0] or Decimal(0))
        self.total_cost = total_row_cost - self.gen_discount
        
        try:
            gen_discount_rate = self.gen_discount / total_row_cost
        except (ZeroDivisionError, InvalidOperation):
            gen_discount_rate = Decimal(0)
            
        self.total_cost_excl_vat = total_row_cost_excl_vat * (Decimal(1) - gen_discount_rate)
    
    def update_twosell_coupon(self):
        """
        Update Twosell coupon sale stats for purchase.
        
        Aggregates values for Twosell coupon sales.
        
        """
        purprods = self.purchasedproduct_set.all()
        self.coupon_gross_incl_vat = purprods.aggregate(Sum('coupon_gross_incl_vat')).values()[0] or Decimal(0)
        self.coupon_gross_excl_vat = purprods.aggregate(Sum('coupon_gross_excl_vat')).values()[0] or Decimal(0)
        self.coupon_net_incl_vat = self.coupon_gross_incl_vat
        self.coupon_net_excl_vat = self.coupon_gross_excl_vat
    
    def update_twosell_time(self):
        """
        Calculate time difference between final and preliminary purchase.
        
        Only applies to Purchase that is final.
        
        """
        if not self.final:
            return
        
        try:
            prel_purchase = Purchase.objects.get(
                transactionid=self.transactionid, 
                final=False
            )
        except Purchase.DoesNotExist:
            return
        
        if not self.time_of_purchase:
            delta = self.time_of_purchase - prel_purchase.time_of_purchase
            self.time_for_twosell = delta.seconds
    
    def update_twosell_direct(self):
        """ 
        Update Twosell direct sale stats for purchase.
        
        Only applies to Purchase that is final. 
        
        Aggregate values of Twosell direct sales.
        
        """ 
        # Bail early if this is a preliminary receipt
        if not self.final:
            raise CalculationException('Cannot calculate Twosell direct on a preliminary purchase.')
        
        purprods = self.purchasedproduct_set.all()
        
        # Bail if both prel and final does not exist
        try:
            prel = Purchase.prels.get(transactionid=self.transactionid)
        except Purchase.DoesNotExist:
            purprods.update(direct_gross_incl_vat=0, direct_gross_excl_vat=0)
            self.direct_gross_incl_vat = purprods.aggregate(Sum('direct_gross_incl_vat')).values()[0] or Decimal(0)
            self.direct_gross_excl_vat = purprods.aggregate(Sum('direct_gross_excl_vat')).values()[0] or Decimal(0)
            return
        
        # Check conditions for qualifying purchase as Twosell generated:
        # - Final must have total cost greater or equal to total cost for Prel
        # - No products should have been removed from Final compared to Prel
        # - Must have received a status report that indicates Twosell screen was shown.
        #
        # Only update Twosell direct for each row if all conditions are met
        if prel.total_cost >= self.total_cost or self.removed_products() or not self.direct_reported_shown:
            purprods.update(direct_gross_incl_vat=0, direct_gross_excl_vat=0)
        else:
            for purprod in purprods:
                purprod.update_twosell_direct(count_extra=settings.COUNT_EXTRA_AS_TWOSELL)
                purprod.save()
        
        # Update Twosell totals on purchase
        self.direct_gross_incl_vat = purprods.aggregate(Sum('direct_gross_incl_vat')).values()[0] or Decimal(0)
        self.direct_gross_excl_vat = purprods.aggregate(Sum('direct_gross_excl_vat')).values()[0] or Decimal(0)
        
        # Handle limits to Twosell contribution
        try:
            max_twosell_direct = TwosellConf.objects.latest().max_twosell_direct
        except TwosellConf.DoesNotExist:
            max_twosell_direct = 1000
            
        if self.direct_gross_incl_vat > max_twosell_direct:
            self.direct_net_incl_vat = max_twosell_direct
        else:
            self.direct_net_incl_vat = self.direct_gross_incl_vat
        
        # Original vat_rate
        try:
            vat_rate = 100 * (self.direct_gross_incl_vat - self.direct_gross_excl_vat) / self.direct_gross_excl_vat
        except (ZeroDivisionError, InvalidOperation):
            vat_rate = 0
        
        self.direct_net_excl_vat = calc_excl_vat(vat_rate, self.direct_net_incl_vat)
    
    def removed_products(self):
        """
        Return True if products have been removed between this (final) Purchase,
        and preliminary Purchase with same transactionid.
        """
        if not self.final:
            return False
        
        try:
            prel = Purchase.prels.get(transactionid=self.transactionid)
        except Purchase.DoesNotExist:
            return False
        
        final_products = self.products.all()
        
        for prod in prel.products.all():
            if prod not in final_products:
                return True
            
        return False
    
    def original_transactionid(self):
        return self.transactionid[len(self.pos.store.internal_id) + 1:-(len(self.seller.idnum) + 1)]

class PurchasedProductManager(models.Manager):
    def find(self, **kwargs):
        start_dt, end_dt = full_day_datetimes(kwargs.pop('start_date', None), 
            kwargs.pop('end_date', None))
        location = kwargs.pop('location', None)
        products = kwargs.pop('products', None)
        filters = []
        
        if isinstance(location, query.QuerySet):
            location_model = getattr(location, 'model', None)
            
            if location_model == Chain:
                filters.append(Q(purchase__pos__store__chain__in=location))
            elif location_model == Store:
                filters.append(Q(purchase__pos__store__in=location))
            elif location_model == PointOfSale:
                filters.append(Q(purchase__pos__in=location))
            elif location_model == Seller:
                filters.append(Q(purchase__seller__in=location))
        elif location:
            location_model = location.__class__
            
            if location_model == Chain:
                filters.append(Q(purchase__pos__store__chain=location))
            elif location_model == Store:
                filters.append(Q(purchase__pos__store=location))
            elif location_model == PointOfSale:
                filters.append(Q(purchase__pos=location))
            elif location_model == Seller:
                filters.append(Q(purchase__seller=location))
        
        if isinstance(products, query.QuerySet):
            filters.append(Q(product__in=products))
        elif products:
            filters.append(Q(product=products))
        
        if start_dt:
            filters.append(Q(purchase__time_of_purchase__gte=start_dt))
        
        if end_dt:
            filters.append(Q(purchase__time_of_purchase__lte=end_dt))
        
        if filters:
            return self.get_query_set().filter(*filters)
        
        return self.get_query_set()
    

class PrelPurchasedProductManager(PurchasedProductManager, TwosellSalesManagerMixin):
    def get_query_set(self):
        return super(PrelPurchasedProductManager, self).get_query_set().filter(final=False)
    

class FinalPurchasedProductManager(PurchasedProductManager, TwosellSalesManagerMixin):
    def get_query_set(self):
        return super(FinalPurchasedProductManager, self).get_query_set().filter(final=True)
    

class PurchasedProduct(TwosellSalesBase):
    # Purchase data
    product = models.ForeignKey('twosell.Product', verbose_name=_('product'))
    purchase = models.ForeignKey('twosell.Purchase', verbose_name=_('purchase'))
    final = models.BooleanField(default=False)
    n_items = models.DecimalField( _("number of items"), default=0,
            max_digits=60, decimal_places=2)
    total_discount = models.DecimalField(_("total discount (incl. VAT)"), max_digits=60, 
            decimal_places=2, default=0, help_text=_('Total row discount (incl. VAT)'))
    
    objects = models.Manager()
    finals = FinalPurchasedProductManager()
    prels = PrelPurchasedProductManager()
    
    class Meta:
        verbose_name = _('purchased product')
        verbose_name_plural = _('purchased products')
        app_label = 'twosell'
    
    def __unicode__(self):
        return self.product.title + " " + unicode(self.n_items)
    
    def save(self, *args, **kwargs):
        self._update_excl_vat()
        super(PurchasedProduct, self).save(*args, **kwargs)
    
    def _update_excl_vat(self):
        """Update excl_vat fields."""
        vat_rate = self.product.vat_rate
        self.total_cost_excl_vat = calc_excl_vat(vat_rate, self.total_cost)
        self.direct_gross_excl_vat = calc_excl_vat(vat_rate, self.direct_gross_incl_vat)
        self.coupon_gross_excl_vat = calc_excl_vat(vat_rate, self.coupon_gross_incl_vat)
    
    def update_twosell_direct(self, count_extra=True):
        """
        Update twosell direct sales of this instance based on diff between final 
        and preliminary purchase of this product.
        """
        if not self.final:
            raise CalculationException('Cannot calculate Twosell contribution on a preliminary purchase.')
        
        # Reset twosell_direct because this might be a recalculation
        self.direct_gross_incl_vat = 0
        
        # Only count positive sales as Twosell.
        if self.total_cost < 0:
            self.direct_gross_incl_vat = 0
            return
        
        # Do not calculate if no preliminary purchase to compare to.
        try:
            prel_purchase = Purchase.objects.get(final=False,
                transactionid=self.purchase.transactionid
            )
        except Purchase.DoesNotExist:
            self.direct_gross_incl_vat = 0
            return
        
        # Do not register any Twosell sales if nothing sold on preliminary receipt.
        # If parent purchase already registered as coupon purchase, 
        # count any extra sales as coupon purchase as well.
        # FIXME (dash 2011-04-07): Should be self.coupon_gross_incl_vat?
        if prel_purchase.total_cost == 0 or prel_purchase.coupon_gross_incl_vat:
            self.direct_gross_incl_vat = 0
            return
        
        try:
            prel = PurchasedProduct.objects.get(product=self.product, purchase=prel_purchase)
        except PurchasedProduct.DoesNotExist:
            # Additional sale of different product
            self.direct_gross_incl_vat = self.total_cost
        else:
            if count_extra:
                # Possibly more sold of same product
                item_incr = self.n_items - prel.n_items
                if item_incr > 0:
                    try:
                        final_cost_per_item = self.total_cost / self.n_items
                    except ZeroDivisionError:
                        pass
                    else:
                        self.direct_gross_incl_vat = item_incr * final_cost_per_item
            
                simple_diff = self.total_cost - prel.total_cost
                if  simple_diff > 0 and simple_diff < self.direct_gross_incl_vat:
                    self.direct_gross_incl_vat = simple_diff
        
        self._update_excl_vat()

class PurchasedProductGroup(models.Model):
    product_group = models.ForeignKey('twosell.ProductGroup', verbose_name=_('product group'))
    purchase = models.ForeignKey('twosell.Purchase', verbose_name=_('purchase'))
    final = models.BooleanField(default=False)
    n_items = models.IntegerField(_('number of items purchased'), default=0)
    
    objects = models.Manager()
    finals = FinalsManager()
    prels = PrelsManager()
    
    class Meta:
        verbose_name = _('purchased product groups')
        verbose_name_plural = _('purchased product groups')
        app_label = 'twosell'
    
    def __unicode__(self):
        return u"%s: %s" %(self.product_group.idnum, self.purchase.transactionid)
    


###################
# Recommendations #
###################
STATUS_CODES = [
    ('100', _('On screen')),                # shown on screen
    ('200', _('On screen, Declined')),      # offered, customer not interested
    ('300', _('On screen, Bad suggestion')),# cashier thinks suggesion is bad
    ('400', _('Not available in store')),   # set on pos backend
    ('410', _('Discontinued product')),     # set on pos backend
    ('500', _('Printed on coupon')),        # offers printed on coupon 
    ('600', _('No action')),                # default if no other status i set.
]

class OfferException(Exception):
    pass


class OfferManager(models.Manager):
    def get_by_natural_key(self, ean):
        return self.get(ean=ean)
    
    def use_code(self, twosell_id, purchase):
        """
        Use Offer with ean ``twosell_id`` with Purchase ``purchase``.
        
        Returns Offer used.
        """
        try:
            offer = self.get(ean=twosell_id)
        except self.model.DoesNotExist:
            offer = None
        else:
            offer.cashin_purchase = purchase
            offer.save()
        return offer
    
    def update_status(self, status_dct):
        """ 
        Save status for offers in status_dct. 
        
        ``status_dct`` should look as follows:
        
        {
            'transactionID': 'TEST1234567890'
            'status': None, 
            'items': [
                {'status': '100', 'ean': '0048552'}, 
                {'status': '400', 'ean': '1705115'}, 
                {'status': '400', 'ean': '4628055'}, 
                {'status': '100', 'ean': '2095769'}, 
                {'status': '410', 'ean': '4476911'}, 
                {'status': '400', 'ean': '7052677'}, 
            ], 
        }
        """
        transactionid = status_dct.get('transactionID')
        items = status_dct.get('items')
        
        # No items to update status for, so return as ok
        if not items:
            return True
        
        try:
            prel_purchase = Purchase.objects.get(
                transactionid=transactionid, 
                final=False
            )
        except Purchase.DoesNotExist:
            logger.error("Prel purchase does not exist. Transactionid: %s" % transactionid)
            return False
        
        try:
            final_purchase = Purchase.objects.get(
                transactionid=transactionid, 
                final=True
            )
        except Purchase.DoesNotExist:
            logger.error("Final purchase does not exist. Transactionid: %s" % transactionid)
            return False
            
        store = final_purchase.pos.store
        
        screen_opened = status_dct.get('screen_opened', None)
        screen_closed = status_dct.get('screen_closed', None)
        
        if screen_opened and screen_closed:
            final_purchase.time_for_twosell = (screen_closed - screen_opened).seconds
            final_purchase.save()
        
        screen_shown = False
        for item in items:
            try:
                offer = Offer.objects.get(ean=item['ean'])
            except Offer.DoesNotExist:
                logger.error("Offerparse failed %s: %s" % (status_dct['transactionID'], repr(item)))
                continue
        
            # Status
            if item['status'] == '210':
                offer.status = '100' # To handle old 1.0 codes.
            else:
                offer.status = item['status']
        
            # Additional info
            if offer.status == '500':
                offer.save()
                continue
        
            offer.cashin_purchase = final_purchase
            if offer.status in ('100', '200', '300'):
                offer.shown_on_screen = True
                screen_shown = True
            offer.save()
        
            # Update stock info
            if offer.product:
                try:
                    product_in_store = ProductInStore.objects.get(
                        product=offer.product,
                        store=store
                    )
                except ProductInStore.DoesNotExist:
                    # Create placeholder product for this store for product 
                    # not yet added to db from being present on a receipt.
                    product_in_store = ProductInStore(
                        product=offer.product,
                        store=store,
                        price=offer.product.max_price(),
                        placeholder=True
                    )
                    product_in_store.save()
                
                if offer.status == '400':
                    product_in_store.last_not_in_stock = final_purchase.time_of_purchase
                    product_in_store.active = False
                elif offer.status == '410':
                    product_in_store.last_discontinued = final_purchase.time_of_purchase
                    product_in_store.active = False
                else:
                    product_in_store.active = True
                product_in_store.save()
        
        prel_purchase.direct_reported_shown = screen_shown
        prel_purchase.update_totals()
        prel_purchase.save()
        
        final_purchase.direct_reported_shown = screen_shown
        final_purchase.update_totals()
        final_purchase.save()
        
        return True
    
    def active_coupons(self, start_date, end_date):
        """
        Number of coupons that have been active, i.e. possible to use,
        during period from start_date to end_date (both inclusive).
        """
        start_dt, end_dt = full_day_datetimes(start_date, end_date)
        return self.filter(
            status='500', # actually printed
            expiration_date__gte=start_date,
            generating_purchase__time_of_purchase__lte=end_dt
        ).count()
    
    def save_offers(self, offer_data):
        """
        Saves Offers to db based on dct from json serialized data
        with natural keys.
        """
        offers = []
        
        for o in offer_data:
            o = o['fields']
            
            try:
                offer = self.get(ean=o['ean'])
            except self.model.DoesNotExist:
                offer = self.model(ean=o['ean'])
            
            try:
                offer.generating_purchase = Purchase.prels.get(
                    transactionid=o['generating_purchase']
                )
            except Purchase.DoesNotExist:
                raise OfferException('No purchase to connect offer to.')
            
            offer.rank = o['rank']
            offer.direct_offer = o['direct_offer']
            offer.type_of_discount = o['type_of_discount']
            offer.percentage_discount = Decimal(o['percentage_discount'])
            offer.fixed_discount = o['fixed_discount']
            offer.expiration_date = datetime.datetime.strptime(o['expiration_date'], "%Y-%m-%d").date()
            offer.title = o.get('title', '')
            
            if o['product']:
                try:
                    offer.product = Product.objects.get(articlenum=o['product'])
                except Product.DoesNotExist:
                    # Product that might have been added from push list in calc
                    try:
                        calc_product = Product.objects.using('calc').get(articlenum=o['product'])
                        calc_product.pk = None
                        calc_product.save(using='canonical')
                        offer.product = Product.objects.get(articlenum=o['product'])
                        
                        ProductInStore.objects.get_or_create(product=offer.product,
                            store=offer.generating_purchase.pos.store, placeholder=True
                        )
                    except Product.DoesNotExist:
                        raise OfferException('Product %s does not exist.' %(o['product']))
            
            offer.save()
            offers.append(offer)
        
        return offers
    

class Offer(models.Model):
    # Generated data
    ean = models.CharField(_('EAN'), max_length=25, blank=True, db_index=True)
    legacy_ean = models.CharField(_('Legacy EAN'), max_length=25, blank=True)
    direct_offer = models.BooleanField(_("direct offer?"))
    product = models.ForeignKey('twosell.Product', verbose_name=_('product'), blank=True, null=True)
    title = models.CharField(max_length=750, null=True)
    product_group = models.ForeignKey('twosell.ProductGroup', verbose_name=_('product group'), 
            blank=True, null=True) # deprecated, but still kept for compatibility with old transactionbundles
    rank = models.PositiveIntegerField(_("prio"), default=0)
    description = models.TextField(blank=True)
    
    # Discount details
    type_of_discount = models.CharField(_('type of discount, percent or fixed'), 
            max_length=1, choices=DISCOUNT_TYPES, default='n')
    percentage_discount = models.DecimalField(_("percentage discount"), max_digits=2, 
            decimal_places=0, default=0)
    fixed_discount = models.PositiveIntegerField(_("fixed discount"), default=0)
    expiration_date = models.DateField(_("expiration_date"))
    
    # Tracking data
    # cashin_purchase is set when offers are used, or when offers are rejected by backend or at pos
    # status is set once, e.g. 500 does not become 100 when coupon is used
    generating_purchase = models.ForeignKey('twosell.Purchase', verbose_name=_("generating purchase"), 
            related_name='generating_purchase_offer_set')
    cashin_purchase = models.ForeignKey('twosell.Purchase', verbose_name=_("cash in purchase"), 
            blank=True, null=True, related_name='cashin_purchase_offer_set')
    shown_on_screen = models.BooleanField(_("presented on TWOSELL screen at POS?"), 
            default=False)
    status = models.CharField(_("status reported by POS"), null=True, blank=True, 
            choices=STATUS_CODES, max_length=10)
    
    # TODO (dash 2011-01-20): Remove, track all sales data on Purchase, PurchasedProduct
    actual_discount = models.DecimalField( _("actual discount"), max_digits=60, 
            decimal_places=2, default=0, null=True, blank=True)
    
    # Manager
    objects = OfferManager()
    
    class Meta:
        app_label = 'twosell'
    
    def __unicode__(self):
        return self.ean
        
    def natural_key(self):
        return self.ean
    
