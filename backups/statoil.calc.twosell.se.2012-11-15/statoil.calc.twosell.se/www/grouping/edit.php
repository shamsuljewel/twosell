
<?php

include("menue.php");

function __autoload($className) {
//require_once dirname(__FILE__)."/models/{$className}.php";
        require_once dirname(__FILE__) . "/baseClasses/{$className}.php";
    }

    $database = new DatabaseUtility();

$meta_group_id=$_REQUEST['meta_group_id'];
$sql="SELECT * FROM tsln_meta_groups WHERE meta_group_id = $meta_group_id";

$res=mysql_query($sql); $row=mysql_fetch_array($res);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>
       TWOSELL: Editera grupp <?php echo $meta_group_id; ?>
</title>
</head>
<body>

<div style="width:1200px; float:left; background:#F8F8FF;">


<form method="post" action="save.php"> 

<div style="float:left;">
<input type="hidden" name="meta_group_id" value="<?php print
utf8_decode($row['meta_group_id']);?>"> <input type="hidden" name="" value="<?php print date('Y-m-d h:i:s');?>">
Grupp ID: <b><?php print $row['meta_group_id'];?></b>

<input type="submit" VALUE="Spara ändringar" title="Detta sparar bara ändringarna men räknar inte om gruppen, alla grupper räknas om
nattetid"> <input type="submit" VALUE="Räkna om gruppens produktlista" title="Detta kan ta lite tid beroende av hur avancerad
gruppen är, när du räktnat om en grupp syns normalt först resultaten dagen efter i TWOSELL förslag i butikerna"> <INPUT
TYPE="BUTTON" VALUE="Visa grupp utan att räkna om"
ONCLICK="window.location.href='list_members_in_group.php?meta_group_id=<?php print $meta_group_id;?>'">
</div>

<div style="float:right; background:#FFFFFF;">

<div>Antal produkter i gruppen: <div style="float:right; background:#FFFFFF;"><?php print $row['number_of_members'];?></div></div>
<div>Senast ändrad: <div style="float:right; background:#FFFFFF;"><?php print $row['change_time'];?></div></div>
</div>

<!--<input
type="submit" VALUE="Visa grupp utan att räkna om" title="Ändringar i formulret kommer inte med påverka gruppens produkter,
välj 'räkna om' för att se den nya listan">	--> <br>

<div style="border-top-width: 3px; border-top-style: solid; border-top-color: grey;  background:#FFF5EE; border-top-color:white;" >
<div style="float:left; width:100px;">Grupp namn: </div><div><input size="80" type="text" name="group_name" value="<?php print utf8_encode($row['group_name']);?>"></div>
</div>

<div style="border-top-width: 3px; border-top-style: solid; border-top-color: grey;  background:#FFF5EE; border-top-color:white;" >
<div style="float:left; width:100px;">Förklaring: </div><div><input size="80" type="text" name="group_name_long" value="<?php print
utf8_encode($row['group_name_long']);?>">
</div>
</div>


<div style="border-top-width: 3px; border-top-style: solid; border-top-color: grey;  background:#FFF5EE; border-top-color:white;" >
<span title="The text info will be shown in the kassa after a product description, for example 'Buy three and pay two'"><div style="float:left; width:100px;">Description2:</div></span><div> <textarea name="description2" rows="1" cols="100"><?php print
utf8_encode($row['description2']);?></textarea></div>
</div>

<div style="border-top-width: 3px; border-top-style: solid; border-top-color: grey;  background:#E6E6FA; border-top-color:white;" >
<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. 
Separeras strängar med ';'. Om du vill att två stängar är krävda, exv kyl&frys så sammanfoga dem med ett '&' tecken (får endast förekomma en gång i nuvarande version),
ordningen de förekommer i spelar ingen roll. I nuvarande version kan man bara ha ett kombinerat & vilkor och då får man inte
använda ; " style="border-bottom:1px dashed;">Krävda textsträngar 1 : (får ej vara tom, endast ; eller ett &)<br> If you want to use screen_text then use G# at the beginning of the text and finished with another G at the end of the text i.e. G#901-17G</span><br>
<textarea name="keyword_include_1" rows="<?php  echo intval(strlen($row['keyword_include_1'])/120);?>" cols="150">
<?php print utf8_encode($row['keyword_include_1']);?>
</textarea><br>
<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten plockas bort även om de nyss angivna
strängarna förekom."
style="border-bottom:1px dashed;">Ej tillåtna textsträngar:</span> <br> <textarea name="keyword_exclude_1" rows="<?php  echo intval(strlen($row['keyword_exclude_1'])/120)+1;?>"
cols="150"><?php print utf8_encode($row['keyword_exclude_1']);?></textarea><br> Kommentar:<br> <textarea
name="keyword_comment_1" rows="1" cols="150"><?php print utf8_encode($row['keyword_comment_1']);?></textarea>
</div>


