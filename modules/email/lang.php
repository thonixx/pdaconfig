<?php

	/* PDAconfig MODULE language
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */


	$language['en']['emailtitle'] = 'email account overview';
	$language['en']['emaileditintro'] = 'In the emailserver module you can customize the emailserver based on Dovecot as an IMAP/POP client and Postfix as MTA (emailtransfer agent) for SMTP connections.<br />Before you set up a new email account, please add a new domain in the section after the email account overview.<br /><br />By deleting a email address the associated emails are not being deleted. If you also wish to delete the emails of the email account you have to delete the email folder on the email server (folder: /var/vemail/DOMAIN/emailADDRESS)';
	$language['en']['statistictitle'] = 'Statistical information';
	$language['en']['email'] = 'Email Address';
	$language['en']['domain'] = 'Domain';
	$language['en']['domaintitle'] = 'Domains for email accounts';
	$language['en']['aliastitle'] = 'Email Address forwarding and catch-all rules';
	$language['en']['aliasdescription'] = 'You can set up forwarding rules for specific email addresses inside or outside your domain or you can define a catch-all email address where every unknown email address for a specific domain will be redirected.<br/>If you want to blackhole a certain email alias, forward it to spam@&lt;domain&gt;.';
	$language['en']['aliases_catchall'] = 'Catchall Aliases';
	$language['en']['aliases_wo_spam'] = 'Aliases w/o spam@';
	$language['en']['aliases_spam'] = 'Blackhole Aliases';
	$language['en']['catchallfor'] = 'Catchall for';
	$language['en']['source'] = 'Source address';
	$language['en']['destination'] = 'Destination address';
	$language['en']['usedaccounts'] = 'Total amount of accounts';
	$language['en']['usedaliases'] = 'Amount of used forwardings';
	$language['en']['descriptionstatistics'] = 'Here you have a little overview about the email server module.';
	$language['en']['manage'] = 'Manage module';
	$language['en']['addemail'] = 'Adding a new email inbox';
	$language['en']['addalias'] = 'Adding a new email forwarding';
	$language['en']['fromemail'] = 'From this email';
	$language['en']['catchallinfo'] = 'Catchall info: To create a catchall you can easily add @DOMAIN as &laquo;From&raquo; email.';
	$language['en']['toemail'] = 'To this email';
	$language['en']['nosuchemail'] = 'There is no such email.';
	$language['en']['alreadyrule'] = 'A rule on this email already exists. You can not set up multiple forwarding rules. It would not even make any sense to do this.';

?>
