<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>
       TWOSELL länkar
</title>
</head>
<body>



<?php include("menue.php"); ?>

Om man vill ändra prisintervall på varor som föreslås gå till
<a href="http://elon.master.twosell.se:8080/admin/recommender_two/algorithmsettings2/">elon.master.twosell.se:8080/admin/recommender_two/algorithmsettings2/</a>.
Där man kan ändra prisintervaller för förslag som kommer upp på skärmen. Dessa ändringar kommer först fungera dagen efter (fungerar inte i debug, om det är
viktigt meddela Mobyen så kan han flytta över värdena till debug).<br><br>

<a href="http://debug.calc.twosell.se:8080/php/bilder/elman_koping201202/">Elon Elman Köping bilder från Feb 2012 ("alla" produkter i butiken)</a><br>
<a href="http://debug.calc.twosell.se:8080/php/bilder/elman_koping_filmer/">Elon Elman Köping Fimler (3 minuter)</a><br><br>

<a href="http://elon.master.twosell.se:8080/admin/">Elon Master</a><br>
<a href="http://elkedjan.master.twosell.se:8080/admin/">Elkedjan Master</a><br>
<a href="http://elspar.master.twosell.se:8080/admin/reports/reports/">Elspar Master</a><br>
<a href="http://arkenzoo.master.twosell.se:8080/admin/">ArkenZoo Master </a><br><br>

<a href="http://debug.calc.twosell.se:8080/php/ramin2/index.php">Debug Twosell Elon (Nya grupperna)</a><br>
<a href="http://debug.calc.twosell.se:8080/php/">Debug Twosell Elon test (testa nya grupperna)</a><br><br>

<a href="elon <http://elon.calc.twosell.se:8080/php/clustering/">Elon Manuellt (gamla grupper/synonymer)</a><br>
<a href="elkedjan <http://elkedjan.calc.twosell.se:8080/php/clustering/">Elkedjan Manuellt </a><br>
<a href="elspar manuellt <http://elspar.calc.twosell.se:8080/php/clustering/">Elspar Manuellt<br>
<a href="arkenzoo <http://arkenzoo.calc.twosell.se:8080/php/clustering/">Arken Manuellt Manuellt</a><br><br>

<a href="http://elon.calc.twosell.se:8080/php/">Elon TWOSELL test (gamla testköra vad det blir för förslag)</a><br>
<a href="http://elkedjan.calc.twosell.se:8080/php/">Elkedjan TWOSELL test</a><br>
<a href="http://elspar.calc.twosell.se:8080/php/">Elspar TWOSELL test </a><br>
<a href="http://arkenzoo.calc.twosell.se:8080/php/">Arkenzoo TWOSELL test</a><br><br>


<h2>MySQL links - TOWSELL offline koppling till TWOSELL online (öppnas i nytt fönster):</h2>

<a href="http://test.twosell.se:8080/phpmyadmin/sql.php?db=debug_calc&token=57d5abcf40ccc6d87dd1a2e341904ca6&table=recommender_two_productstorematch&pos=0" target="_blank">
Databas med vilka förslag varje produkt har fått (id=twosellid; score= Poäng för förslagen; match_id = produkten som föreslås</a><br>
<a href="http://test.twosell.se:8080/phpmyadmin/tbl_select.php?db=debug_calc&table=twosell_product&token=57d5abcf40ccc6d87dd1a2e341904ca6"  target="_blank">För att se vilken product som föreslås skriv in twosell-id här</a>

<br><br>
2) Om en ny produkt ej har förslag går Online in här och hämtar förslag:
<a href="http://test.twosell.se:8080/phpmyadmin/sql.php?db=debug_calc&token=57d5abcf40ccc6d87dd1a2e341904ca6&table=recommender_two_productstorematch_item_group&pos=0"  target="_blank">
Om en ny produkt ej har förslag går Online in här och hämtar förslag i denna lista</a><br><br>

<h2>Wiki för TWOSELL och Bugtracker</h2>
<a href="http://wiki.twosell.se:8090/">Wiki för Pocada personal, där lägger vi in instruktioner, rutiner mm</a><br>
<a href="http://bugtracker.twosell.se:8091/">Fel och status för TWOSELL</a><br><br>


</body>