<?php
require_once("rss_fetch.inc");

	function recup_rss($url, $nb){
                $i=0;
                $trouve = 0;
                
		$rss = fetch_rss( $url );

                $chantitle = $rss->channel['title'];
                
		$res .= $chantitle . "\n";

                $items = array_slice($rss->items, 0, 10);

                foreach ($items as $item) {
           		 $title = strip_tags($item['title']);
               	 $description = strip_tags($item['description']);
                        $author = $item['dc']['creator'];
                        $date = $item['dc']['date'];


                                        $res .= $author . "\n";
                                        $res .= $title . "\n";
                                        $res .= $description ."\n";
                                        $i++;



                }

		return $res;

        }

echo	recup_rss("http://yoan.octolys.fr/rss.php", 2);

?>
