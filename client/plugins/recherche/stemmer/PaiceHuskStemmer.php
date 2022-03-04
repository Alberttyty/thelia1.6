<?php

/*
 *
 * implements a Paice/Husk Stemmer written in PHP by Alexis Ulrich (http://alx2002.free.fr)
 *
 * This code is in the public domain.
 *
 */



class PaiceHuskStemmer
{
	// the rule patterns include all accented forms for a given language
	var $rule_pattern_en = "/^([a-z]*)(\*){0,1}(\d)([a-z]*)([.|>])/";
	var $rule_pattern_fr = "/^([a-zàâèéêëîïôûùç]*)(\*){0,1}(\d)([a-zàâèéêëîïôûùç]*)([.|>])/";

	// we include both languages to prevent I/O of files everywhere

	function getIntlVars(&$PaiceHuskStemmerRules, &$rule_pattern, $language)
	{
		if ($language == 'fr')
		{
			require(realpath(dirname(__FILE__)) . '/PaiceHuskStemRules_fr.php');

			$PaiceHuskStemmerRules = $PaiceHuskStemmerRules_fr;

			$rule_pattern = $this->rule_pattern_fr;
		}
		else if ($language == 'en')
		{
			require(realpath(dirname(__FILE__)) . '/PaiceHuskStemRules_en.php');

			$PaiceHuskStemmerRules = $PaiceHuskStemmerRules_en;
			$rule_pattern = $this->rule_pattern_en;
		}
	}

	// returns the number of the first rule from the rule number $rule_number
	// that can be applied to the given reversed form
	// returns -1 if no rule can be applied, ie the stem has been found
	function getFirstRule($reversed_form, $rule_number, $language='en') {

		$this->getIntlVars($PaiceHuskStemmerRules, $rule_pattern, $language);

		$nb_rules = sizeOf($PaiceHuskStemmerRules);

		for ($i=$rule_number; $i<$nb_rules; $i++) {
			// gets the letters from the current rule
			$rule = $PaiceHuskStemmerRules[$i];
			$rule = preg_replace($rule_pattern, "\\1", $rule);

			if (strncasecmp($rule,$reversed_form,strlen($rule)) == 0) return $i;
		}
		return -1;
	}


	/*
	 * Check the acceptability of a stem for a given language
	 *
	 * $reversed_stem:	the stem to check in reverse form
	 * $language:		text language (default: French)
	 */
	function checkAcceptability($reversed_stem, $language='en') {
		switch ($language) {
			case 'en': # English
				if (preg_match("/[aeiouy]$/",$reversed_stem)) {
					// if the form starts with a vowel then at least two letters must remain after stemming (e.g., "owed"/"owing" --> "ow", but not "ear" --> "e")
					return (strlen($reversed_stem) >= 2);
				}
				else {
					// if the form starts with a consonant then at least three letters must remain after stemming
					if (strlen($reversed_stem) < 3) return False;
					// and at least one of these must be a vowel or "y" (e.g., "saying" --> "say" and "crying" --> "cry", but not "string" --> "str", "meant" --> "me" or "cement" --> "ce")
					return (preg_match("/[aeiouy]/",$reversed_stem));
				}
				break;
			case 'fr': # French
				if (preg_match("/[aàâeèéêëiîïoôuûùy]$/",$reversed_stem)) {
					// if the form starts with a vowel then at least two letters must remain after stemming (e.g.: "étaient" --> "ét")
					return (strlen($reversed_stem) > 2);
				}
				else {
					// if the form starts with a consonant then at least two letters must remain after stemming
					if (strlen($reversed_stem) <= 2) {
						return False;
					}
					// and at least one of these must be a vowel or "y"
					return (preg_match("/[aàâeèéêëiîïoôuûùy]/",$reversed_stem));
				}
				break;
				break;
			default:
				die("Error in checkAcceptability function: the language <i>$language</i> is not supported.");
		}
	}


	/*
	 * the actual Paice/Husk stemmer
	 * which returns a stem for the given form
	 *
	 * $form:		the word for which we want the stem
	 * $language:	the word language (default: French)
	 */
	function Stem($form, $language='en') {

		$this->getIntlVars($PaiceHuskStemmerRules, $rule_pattern, $language);

		$intact = True;
		$stem_found = False;
		$reversed_form = strrev($form);
		$rule_number = 0;
		// that loop goes through the rules' array until it finds an ending one (ending by '.') or the last one ('end0.')
		while (True) {
			$rule_number = $this->getFirstRule($reversed_form, $rule_number, $language);
			if ($rule_number == -1) {
				// no other rule can be applied => the stem has been found
				break;
			}
			$rule = $PaiceHuskStemmerRules[$rule_number];
			preg_match($rule_pattern, $rule, $matches);

			if (($matches[2] != '*') || ($intact)) {
				$reversed_stem = $matches[4] . substr($reversed_form,$matches[3],strlen($reversed_form)-$matches[3]);
				if ($this->checkAcceptability($reversed_stem,$language)) {
					$reversed_form = $reversed_stem;
					if ($matches[5] == '.') break;
				}
				else {
					// go to another rule
					$rule_number++;
				}
			}
			else {
				// go to another rule
				$rule_number++;
			}
		}

		return strrev($reversed_form);

	}
}

?>
