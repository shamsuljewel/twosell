<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$title = "TWOSELL kassa";
$bottom_logo1='TWOSELL_POCADA_final.png';
$bottom_logo2='Statoil.jpg';
$bottom_logo3='NCR_1_logo.jpg';
$images = array(
    0 => array(
        "art_num" => 0,
        "image_name" => 'Bensinpump.jpg',
        "price" => '85'
    ),
    1 => array(
        "art_num" => 1,
        "image_name" => 'Biltvatt.jpg',
        "price" => '100'
    ),
    2 => array(
        "art_num" => 2,
        "image_name" => 'Grillkorv.png',
        "price" => '45'
    ),
    3 => array(
        "art_num" => 3,
        "image_name" => 'Kaffe.png',
        "price" => '80'
    ),
    4 => array(
        "art_num" => 4,
        "image_name" => 'Macka.png',
        "price" => '75'
    ),
    5 => array(
        "art_num" => 5,
        "image_name" => 'Nassprej.png',
        "price" => '81'
        
    ),
    6 => array(
        "art_num" => 6,
        "image_name" => 'Aftonbladet.png',
        "price" => '79'
    ),
    7 => array(
        "art_num" => 7,
        "image_name" => 'Spolarvatska.png',
        "price" => '91'
    ),
    8 => array(
        "art_num" => 8,
        "image_name" => 'Vattenflaska.png',
        "price" => '101'
    ),
);

$suggestions = array(
    0 => array(
        0 => array(
            "id"=> "414",
            "art_num" => "60003456",
            "art_name" => "Olja superway 10w-40w",
            "text" => "När kollade du oljan sist?",
            "price" => 142
        ),
        1 => array(
            "id"=>"1873",
            "art_num" => "",
            "art_name" => "torkarblad",
            "text" => "Randig framruta? Då behövs nya torkarblad",
            "price" => 0
        ),
        2 => array(
            "id"=> "48",
            "art_num" => "9007",
            "art_name" => "spolarvätska",
            "text" => "Är tanken full?",
            "price" => 21
            
        ),
    ),
    1 => array(
        0 => array(
            "id"=>"3736",
            "art_num" => "20007818",
            "art_name" => "extra föravfettning",
            "text" => "Smutsigt? Välj extra avfettning för ren bil",
            "price" => 39
        ),
        1 => array(
            "id"=>"1126",
            "art_num" => "10137580",
            "art_name" => "dammsugspolett",
            "text" => "Städa insidan också?",
            "price" => 20
        ),
        2 => array(
            "id"=>"695",
            "art_num" => "60104084",
            "art_name" => "doftgran Wunderbaum",
            "text" => "Låt bilen lukta gott",
            "price" => 20
        )
        
    ),
    2 => array(
        0 => array(
            "id"=>"81",
            "art_num" => "10133125",
            "art_name" => "tillbehör 6kr",
            "text" => "Extra tillbehör, gurka kanske?",
            "price" => 6
        ),
        1 => array(
            "id"=>"70",
            "art_num" => "10077944",
            "art_name" => "coca-cola pet",
            "text" => "Dricka till maten?",
            "price" => 21
        ),
        2 => array(
            "id"=>"9",
            "art_num" => "10138168",
            "art_name" => "kaffe ",
            "text" => "En liten kaffe",
            "price" => 15
        )
    ),
    3 => array(
        0 => array(
            "id"=>"381",
            "art_num" => "20003442",
            "art_name" => "gb magnum mandel",
            "text" => "Glass är gott till",
            "price" => 21
        ),
        1 => array(
            "id"=>"623",
            "art_num" => "20002872",
            "art_name" => "delicatoboll",
            "text" => "En liten godsak är gott till",
            "price" => 10
        ),
        2 => array(
            "id"=>"646",
            "art_num" => "20013509",
            "art_name" => "sandwich",
            "text" => "Hungrig? Varför inte en fralla?",
            "price" => 12
        )
    ),
    4 => array(
        
        0 => array(
            "id"=>"318",
            "art_num" => "10996903",
            "art_name" => "mer apelsin",
            "text" => "Dryck till maten",
            "price" => 21
        ),
        1 => array(
            "id"=>"9",
            "art_num" => "10138168",
            "art_name" => "kaffe",
            "text" => "En liten kaffe",
            "price" => 15
        ),
        2 => array(
            "id"=>"192",
            "art_num" => "20011159",
            "art_name" => "aftonbladet",
            "text" => "Dagens tidning till maten",
            "price" => 12
        )
    ),
    5 => array(
        0 => array(
            "id"=>"481",
            "art_num" => "20014563",
            "art_name" => "alvedon tabletter",
            "text" => "Bra att ha med värktabletter på resan",
            "price" => 42
        ),
        1 => array(
            "id"=>"1520",
            "art_num" => "20006055",
            "art_name" => "näsdukar",
            "text" => "Extra näsdukar i bilen kan vara bra att ha",
            "price" => 8
        ),
        2 => array(
            "id"=>"1223",            
            "art_num" => "20014796",
            "art_name" => "bromhex",
            "text" => "Slemlösande vid hosta",
            "price" => 36
        )
    ),
   6 => array(
        0 => array(
            "id"=>"824",
            "art_num" => "20016381",
            "art_name" => "aftonbladet tv-bilaga",
            "text" => "TV bilagan, lätt att hitta rätt favorit",
            "price" => 15
        ),
        1 => array(
            "id"=>"404",
            "art_num" => "20016212",
            "art_name" => "marabou japp",
            "text" => "Choklad, alltid gott",
            "price" => 13
        ),
        2 => array(
            "id"=>"9",
            "art_num" => "10138168",
            "art_name" => "kaffe",
            "text" => "En liten kaffe",
            "price" => 15
        )
    ),
    7 => array(
        0 => array(
            "id"=>"X2",
            "art_num" => "",
            "art_name" => "",
            "text" => "Föreslå en extra spolarvätska, bra att ha i reserv",
            "price" => 0
        ),
        1 => array(
            "id"=>"2712",
            "art_num" => "60002529",
            "art_name" => "triss",
            "text" => "Ta chansen att vinna lite extra.",
            "price" => 25
        ),
        2 => array(
            "id"=>"1126",
            "art_num" => "10137580",
            "art_name" => "dammsugspolett",
            "text" => "Passa på att dammsuga bilen?",
            "price" => 20
        )
    ),
    8 => array(
        0 => array(
            "id"=>"572",
            "art_num" => "20012066",
            "art_name" => "tuggummi citrus",
            "text" => "Bra för tänderna",
            "price" => 21
        ),
        1 => array(
            "id"=>"596",
            "art_num" => "60001433",
            "art_name" => "ahlgrens bilar",
            "text" => "Godissugen? En bil som inte går att stoppa",
            "price" => 22
        ),
        2 => array(
            "id"=>"1078",
            "art_num" => "20009436",
            "art_name" => "frukt styckpris",
            "text" => "Nyttig!? En frukt är aldrig fel",
            "price" => 85
        )
    )
    
);
$path = "http://statoil.calc.twosell.se/php/kassa2/";

?>
