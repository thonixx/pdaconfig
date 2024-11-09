<?php

	/* PDAconfig
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */

	// this is the language file
	// structure: $language['SHORT LANGUAGE']['ID'] = 'TEXT STRING';
	// example: $language['de']['1'] = 'Hallo Welt!';

	// usage in scripts: $lang['ID'];

	$language['en']['language'] = 'en';
	$language['en']['software'] = 'PDAconfig';
	$language['en']['pdaconfigtitle'] = 'PDAconfig | Server configuration tool';
	$language['en']['copyright'] = 'Copyright '.date('Y').' @ github.com/thonixx';
	$language['en']['mysqlconnect'] = 'Database error: connection to mysql server failed';
	$language['en']['mysqlselectdb'] = 'Database error: could not select database';
	$language['en']['logindialogtitle'] = 'PDAconfig Authentication';
	$language['en']['usernameinput'] = 'Your username';
	$language['en']['loginbutton'] = 'Login';
	$language['en']['capslock'] = 'Capslock activated';
	$language['en']['warnlogin'] = 'Login failed. Check data.';
	$language['en']['running'] = 'is running';
	$language['en']['notrunning'] = 'is not running';
	// date formats
	$language['en']['datemonth'] = date('F d'); // January 01
	$language['en']['datemonthyear'] = date('F d, Y'); // Januar 01, 1970
	$language['en']['time'] = date('H:i'); // 23:59
	$language['en']['nomoduletitle'] = 'Module not found';
	$language['en']['nomoduletitleshort'] = 'Recommendation';
	$language['en']['recommendation'] = 'You should have the main file &raquo;index.php&laquo; in the directory modules/YOURMODULE/';
	$language['en']['nomodule'] = 'Module could not be found. Please check installation and structures.';
	$language['en']['nomoduletitle'] = 'Lanuage file of module could not be found:';
	$language['en']['nodirectory'] = 'Directory does not exist'; // in class userPref
	$language['en']['loadqueryfailed'] = 'The mysql query in class function loadQuery failed. Error:<br /><br />'; // in class userPref
	$language['en']['logout'] = 'Logout';
	$language['en']['accessdenied'] = 'Access denied';
	$language['en']['accessdeniedtext'] = 'You have no access to this module. Please report this to your administrator or choose another module in the navigation.';
	$language['en']['nolanguage'] = 'Language file not found. Module: ';
	$language['en']['nowrite'] = 'You have no write-permission in this module';
	$language['en']['reallydelete'] = 'Do you really want to delete the entry?';
	$language['en']['yes'] = 'Yes';
	$language['en']['no'] = 'No';
	$language['en']['deleteit'] = 'delete it';
	$language['en']['dontdeleteit'] = 'do not delete it';
	$language['en']['cantdeladmin'] = 'You can not delete admin user';
	$language['en']['addentry'] = 'Adding another entry';
	$language['en']['success'] = 'Success';
	$language['en']['fail'] = 'Something failed.';
	$language['en']['successsaving'] = 'The data was saved successfully.';
	$language['en']['failedsaving'] = 'Something failed. Could not save.';
	$language['en']['wrongmail'] = 'Wrong format for email.';

?>
