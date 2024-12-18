<?php

	/* PDAconfig
	*
	* scripted by Michael Tanner for Schleuniger Montagen AG
	* www.white-tiger.ch
	* www.schleuniger-montagen.ch
	*/

	// parse http accept language string and return language array
	function parse_lang($header = '') {
		// source for regular expression: http://www.thefutureoftheweb.com/blog/use-accept-language-header
		// heavily modified!

		if($header == false) $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $header, $lang_parse);

		// put user defined language into array at top position
        global $mysql_connect;
        $login_sess = $_SESSION['login_session'] ? $_SESSION['login_session'] : '0';
		$userLanuageQuery = mysqli_query($mysql_connect, 'SELECT `lang` FROM `authentication` WHERE `id` = '.$login_sess);
		$userLanguageAssoc = mysqli_fetch_assoc($userLanuageQuery);
		$langs[] = $userLanguageAssoc['lang'];
		foreach($lang_parse[1] as $l) {
			// parse http header into array
			$langs[] = $l;
		}

		return $langs;
	}
	$languageArray = parse_lang();

	// output of language string
	function lang($string, $trim = '') {
		global $language;
		global $languageArray;

		$found = false;
		foreach($languageArray as $lang) {
			if($language[$lang][$string] == true) {
				if($trim == true and $trim > 0) {
					return limit($language[$lang][$string], $trim);
				} else {
					return $language[$lang][$string];
				}
			}
		}

		if($language['en'][$string] == true) return $language['en'][$string];
		else return '<span class="red">String "'.$string.'" not found.</span>';
	}

	// trim a defined string and add ...
	function limit($str, $int = false, $reverse = false) {
		if($str == false) return false;
		if($int == false) $int = 25;

		$suffix = '...';
		$limit = $int - strlen($suffix);

		if(strlen($str) <= $int) return $str;
		if(strlen($str) > $limit and $reverse == false) $return = substr($str, 0, $limit).$suffix;
		elseif(strlen($str) > $limit and $reverse == true) $return = $suffix.substr($str, strlen($str)-$limit, strlen($str));
		else $return = $str;

		return $return;
	}

	// to check if a mail is valid and as ouput gives the domain and the mail address
	function chkMail($input, $catchall = false) {
		if(empty($input)) return false;

		if(preg_match("/^^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $input) == true)
			preg_match_all("/^^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $input, $mailArray);
		elseif(preg_match("/^^(@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?))$/", $input) == true && $catchall != false)
			preg_match_all("/^^(@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?))$/", $input, $mailArray);
		else
			return false;

		return $mailArray;
	}

?>
