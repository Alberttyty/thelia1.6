<?php

/*
 *
 * implements a Paice/Husk Stemmer written in PHP by Alexis Ulrich (http://alx2002.free.fr)
 *
 * Tool kit
 *
 * This code is in the public domain.
 *
 */
require_once(realpath(dirname(__FILE__)) . '/PaiceHuskStemmer.php');

class StemmingToolkit
{

	// punctuation characters
	var $punctuation = array('.', ',', ';', ':', '!', '?', '"', '\'', '(', ')', '--');


	/*
	 * standardized punctuation: each punctuation mark has a space before and after it
	 *
	 * 	$text:	string, the text to be processed
	 *	$lang:	language of the text (default: English)
	 */
	function standardizePunctuation($text, $lang='en') {
		// puts a space before and after a punctuation mark,
		// whatever the number of spaces there were before and after it
		$text = preg_replace('/( )*(["\'\.,;:\(\)\?!])( )*/', ' \\2 ', $text);
		// whitespace
		$text = preg_replace('/\s/', ' ', $text);
		if ($lang == 'en') {
			// handles the didn't, couldn't...
			$text = str_replace('n \' t', 'n\'t', $text);
			// handles the o'clock
			$text = str_replace('o \' clock', 'o\'clock', $text);
		}
		return $text;
	}

	/*
	 * indexes the given text and returns an array of three arrays:
	 *	- 'original': the original text
	 *	- 'modified': the modified text, ie the standardized-punctuation form
	 *	- 'index': an array of three-element arrays:
	 *			- 'form': the form of the word in the original text
	 *			- 'index': the index of the form in the modified text
	 *			- 'stem': the stem of the form
	 *
	 * 	$text:		string, the text to be processed
	 *	$lang:		language of the text (default: English)
	 */
	function indexText($text, $lang='en', $stem = true) {
		global $punctuation;
		require_once(realpath(dirname(__FILE__)) . '/stoplist_'.$lang.'.inc.php');
		$indexArray = array();
		$thisText = $this->standardizePunctuation($text, $lang);
		$thisTextWords = explode(' ',$thisText);
		$thisTextIndex = array();
		$wordIndex = 0;

		$stemmer = new PaiceHuskStemmer();

		for ($i=0; $i<sizeOf($thisTextWords); $i++)
		{
			$form = $thisTextWords[$i];
			$word = strtolower($form);

			// words which length is 1 or 0 are not processed.
			if ((!@in_array($word, $punctuation)) && (strlen($word) > 1) && (!@in_array($word, $stoplist))) {

				$thisTextIndex[] = array('form'=>$form, 'stem'=>($stem ? $stemmer->Stem($word,$lang) : $word), 'index'=>$wordIndex);
			}
			$wordIndex = $wordIndex + strlen($word) + 1; // the last space
		}

		return array('original'=>$text, 'modified'=>$thisText, 'index'=>$thisTextIndex);
	}
}

?>
