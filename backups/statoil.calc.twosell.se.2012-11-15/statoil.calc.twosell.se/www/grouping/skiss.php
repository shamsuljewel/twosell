<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
  <title> TWOSELL Gruppadmin</title>
 </head>

 <body>

<?php
$meta_group_id=$_REQUEST['meta_group_id'];
include('conn.php');

$res=mysql_query($sql);
$row=mysql_fetch_array($res);

?>

<form method="post" action="save.php">
<input type="submit"  VALUE="Spara ändringar" title="Detta sparar bara ändringarna men räknar inte om gruppen, alla grupper räknas om nattetid"> 	
<input type="submit" VALUE="Räkna om gruppens produktlista" title="Detta kan ta lite tid beroende av hur avancerad gruppen är, när du räktnat om en grupp syns normalt först resultaten dagen efter i TWOSELL förslag i butikerna">
<input type="submit" VALUE="Visa grupp utan att räkna om" title="Ändringar i formulret kommer inte med påverka gruppens produkter, välj 'räkna om' för att se den nya listan">	
<br>

Grupp namn:
<input size="80" type="text" name="group_name" value="<?php print $row['group_name'];?>"><br>
Förklaring: <input size="80" type="text" name="group_name_lon" value="<?php print $row['group_name_long'];?>"><br>
Grupp ID: <?php print $row['meta_group_id'];?> Senast ändrad: <?php print $row['change_time'];?><br>
<span title="Viktigt att skriva in större ändringar mm så gruppens utveckling kan följas">Kommentar:</span>
<textarea name="comment" rows="4" cols="70"><?php print $row['comment'];?></textarea><br><br>

<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. Om mer än en sträng, separeras strängar med ','. Om du vill att två eller fler stängar måste finnas i namnet så sammanfoga dem med ett '&' tecken, ordningen de förekommer i spelar ingen roll" style="border-bottom:1px dashed;">Krävda textsträngar:</span> 
<input size="70" type="text" name="keyword_include_1" value="<?php print $row['keyword_include_1'];?>"> <br>

<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten plockas bort även om de nyss angivna strängarna förekom. ## betyder att det är ett fristående ord som inte får förekomma med ett tecken efter ' ',-*/+ etc." style="border-bottom:1px dashed;">Ej tillåtna textsträngar:</span>  
<input size="70" type="text" name="keyword_exclude_1" value="<?php print $row['keyword_exclude_1'];?>"><br>
Kommentar: 
<input size="70" type="text" name="keyword_comment_1 " value="<?php print $row['keyword_comment_1 '];?>"><br><br>

<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. Om mer än en sträng, separeras strängar med ','. Om du vill att två eller fler stängar måste finnas i namnet så sammanfoga dem med ett '&' tecken, ordningen de förekommer i spelar ingen roll" style="border-bottom:1px dashed;">Krävda textsträngar:</span> 
<input size="70" type="text" name="keyword_include_2" value="<?php print $row['keyword_include_2'];?>"><br>
Ej tillåtna textsträngar:
<input size="70" type="text" name="keyword_exclude_2" value="<?php print $row['keyword_exclude_2'];?>"><br>
Kommentar: 
<input size="70" type="text" name="keyword_comment_2" value="<?php print $row['keyword_comment_2'];?>"><br><br>

<span title="Om någon av dessa textsträngar hittas i ett produktnamn så kommer produkten inkluderas i gruppen. Om mer än en sträng, separeras strängar med ','. Om du vill att två eller fler stängar måste finnas i namnet så sammanfoga dem med ett '&' tecken, ordningen de förekommer i spelar ingen roll" style="border-bottom:1px dashed;">Krävda textsträngar:</span> 
<input size="70" type="text" name="keyword_include_3" value="<?php print $row['keyword_include_3'];?>"><br>
Ej tillåtna textsträngar:
<input size="70" type="text" name="keyword_exclude_3" value="<?php print $row['keyword_exclude_3'];?>"><br>
Kommentar: 
<input size="70" type="text" name="keyword_comment_3" value="<?php print $row['keyword_comment_3'];?>"><br><br>

<span title="Exempelvis om alla produkter som börjar med DSK ovilkorligt ska ingå i denna gruppen skriv DSK*. *? tillåtna,  max 3, separerade med ," style="border-bottom:1px dashed;">Skriv del av artikelnummer som ska ingå i grupp:</span> 
<input size="30" type="text" name="change_time" value="<?php print $row['articelnumber_serie_include'];?>"><br> 
<span title="Här kan du styra från vilka artikelnummer medlemmarna ska hämtas. *? tillåtna,  max 3, separerade med ," style="border-bottom:1px dashed;">Skriv artikelnumer som medlemmar ska hämtas från:</span> 
<input size="40" type="text" name="articelnumber_serie_use_as_base" value="<?php print $row['articelnumber_serie_use_as_base'];?>"><br><br>

<span title="lista TWOSELL id för dessa produkter, separerat med komma ','" style="border-bottom:1px dashed;">Uteslut dessa Produkter från gruppen:</span> 
<input size="50" type="text" name="exclude_prod_items" value="<?php print $row['exclude_prod_items'];?>"><br>
<span title="Dessa produkter kommer inkludera i gruppen under alla omständigheter. Lista TWOSELL id för dessa produkter, separerat med komma ','" style="border-bottom:1px dashed;">Inkudera dessa produkter i gruppen:</span> 
<input size="50" type="text" name="include_prod_items" value="<?php print $row['include_prod_items'];?>"><br><br>

