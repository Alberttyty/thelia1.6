<?php       
	include 'googleCustomSearch.php';
                
	//instantiate the class
	$cs = new googleCustomSearch();
	
	//get the search terms from post and clean it, whatever you called them
	$searchterms = $cs->cleanInput('test'/*$_POST['searchterms']*/);
	             
	//if the start number was querystringed
	if($_GET['s'])
	{
		$start = (int)$_GET['s'];
	}
	else 
	{
		$start = 0;
	}
	
	//if the query was querystringed through (for pagination purposes)
	if($_GET['q'])
	{
		$searchterms = $cs->cleanInput($_GET['q']);
	}
         
	//get the XML (as built in the class)
	$xml = $cs->getXML($searchterms,$start);
	   
	//get the number of results on this page
	$total = $cs->getPageTotal($xml);
	
	//get the *ESTIMATED* total results, only useful on the final page
	$totalnum = $cs->getTotalResults($xml);
	
	//to show the Y of "showing results x - y"
	$displaytotal = $total + $start;
	
	if($displaytotal != 0)
		echo '<p><strong>Viewing results '.($start+1).' - '.$displaytotal.'</strong></p>';
	
	/*check for no results*/
	if($total == 0 )
  	{
		echo '<p>Sorry, there were no results.</p>';
	}
	 
	//write the suggestion (if there is one - this check is done in the function itself)
	$cs->writeSuggestion($xml);

	//write search results
	$cs->writeSearchResults($xml);
	
	//write the pagination
	$cs->writePagination($totalnum, $total, $start, $searchterms);
	
?>