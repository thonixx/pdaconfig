<?php

	/* PDAconfig MODULE
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */

?>
<div class="pda-content tworow">
	<h2><?php echo lang('mailtitle') ?></h2>
	<div class="pda-main-content">
		<p>
			<?php echo lang('maileditintro') ?>
		</p>
		<?php

			include "secure_data.php";

			if ($mysql_use_ssl == true) {

				$dbh = mysqli_init()
					or die ('<span class="red medium">mysqli_init failed</span>');
				mysqli_ssl_set($dbh, $mysql_key, $mysql_crt, $mysql_ca, NULL, NULL);
				mysqli_real_connect($dbh, $mysql_server, $mysql_user, $mysql_pass, 'p_mail')
					or die('<span class="red medium">mysql connection to mailserver failed. really failed.</span>');

			} else {

				$dbh = mysqli_connect($mysql_server, $mysql_user, $mysql_pass, 'p_mail')
					or die('<span class="red medium">mysql connection to mailserver failed. really failed.</span>');

			}

			$mail = new mail();
			echo '<div style="float: right; width: 45%">';
			echo $mail->mkMailForm();
			echo '</div>';
			echo $mail->mkTable();

		?>
		<div class="clear"></div>
	</div>
	<h2><?php echo lang('aliastitle') ?></h2>
	<div class="pda-main-content">
		<p>
			<?php echo lang('aliasdescription') ?>
		</p>
		<?php

			echo '<div style="float: right; width: 45%">';
			echo $mail->mkAliasForm();
			echo '</div>';
			echo $mail->mkAliasTable();

		?>
		<div class="clear"></div>
	</div>
</div>
<div class="pda-content short">
	<h2><?php echo lang('statistictitle') ?></h2>
	<div class="pda-main-content">
		<?php echo lang('descriptionstatistics') ?>
		<p>
			<?php

				// query for statistical purposes
				$statQuery = mysqli_query($dbh, 'SELECT COUNT("email") FROM `virtual_users`');
				$statArray = mysqli_fetch_array($statQuery);
				$amountAccounts = $statArray['COUNT("email")'];
				echo '<span class="blue">'.lang('usedaccounts').':</span> '.$statArray['COUNT("email")'].'<br />';

				$statQuery = mysqli_query($dbh, 'SELECT COUNT("source") FROM `virtual_aliases`');
				$statArray = mysqli_fetch_array($statQuery);
				echo '<span class="blue">'.lang('usedaliases').':</span> '.($statArray['COUNT("source")']-$amountAccounts).'<br />';
				// for every email account there is one alias. so you have to subtract them.

			?>
		</p>
	</div>
</div>