<span title="Produkter med ägre pris kommer ej tas med i denna grupp" style="border-bottom:1px dashed;">Minsta tillåtna pris: </span>  
<input size="7" type="text" name="price_min" value="<?php print $row['price_min'];?>">
<span title="Produkter med högre pris kommer ej tas med i denna grupp, om det är 0 så finns inget maxpris" style="border-bottom:1px dashed;">Högsta tillåtna pris: </span>  
<input size="7" type="text" name="price_max" value="<?php print $row['price_max'];?>"><br>

<span title="Podikter i denna grupp får ej föreslås utanför angivna säsonger" style="border-bottom:1px dashed;">Säsong </span> 
1 startmånad: <input size="7" type="text" name="start_month_season_1" value="<?php print $row['start_month_season_1'];?>">
 startdag: 
<input size="7" type="text" name="start_day_season_1" value="<?php print $row['start_day_season_1'];?>">
 slutmånad: 
<input size="7" type="text" name="stop_month_season_1" value="<?php print $row['stop_month_season_1'];?>">
 sista dagen: 
<input size="7" type="text" name="stop_day_season_1" value="<?php print $row['stop_day_season_1'];?>">
<br>Säsong 2 startmånad:
<input size="7" type="text" name="start_month_season_2" value="<?php print $row['start_month_season_2'];?>">
 startdag: 
<input size="7" type="text" name="start_day_season_2" value="<?php print $row['start_day_season_2'];?>">
 slutmånad: 
<input size="7" type="text" name="stop_month_season_2" value="<?php print $row['stop_month_season_2'];?>">
 sista dagen: 
<input size="7" type="text" name="stop_day_season_2" value="<?php print $row['stop_day_season_2'];?>"><br>

<br>
<input type="checkbox" name="never_suggest" value="<?php print $row['never_suggest'];?>"> Gruppens varor får aldrig föreslås av TWOSELL <br>
<input type="checkbox" name="never_trigger" value="<?php print $row['never_trigger'];?>"> Gruppens varor får aldrig trigga TWOSELL förslag <br>
<input type="checkbox" name="group_self_refer" value="<?php print $row['group_self_refer'];?>"> Gruppen får hämta förslag från gruppen själv <br>
<input type="checkbox" name="override_calculted_groups" value="<?php print $row['override_calculted_groups'];?>"><span title="Om det finns manuella förslag kommer producter alltid väljas från dessa" style="border-bottom:1px dashed;"> Använd endast manuella grupper</span> 

<br><br>
<span title="Lista Buikernas ID nummer för alla butiek där gruppen ska vara blockerad, skriv '*' om gruppen ska blockeras i alla butiker" style="border-bottom:1px dashed;">Gruppen spärrad i följande butiker:</span> 
<input size="50" type="text" name="never_suggest" value="<?php print $row['never_suggest'];?>"><br>
<span title="Om en annan vara föreslår denna grupp och det inte finns en matchande product, används dessa produkter." style="border-bottom:1px dashed;">Reservförslag om inga andra förslag finns:</span> 
<input size="40" type="text" name="manual_suggestions" value="<?php print $row['manual_suggestions'];?>"><br>

<span title="Denna grupp får aldrig hämta förslag från följade grupper" style="border-bottom:1px dashed;">Spärrade relationsgruppen:</span> 
<input size="50" type="text" name="group_relation_block_list" value="<?php print $row['group_relation_block_list'];?>"><br>

Hur många grupper av grupperna får förslag hämtas från:  
<input size="1" type="text" name="group_use_top_nr" value="<?php print $row['group_use_top_nr'];?>"><br>
Manuella förslag på grupper: 
<input size="50" type="text" name="group_relation_top_manual" value="<?php print $row['group_relation_top_manual'];?>"><br>

<input type="checkbox" name="override_calculted_groups" value="<?php print $row['override_calculted_groups'];?>"><span title="(Ej implementerad) Är detta en metagrupp som inehåller andra grupper? Om metagrupp så kan man inte ha krävda textsträngar etc i metagruppen. Listan innehåller de grupper som ingår. Alla andra inställningar överrider de inkluderade grupperna och grupperna behandlas som en grupp" style="border-bottom:1px dashed;"> Metagrupp. Följade grupper ingår i denna grupp gruppen:</span>
<input size="35" type="text" name="included_groups" value="<?php print $row['included_groups'];?>"><br><br>


Group Statistics:<br>
Antal produkter i gruppen: <?php print $row['number_of_members'];?><br>
Mest relaterade grupper: <?php print $row['group_relation_top_calculated'];?><br>
<span title="Detta bilr listor som öppnas i nytt fönster, lämpligt för exjobbare, projektarbete" style="border-bottom:1px dashed;">Mest sålda varor i denna gruppen senaste:  </span> 
<input type="submit"  VALUE="7 dagar" title="Öpnas i ny flik"> 	<input type="submit"  VALUE="30 dagar" title="Öpnas i ny flik"> <input type="submit"  VALUE="400 dagar" title="Öpnas i ny flik"> <br>
<br>

<input type="submit"  VALUE="Spara ändringar" title="Detta sparar bara ändringarna men räknar inte om gruppen, alla grupper räknas om nattetid"> 	
<input type="submit" VALUE="Räkna om gruppens produktlista" title="Detta kan ta lite tid beroende av hur avancerad gruppen är, när du räktnat om en grupp syns normalt först resultaten dagen efter i TWOSELL förslag i butikerna">
<input type="submit" VALUE="Visa grupp utan att räkna om" title="Ändringar i formulret kommer inte med påverka gruppens produkter, välj 'räkna om' för att se den nya listan">	
</form>

</div>