<div style="border-top-width: 3px; border-top-style: solid; border-top-color: grey;  background:#E6E6FA; border-top-color:white;" >
<span title="Även dessa produkter inkluderas i grupper. Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. Separeras strängar med ';'. Om du vill att två eller fler stängar måste finnas i namnet så sammanfoga dem med ett '&'
tecken (får endast förekomma en gång i nuvarande version), ordningen de förekommer i spelar ingen roll" style="border-bottom:1px dashed;">Krävda textsträngar 2:(får ej vara tom, endast ; eller ett &)<br> If you want to use screen_text then use G# at the beginning of the text and finished with another G at the end of the text i.e. G#901-17G</span><br>
<textarea name="keyword_include_2" rows="<?php  echo intval(strlen(utf8_encode($row['keyword_include_2']))/120)+1;?>" cols="140"><?php print utf8_encode($row['keyword_include_2']);?></textarea><br>
<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten plockas bort även om de nyss angivna
strängarna förekom (ej klart: ## betyder att det är ett fristående ord som inte får förekomma med ett tecken efter ' ',-*/+ etc.)"
style="border-bottom:1px dashed;">Ej tillåtna textsträngar:</span> <br> <textarea name="keyword_exclude_2" rows="<?php  echo intval(strlen(utf8_encode($row['keyword_exclude_2']))/120)+1;?>"
cols="140"><?php print utf8_encode($row['keyword_exclude_2']);?></textarea><br> Kommentar:<br> <textarea
name="keyword_comment_2" rows="1" cols="140"><?php print utf8_encode($row['keyword_comment_2']);?></textarea><br><br>
</div>

<div style="border-top-width: 3px; border-top-style: solid; border-top-color:white;  background:#E6E6FA; " >
<span title="Även dessa produkter inkluderas i grupper. Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. Separeras strängar med ';'. Om du vill att två eller fler stängar måste finnas i namnet så sammanfoga dem med ett '&'
tecken (får endast förekomma en gång i nuvarande version), ordningen de förekommer i spelar ingen roll" style="border-bottom:1px dashed;">Krävda textsträngar 3: (får ej vara tom, endast ; eller ett &)<br> If you want to use screen_text then use G# at the beginning of the text and finished with another G at the end of the text i.e. G#901-17G</span><br>
<textarea name="keyword_include_3" rows="<?php  echo intval(strlen(utf8_encode($row['keyword_include_3']))/120)+1;?>" cols="140"><?php print utf8_encode($row['keyword_include_3']);?></textarea><br>
<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten plockas bort även om de nyss angivna
strängarna förekom (ej klart: ## betyder att det är ett fristående ord som inte får förekomma med ett tecken efter ' ',-*/+ etc.)"
style="border-bottom:1px dashed;">Ej tillåtna textsträngar:</span> <br> <textarea name="keyword_exclude_3" rows="<?php  echo intval(strlen(utf8_encode($row['keyword_exclude_3']))/120)+1;?>"
cols="140"><?php print utf8_encode($row['keyword_exclude_3']);?></textarea><br> Kommentar:<br> <textarea
name="keyword_comment_3" rows="1" cols="140"><?php print utf8_encode($row['keyword_comment_3']);?></textarea><br><br>
</div>

<div style="border-top-width: 3px; border-top-style: solid; border-top-color:white;  background:#F8F880;" >
<input type="checkbox" name="if_sold_ingroup" value="1" <?php  if ($row['if_sold_ingroup']=='1') echo 'CHECKED'; ?> ><span title="Under konstruktion">
<?php print utf8_encode('TEXT N�R VARA S�LD I DENNA GRUPP');?></span><br>
<span title="Undvik använda denna funktion. Endast delvis införd. Exempelvis om alla produkter som börjar med DSK ovilkorligt ska ingå i denna gruppen skriv DSK*. *? tillåtna, 
max 3, separerade med ," style="border-bottom:1px dashed;"><input
size="160" type="text" name="if_sold_ingroup_text" value="<?php print utf8_encode($row['if_sold_ingroup_text']);?>"><br> 
</div>


<div style="border-top-width: 3px; border-top-style: solid; border-top-color:white;  background:#F8F880;" >
<input type="checkbox" name="if_group_selected" value="1" <?php  if ($row['if_group_selected']=='1') echo 'CHECKED'; ?> ><span title="Under konstruktion. Denna text visas både om gruppen föreslagits manuellt eller av TWOSELL algoritmen.
Antal tecken som kan skrivas är POS specifikt, se TWOSELL Wiki. På plats 2 och 3 på TWOSELL skärmen kommer förslag som.">
<?php print utf8_encode('TEXT N�R GRUPPEN F�RESL�S');?> </span><br>
<span title="Undvik använda denna funktion. Endast delvis införd. Exempelvis om alla produkter som börjar med DSK ovilkorligt ska ingå i denna gruppen skriv DSK*. *? tillåtna, 
max 3, separerade med ," style="border-bottom:1px dashed;"><input
size="160" type="text" name="if_group_selected_text" value="<?php print utf8_encode($row['if_group_selected_text']);?>"><br> 
</div>    
    
<div style="border-top-width: 2px; border-top-style: solid; border-top-color:white; background:#F8F880;" >
<b><span title="Hämta förslag från följande grupper. När en produkt i denna grupp säljs så hämtar TWOSELL förslag för produkten från dessa grupper, 
separera med ; (inget semikolon i slutet)" style="border-bottom:1px dashed;">Manuella gruppförslag för denna grupper:</span></b>
<input size="50" type="text" name="group_relation_top_manual" value="<?php print $row['group_relation_top_manual'];?>">
    | <input type="checkbox" name="group_relation_manual_ok" value="1" <?php  if ($row['group_relation_manual_ok']=='1') echo 'CHECKED'; ?> > don't check if TWOSELL should ignore manual suggestions
    <br>
</div>


<div style="border-top-width: 2px; border-top-style: solid; border-top-color:white; background:#F8F880;" >
<span title="Produkter med lägre pris kommer ej tas med i denna grupp" style="border-bottom:1px dashed;">Minsta tillåtna pris:
</span> <input size="7" type="text" name="price_min" value="<?php if ($meta_group_id > 0) { print $row['price_min']; } else { print "0";} ?>"> <span title="Produkter med
högre pris kommer ej tas med i denna grupp, om det är 0 så finns inget maxpris" style="border-bottom:1px dashed;">Högsta
tillåtna pris: </span> <input size="7" type="text" name="price_max" value="<?php  if ($meta_group_id > 0) { print $row['price_max']; } else { print "500000";} ?>">
</div>

<br>
<span title="Undvik använda denna funktion. Endast delvis införd. Exempelvis om alla produkter som börjar med DSK ovilkorligt ska ingå i denna gruppen skriv DSK*. *? tillåtna, 
max 3, separerade med ," style="border-bottom:1px dashed;">Skriv artikelnummer som ska ingå i grupp:</span> <input
size="30" type="text" name="change_time" value="<?php print $row['articelnumber_serie_include'];?>"><br> 

<?php 
// <span title="Här kan
// du styra från vilka artikelnummer medlemmarna ska hämtas. *? tillåtna,  max 3, separerade med ," style="border-bottom:1px
// dashed;">Skriv artikelnumer som medlemmar ska hämtas från:</span> <input size="40" type="text"
//name="articelnumber_serie_use_as_base" value="<?php print $row['articelnumber_serie_use_as_base'];
// ?xxxxxxx>">
// <br><br>

?>

<span title="lista TWOSELL id för dessa produkter, separerat med komma ','" style="border-bottom:1px dashed;">Uteslut dessa
Produkter (produkt ID) från gruppen (separera med ;) :</span> <input size="260" type="text" name="exclude_prod_items" value="<?php print
$row['exclude_prod_items'];?>"><br> <span title="Dessa produkter kommer inkludera i gruppen under alla omständigheter. Lista
TWOSELL Produkt ID för dessa produkter, separerat med komma ';'" style="border-bottom:1px dashed;">Inkludera dessa produkter i gruppen
(separera med ;)  :</span> <input size="260" type="text" name="include_prod_items" value="<?php print
$row['include_prod_items'];?>"><br><br>


<span title="Produkter i denna grupp blockeras som förslag utanför angivna säsonger" style="border-bottom:1px dashed;">Säsong </span>
1 startmånad: <input size="7" type="text" name="start_month_season_1" value="<?php print $row['start_month_season_1'];?>">
startdag: <input size="7" type="text" name="start_day_season_1" value="<?php print $row['start_day_season_1'];?>"> slutmånad:
<input size="7" type="text" name="stop_month_season_1" value="<?php print $row['stop_month_season_1'];?>"> sista dagen: <input
size="7" type="text" name="stop_day_season_1" value="<?php print $row['stop_day_season_1'];?>"> <br>Säsong 2 startmånad:
<input size="7" type="text" name="start_month_season_2" value="<?php print $row['start_month_season_2'];?>"> startdag: <input
size="7" type="text" name="start_day_season_2" value="<?php print $row['start_day_season_2'];?>"> slutmånad: <input size="7"
type="text" name="stop_month_season_2" value="<?php print $row['stop_month_season_2'];?>"> sista dagen: <input size="7"
type="text" name="stop_day_season_2" value="<?php print $row['stop_day_season_2'];?>"><br>

<br> <input type="checkbox" name="never_trigger" value="<?php print $row['never_trigger'];?>"> Gruppens varor får aldrig
trigga TWOSELL förslag <br> <input type="checkbox" name="group_self_refer" value="<?php print $row['group_self_refer'];?>">
Gruppen får hämta förslag från gruppen själv <br> <input type="checkbox" name="override_calculted_groups" value="<?php print
$row['override_calculted_groups'];?>"><span title="Om det finns manuella förslag kommer producter alltid väljas från dessa"
style="border-bottom:1px dashed;"> Använd endast manuella grupper</span>

<div style="border-top-width: 2px; border-top-style: solid; border-top-color:white; background:#F8F880;" >
<span title="Lista Buikernas ID nummer för alla butiek där gruppen ska vara blockerad, skriv '*' om gruppen ska 
blockeras i alla butiker" style="border-bottom:1px dashed;">Gruppen spärrad i följande butiker:</span> <input size="50"
type="text" name="never_suggest" value="<?php print $row['never_suggest'];?>"></div>

<span title="Om en annan vara föreslår
denna grupp och det inte finns en matchande product, används dessa produkter." style="border-bottom:1px dashed;">Reservförslag
om inga andra förslag finns:</span> <input size="40" type="text" name="manual_suggestions" value="<?php print
$row['manual_suggestions'];?>">

<span title="Denna grupp får aldrig hämta förslag från följade grupper" style="border-bottom:1px dashed;">Spärrade
relationsgruppen:</span> <input size="50" type="text" name="group_relation_block_list" value="<?php print
$row['group_relation_block_list'];?>"><br>

<span title="Hur många förslag får hämtas från denna grupp och föreslås på TSL-skärmen?" style="border-bottom:1px dashed;">
Hur många förslag får hämtas max från gruppen:</span> <input size="1" type="text" name="max_suggestion" value="<?php if ($meta_group_id > 0) { print $row['max_suggestion']; } else { print "1";} ?>">.  


Hur många grupper av grupperna får förslag hämtas från: <input size="1" type="text" name="group_use_top_nr" value="<?php if ($meta_group_id > 0) { print $row['group_use_top_nr']; } else { print "3";} ?>"><br>


<input type="checkbox" name="override_calculted_groups" value="<?php print $row['override_calculted_groups'];?>"><span
title="(Ej implementerad) Är detta en metagrupp som inehåller andra grupper? Om metagrupp så kan man inte ha krävda
textsträngar etc i metagruppen. Listan innehåller de grupper som ingår. Alla andra inställningar överrider de inkluderade
grupperna och grupperna behandlas som en grupp" style="border-bottom:1px dashed;"> Metagrupp. Följade grupper ingår i denna
grupp gruppen:</span> <input size="35" type="text" name="included_groups" value="<?php print
$row['included_groups'];?>"><br><br>

Tidpunkt för senaste omräkning <?php print $row['latest_calculation_dattime'];?><br> 

Mest relaterade grupper: <?php print $row['group_relation_top_calculated'];?><br>

<span title="Detta blir listor som öppnas i nytt fönster, lämpligt för exjobbare, projektarbete" style="border-bottom:1px
dashed;">Mest sålda varor i denna gruppen senaste:  </span>

<input type="submit"  VALUE="7 dagar" title="Öpnas i ny flik"> 	<input type="submit"  VALUE="30 dagar" title="Öpnas i ny
flik"> <input type="submit" VALUE="400 dagar" title="Öpnas i ny flik"> <br> <br>

<input type="submit"  VALUE="Spara ändringar" title="Detta sparar bara ändringarna men räknar inte om gruppen, alla grupper
räknas om nattetid">

<input type="submit" VALUE="spara och räkna om grupp" title="Detta kan ta lite tid beroende av hur avancerad gruppen är, när
du räktnat om en grupp syns normalt först resultaten dagen efter i TWOSELL förslag i butikerna">

<input type="submit" VALUE="Visa grupp utan att räkna om" title="Ändringar i formulret kommer inte med påverka gruppens
produkter, välj 'räkna om' för att se den nya listan"> </form>

<div style="background-image: url(twosell_line_760.png);  height:38px"> <img src="twosellbypocada.png" style="float:right">
</div>


</div>


