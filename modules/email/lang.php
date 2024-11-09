<?php
	
	/* PDAconfig MODULE language
	 * 
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */
	
	
	$language['en']['mailtitle'] = 'Email account overview';
	$language['en']['maileditintro'] = 'In the mailserver module you can customize the mailserver based on Dovecot as an IMAP/POP client and Postfix as MTA (mailtransfer agent) for SMTP connections.<br />Before you set up a new email account, please add a new domain in the section after the email account overview.<br /><br />By deleting a mail address the associated emails are not being deleted. If you also wish to delete the emails of the mail account you have to delete the mail folder on the mail server (folder: /var/vmail/DOMAIN/MAILADDRESS)';
	$language['en']['statistictitle'] = 'Statistical information';
	$language['en']['email'] = 'Mail address';
	$language['en']['domain'] = 'Domain';
	$language['en']['domaintitle'] = 'Domains for email accounts';
	$language['en']['aliastitle'] = 'Email address forwarding and catch-all rules';
	$language['en']['aliasdescription'] = 'You can set up forwarding rules for specific email addresses inside or outside your domain or you can define a catch-all email address where every unknown email address for a specific domain will be redirected.';
	$language['en']['catchallfor'] = 'Catchall for';
	$language['en']['source'] = 'Source address';
	$language['en']['destination'] = 'Destination address';
	$language['en']['usedaccounts'] = 'Total amount of accounts';
	$language['en']['usedaliases'] = 'Amount of used forwardings';
	$language['en']['descriptionstatistics'] = 'Here you have a little overview about the mail server module.';
	$language['en']['manage'] = 'Manage module';
	$language['en']['add'] = 'Adding a new entry';
	$language['en']['frommail'] = 'From this mail';
	$language['en']['catchallinfo'] = 'Catchall info: To create a catchall you can easily add @DOMAIN as &laquo;From&raquo; mail.';
	$language['en']['tomail'] = 'To this mail';
	$language['en']['nosuchmail'] = 'There is no such mail.';
	$language['en']['alreadyrule'] = 'A rule on this email already exists. You can not set up multiple forwarding rules. It would not even make any sense to do this.';
	
?>
