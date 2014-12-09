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
			$dbh = mysql_connect($mysql_server, $mysql_user, $mysql_pass, true) or die('<span class="red medium">mysql connection to mailserver failed. really failed.</span>');
			mysql_select_db('mailserver', $dbh) or die('no such database');
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
				$statQuery = mysql_query('SELECT COUNT("email") FROM `virtual_users`');
				$statArray = mysql_fetch_array($statQuery);
				$amountAccounts = $statArray['COUNT("email")'];
				echo '<span class="blue">'.lang('usedaccounts').':</span> '.$statArray['COUNT("email")'].'<br />';
				
				$statQuery = mysql_query('SELECT COUNT("source") FROM `virtual_aliases`');
				$statArray = mysql_fetch_array($statQuery);
				echo '<span class="blue">'.lang('usedaliases').':</span> '.($statArray['COUNT("source")']-$amountAccounts).'<br />';
				// for every email account there is one alias. so you have to subtract them.
			
			?>
		</p>
	</div>
	<h2><?php echo lang('manage') ?></h2>
	<div class="pda-main-content">
		<div class="status">
			<div><?php echo service('postfix') ?></div>
			<div><?php echo service('dovecot') ?></div>
		</div>
		<p>
			<?php
				
				// restart service
				if($_GET['server'] == 'restart') {
					echo service('postfix', 'restart');
					echo service('dovecot', 'restart');
				}
				
			?>
			<a href="<?php echo $request_url ?>&amp;server=restart">&raquo; <?php echo lang('restart') ?></a>
		</p>
	</div>
</div>
