<?php
	class googleCustomSearch
	{
		function googleCustomSearch()
		{
			//empty constructor
		}
		
		//a handy function to clean the inputs from a search query
		function cleanInput($input)
		{
			$input = preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
			$input = str_replace(" ","+",$input);
			$input = str_replace("++","+",$input);
			
			return $input;
		}
		
		function getXML($searchterms,$start)
		{
			//the unique identifier for this Google Custom Search
			$cseNumber = 'dowgy9_pqaq'; // CHANGE ME!!!!!!  this won't work otherwise
			
			echo $xmlfile = 'http://www.google.com/search?cx='.$cseNumber.'&client=google-csbe&start='.$start.'&num=10&output=xml_no_dtd&q='.$searchterms;
			$xml = new SimpleXMLElement(file_get_contents($xmlfile));
			
			return $xml;
		}
		
		//get estimated total number of results.  this is awfully inaccurate until you're on the final page
		//so it's only useful for working out whether there is a next page or not, rather than "results 1-10 of 417
		function getTotalResults($xml)
		{
			$total = $xml->RES->M;
			return $total;
		}
		
		//total number of results for this page
		function getPageTotal($xml)
		{
			return count($xml->RES->R);
		}
		
		function writePagination($totalnum, $total, $start, $searchterms)
		{
			//if there you have a full 10 results on the page
			if($total >= 10)
			{	
				//if we're not at the start
				if($start != 0)
				{
					echo '<a href="?s='.($start -10).'&q='.$searchterms.'"><strong>&lt; Previous Page</strong></a>';
					echo '&nbsp;&nbsp;';
				}					
				//if where you started + 10 is NOT equal to the total number of results
				if(($start + 10) != $totalnum)
				{
					echo '<a href="?s='.($start +10).'&q='.$searchterms.'"><strong>Next Page &gt;</strong></a>';
				}
			}
		}
		
		//this function needs customising if you want to modify how the search results are displayed
		function writeSearchResults($xml)
		{
			foreach ($xml->RES->R as $key)
			{
				$output .= '<p class="search-result"><a href="'.$key->U.'">'.$key->T.'</a>';
				$output .= $key->S.'<br />';
				//$output .= '<span>'.$key->U.'</span></p>';
				$output .= '</p>';
			}
			
			echo $output;
		}
		
		//"did you mean..."
		function writeSuggestion($xml)
		{
			$searchterm = $xml->Q;
			$suggestion = $xml->Spelling->Suggestion;
			
			if($suggestion != "")
				echo '<p>You searched for <strong>'.$searchterm.'</strong>, did you mean <strong><a href="?q='.strip_tags($suggestion).'">'.$suggestion.'</a></strong>?</p>';
		}
	}
?>