<?php

	/* PDAconfig MODULE
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */

	// create the class used in this module
	$apache = new apache();
	// addDomain for adding a new domain
	// rmDomain to remove a domain
	// rebuild to parse the database values into hard written files for apache2

	// for debugging purposes
	//echo 'Adding pixelcraft.ch: '.$apache->addDomain('pixelcraft.ch', '/var/www', array('www.pixelcraft.ch', 'sub.pixelcraft.ch'));
	//echo '<br />Removing pixelwolf.ch: '.$apache->rmDomain('pixelwolf.ch');
	//echo '<br />Rebuild: '.$apache->rebuild();

?>
<div class="pda-content tworow">
	<h2><?php echo lang('apachetitle') ?></h2>
	<div class="pda-main-content">
		<p>
			<?php echo lang('apachedescription') ?>
		</p>
		<p>
			<?php echo lang('rebuildtext') ?><br />
			<a href="?module=<?php echo $_GET['module'] ?>&amp;config=rebuild"><?php echo lang('rebuildlink') ?></a>
			<?php

				// rebuild configuration
				if($_GET['config'] == 'rebuild') {
					echo '<br />';
					echo $apache->rebuild();
					echo '<br />';
				}

			?>
		</p>
		<!-- Form for adding new/editing domains or dialog for requesting deletion -->
		<div style="float: right; width: 45%">
			<?php if($_GET['delete'] == true) { ?>
				<div id="edit" style="margin: 25px 0pt;" class="border">
					<h2>Do you really want to delete the entry?</h2>
					<?php

						// reask to be sure
						if(!isset($_GET['reallyDelete'])) {

					?>
					<p style="padding: 0pt 20px;">
						<a href="?module=<?php echo $_GET['module'] ?>&amp;delete=<?php echo $_GET['delete'] ?>&amp;reallyDelete=<?php echo $_GET['delete'] ?>#edit">
							<img src="img/yes.png" alt="Yes"> Yes, delete it</a><br><br>
						<a href="?module=<?php echo $_GET['module'] ?>">
							<img src="img/no.png" alt="Yes"> No, do not delete it</a>
					</p>
					<?php

						} else {
							echo '<p style="padding: 0pt 20px;">';
							echo $apache->rmDomain($_GET['reallyDelete']);
							echo '</p>';
						}

					?>
				</div>
			<?php } else { ?>
				<form id="edit" style="width: 100%; margin: 25px 0;" class="border" action="?module=<?php echo $_GET['module']; echo $_GET['edit'] ? '&amp;edit='.$_GET['edit'] : '' ; ?>#edit" method="post">
					<h2><?php echo lang('addedittitle'); ?></h2>
					<div style="padding: 15px 15px 0 15px">
						<p><?php echo lang('rebuildtext') ?></p>
						<?php

							// if editing a domain read everything out of a query
							if($_GET['edit'] == true) {
								$editquery = mysqli_query($mysql_connect, 'SELECT apache_vhost.servername, documentroot, `option` FROM apache_vhost LEFT JOIN apache_custom on apache_vhost.servername = apache_custom.servername WHERE apache_vhost.servername = "'.mysqli_real_escape_string($mysql_connect, $_GET['edit']).'"');
								$sql = mysqli_fetch_assoc($editquery);
								echo mysqli_error();

								// build server aliases string
								$aliasquery = mysqli_query($mysql_connect, 'SELECT * FROM apache_aliases WHERE servername ="'.mysqli_real_escape_string($mysql_connect, $_GET['edit']).'"');
								$serveraliases = '';
								while($aliases = mysqli_fetch_array($aliasquery)) {
									$serveraliases .= $aliases['serveralias']."\n";
								}
								$serveraliases = trim($serveraliases); // remove unnecessary space at the end

								if(isset($_POST['submit'])) {
									// update main vhost
									$updateSQL = 'UPDATE apache_vhost SET servername = "'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'", documentroot = "'.mysqli_real_escape_string($mysql_connect, $_POST['documentroot']).'" WHERE servername = "'.mysqli_real_escape_string($mysql_connect, $_GET['edit']).'"';
									$updateQuery = mysqli_query($mysql_connect, $updateSQL);

									// update aliases
									$aliasArray = explode("\n", $_POST['serveralias']);
									// first remove current aliases
									mysqli_query($mysql_connect, 'DELETE FROM apache_aliases WHERE servername = "'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'"');
									// then insert every alias again
									foreach($aliasArray as $alias) {
										mysqli_query($mysql_connect, 'INSERT INTO apache_aliases (servername, serveralias) values("'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'", "'.mysqli_real_escape_string($mysql_connect, $alias).'")');
										if(mysqli_error() == true) echo lang('fail');
									}
									// and at least insert the custom configuration
									// look for existing entry
									$cust_exist = mysqli_fetch_assoc(mysqli_query($mysql_connect, 'SELECT `servername` FROM apache_custom WHERE servername = "'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'"'));
									if($cust_exist['servername'] == $_POST['servername']) {
										mysqli_query($mysql_connect, 'UPDATE apache_custom SET `option` = "'.mysqli_real_escape_string($mysql_connect, $_POST['custom']).'" WHERE servername = "'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'"');
									} else {
										mysqli_query($mysql_connect, 'INSERT INTO apache_custom (servername, `option`) VALUES ("'.mysqli_real_escape_string($mysql_connect, $_POST['servername']).'", "'.mysqli_real_escape_string($mysql_connect, $_POST['custom']).'")');
									}

									if(mysqli_error() == false) echo '<span class="green medium bold">'.lang('success').'</span><br /><br />';
									else echo '<span class="red medium bold">'.lang('fail').mysqli_error().'</span><br /><br />';
								}
								// reread the data to be sure to have the most recent data
								$editquery = mysqli_query($mysql_connect, 'SELECT apache_vhost.servername, documentroot, `option` FROM apache_vhost LEFT JOIN apache_custom on apache_vhost.servername = apache_custom.servername WHERE apache_vhost.servername = "'.mysqli_real_escape_string($mysql_connect, $_GET['edit']).'"');
								$sql = mysqli_fetch_assoc($editquery);
							}

							if(isset($_POST['submit']) and !isset($_GET['edit'])) {
								// build alias array
								$aliasArray = explode("\n", $_POST['serveralias']);
								if(count($aliasArray) > 0) $aliases = $aliasArray;
								else $aliases = false;

								// adding the configuration
								echo $apache->addDomain($_POST['servername'], $_POST['documentroot'], $aliases, $_POST['custom']).'<br /><br />';
							}

						?>
						<?php echo lang('maindomain'); ?>: <br />
							<input type="text" name="servername" value="<?php echo $_POST['servername'] ? $_POST['servername'] : $sql['servername'] ?>" /><br />
						<?php echo lang('aliasdomain'); ?>: <br />
							<textarea name="serveralias"><?php echo $_POST['serveralias'] ? $_POST['serveralias'] : $serveraliases ?></textarea><br />
						<?php echo lang('docroot'); ?>: <br />
							<input type="text" name="documentroot" value="<?php echo $_POST['documentroot'] ? $_POST['documentroot'] : $sql['documentroot'] ?>" /><br />
						<?php echo lang('customopts'); ?>: <br />
							<textarea name="custom"><?php echo $_POST['custom'] ? $_POST['custom'] : $sql['option'] ?></textarea><br />

						<br /><input type="submit" name="submit" value="Save" /></div>
				</form>
			<?php } ?>
		</div>
		<!-- Table with all domains and subdomains -->
		<table style="width: 50%; float: left; margin: 20px 0;">
			<tr>
				<th>Main URL</th>
				<th>Alias URLs</th>
				<th>Root folder</th>
				<th>Options</th>
			</tr>
			<?php

				// list all domains, aliases and document roots
				$query = mysqli_query($mysql_connect, 'SELECT * FROM apache_vhost');

				while($vhost = mysqli_fetch_array($query)) {
					echo '<tr class="aligntop';
					if(is_float($counter/2)) echo ' background';
					echo '"';
					if($vhost['servername'] == $_GET['delete'] or $vhost['servername'] == $_GET['edit']) echo ' style="color: red;"';
					echo '>';
						echo '<td class="aligntop">'.$vhost['servername'].'</td>';
						echo '<td>';

							// now list all server aliases if available
							$somethingThere = false;
							$aliasQuery = mysqli_query($mysql_connect, 'SELECT serveralias FROM apache_aliases WHERE servername = "'.$vhost['servername'].'"');
							while ($alias = mysqli_fetch_array($aliasQuery)) {
								if($alias['serveralias'] == true) {
									echo $alias['serveralias'].'<br />';
									$somethingThere = true;
								}
							}
							if($somethingThere == false) echo '<span class="grey small">'.lang('noaliases').'</span>';

						echo '</td>';
						echo '<td>'.limit($vhost['documentroot'], 35, true).'</td>';
						echo '<td><a href="?module='.$_GET['module'].'&amp;edit='.$vhost['servername'].'#edit"><img src="modules/'.$_GET['module'].'/edit.png" alt="Edit"></a> <a href="?module='.$_GET['module'].'&amp;delete='.$vhost['servername'].'#edit"><img src="modules/'.$_GET['module'].'/delete.png" alt="delete"></a></td>';
					echo '</tr>';
				}

			?>
		</table>
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
				$statQuery = mysqli_query($mysql_connect, 'SELECT COUNT("servername") FROM `apache_vhost`');
				$statArray = mysqli_fetch_array($statQuery);
				$amountAccounts = $statArray['COUNT("servername")'];
				echo '<span class="blue">'.lang('usedvhosts').':</span> '.$statArray['COUNT("servername")'].'<br />';

				$statQuery = mysqli_query($mysql_connect, 'SELECT COUNT("serveralias") FROM `apache_aliases`');
				$statArray = mysqli_fetch_array($statQuery);
				echo '<span class="blue">'.lang('usedaliases').':</span> '.$statArray['COUNT("serveralias")'].'<br />';
				// for every email account there is one alias. so you have to subtract them.

			?>
		</p>
	</div>
	<h2><?php echo lang('manage') ?></h2>
	<div class="pda-main-content">
		<div class="status">
			<div><?php echo service('apache2') ?></div>
			<div><?php echo service('mysql') ?></div>
		</div>
		<p>
			<?php

				// restart service
				if($_GET['server'] == 'restart') {
					echo service('apache2', 'restart');
					echo service('mysql', 'restart');
				}

			?>
			<a href="<?php echo $request_url ?>&amp;server=restart">&raquo; <?php echo lang('restart') ?></a>
		</p>
	</div>
</div>
