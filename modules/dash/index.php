<?php

	/* PDAconfig MODULE
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */

?>
<div class="pda-dash">
	<?php

		if($_GET['editUser'] and $userPref['dash'] >= 6) {
			$getQuery = mysqli_query($mysql_connect, 'SELECT * FROM `authentication` INNER JOIN `group` on `group`.`id` = `authentication`.`group` WHERE `authentication`.`id` = '.mysqli_real_escape_string($mysql_connect, $_GET['editUser']));
			$getAssoc = mysqli_fetch_assoc($getQuery);
			$fehler = false;

			?>
			<h2><?php echo lang('editinguser') ?></h2>
			<div class="pda-main-content">
				<form action="<?php echo $request_url ?>" method="post">
					<div>
						<?php echo lang('username'); ?>:<br /><input type="text" name="username" value="<?php echo $_POST['username'] ? $_POST['username'] : $getAssoc['username'] ?>" /><?php
							if($_POST['username'] == false and isset($_POST['submit'])) {echo '<br /><span class="red">'.lang('mustnotbeempty').'<br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('realname'); ?>:<br /><input type="text" name="realname" value="<?php echo $_POST['realname'] ? $_POST['realname'] : $getAssoc['realname'] ?>" /><br />
						<?php echo lang('password'); ?>:<br /><input type="password" name="password" value="" /><?php
							if($_POST['password'] == true and $_POST['password'] != $_POST['repeatpassword']) {echo '<br /><span class="red">'.lang('nopwmatch').'<br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('repeatpassword'); ?>:<br /><input type="password" name="repeatpassword" value="" /><br />
						<?php echo lang('languagelabel'); ?>:<br /><input type="text" name="language" value="<?php echo $_POST['language'] ? $_POST['language'] : $getAssoc['lang'] ?>" /><?php
							if($_POST['language'] == false and isset($_POST['submit'])) {echo '<br /><span class="red">'.lang('mustnotbeempty').'<br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('group'); ?>:<br /><select name="group"><?php

							$groupQuery = mysqli_query($mysql_connect, 'SELECT `id`, `name` FROM `group`');

							while($group = mysqli_fetch_array($groupQuery)) {
								echo '<option ';
								// to mark group as selected from current users group
								if($group['id'] == $getAssoc['group'] or $group['id'] == $_POST['group']) echo 'selected="selected"';
								echo ' value="'.$group['id'].'">'.$group['name'].'</option>';
							}

						?></select><br />
						<br />
						<input type="submit" name="submit" value="<?php echo lang('savebutton'); ?>" />
						<?php
							// save data if no error was found
							if(isset($_POST['submit']) and $fehler == false) {
								$updateSQL = 'UPDATE `authentication` SET `username` = "'.mysqli_real_escape_string($mysql_connect, $_POST['username']).'", `realname` = "'.mysqli_real_escape_string($mysql_connect, $_POST['realname']).'", `lang` = "'.mysqli_real_escape_string($mysql_connect, $_POST['language']).'", `group` = '.mysqli_real_escape_string($mysql_connect, $_POST['group']);
								if($_POST['password'] == $_POST['repeatpassword'] and !empty($_POST['password'])) $updateSQL .= ', `password` = MD5("'.$_POST['password'].'")';
								$updateSQL .= ' WHERE `id` = '.mysqli_real_escape_string($mysql_connect, $_GET['editUser']);

								if(mysqli_query($mysql_connect, $updateSQL) == true) echo '<p class="green medium">'.lang('successfulsaved').'</p>';
								else echo '<p class="red medium">'.lang('failedsaving').'</p>';
							}
						?>
					</div>
				</form>
			</div>
			<?php
		} elseif(($_GET['delUser'] == true or $_GET['reallyDelete'] == true) and $userPref['dash'] >= 6) {
			?>
			<h2><?php echo lang('editinguser') ?></h2>
			<div class="pda-main-content">
				<div class="border">
					<h2><?php echo lang('reallydelete') ?></h2>
					<?php
						if($_GET['reallyDelete'] > 1) {
							// to prevent deleting an admin user
							if(mysqli_query($mysql_connect, 'DELETE FROM `authentication` WHERE `id` = '.mysqli_real_escape_string($mysql_connect, $_GET['reallyDelete'])))
								echo '<p class="medium bold green">'.lang('success').'</p>';
							else
								echo '<p class="medium bold red">'.lang('fail').'</p>';
						} elseif($_GET['reallyDelete'] == 1) {
							echo '<p class="medium bold red">'.lang('cantdeladmin').'</p>';
						} else {
					?>
					<p style="padding: 0 20px;">
						<a href="?module=<?php echo $_GET['module'] ?>&amp;reallyDelete=<?php echo $_GET['delUser'] ?>">
							<img src="img/yes.png" alt="<?php echo lang('yes') ?>" /> <?php echo lang('yes') ?>,  <?php echo lang('deleteit') ?>
						</a><br /><br />
						<a href="?module=<?php echo $_GET['module'] ?>">
							<img src="img/no.png" alt="<?php echo lang('yes') ?>" /> <?php echo lang('no') ?>,  <?php echo lang('dontdeleteit') ?>
						</a>
					</p>
					<?php } ?>
				</div>
			</div>
			<?php
		} else {
			$i = 0;
			foreach($module_array as $module) {
				if($module['status'] == true) {
					$i++;
	?>
					<h2><?php echo lang('module').' '.$i.': '.$module['nav'] ?></h2>
					<div class="pda-main-content">
						<?php include($moduleOverview->dir.'/'.$module['name'].'/'.'status.php'); ?>
					</div>
	<?php
				}
			}
		}
	?>
</div>
<div class="pda-dash">

	<!-- List current users from database -->
	<h2><?php echo lang('useradmin_title'); ?></h2>
	<div class="pda-main-content">
		<p><?php echo lang('introduction_useradmin'); ?></p>
		<?php include($modDir.'useradmin.php') ?>
	</div>

	<!-- Adding a new entry to the user database if user has permission -->
	<?php
		if($userPref['dash'] >= 6) { // check permission
			$fehler = false;
	?>
	<h2><?php echo lang('addentry') ?></h2>
	<div class="pda-main-content">
		<form action="<?php echo str_replace('editUser', 'nothing', $request_url) // prevent editing the user instead of saving a new one ?>" method="post">
					<div>
						<?php echo lang('username'); ?>:<br /><input type="text" name="username" value="<?php echo $_POST['username'] ? $_POST['username'] : $getAssoc['username'] ?>" /><?php
							if($_POST['username'] == false and isset($_POST['submit'])) {echo '<br /><span class="red">'.lang('mustnotbeempty').'<br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('realname'); ?>:<br /><input type="text" name="realname" value="<?php echo $_POST['realname'] ? $_POST['realname'] : $getAssoc['realname'] ?>" /><br />
						<?php echo lang('password'); ?>:<br /><input type="password" name="password" value="" /><?php
							if($_POST['password'] == true and $_POST['password'] != $_POST['repeatpassword']) {echo '<br /><span class="red">'.lang('nopwmatch').'<br /></span>';$fehler = true;}
							elseif(($_POST['password'] == false or $_POST['repeatpassword'] == false) and isset($_POST['submit'])) {echo '<br /><span class="red">'.lang('mustnotbeempty').'<br /><br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('repeatpassword'); ?>:<br /><input type="password" name="repeatpassword" value="" /><br />
						<?php echo lang('languagelabel'); ?>:<br /><input type="text" name="language" value="<?php echo $_POST['language'] ? $_POST['language'] : $getAssoc['lang'] ?>" /><?php
							if($_POST['language'] == false and isset($_POST['submit'])) {echo '<br /><span class="red">'.lang('mustnotbeempty').'<br /></span>';$fehler = true;}
							?><br />
						<?php echo lang('group'); ?>:<br /><select name="group"><?php

							$groupQuery = mysqli_query($mysql_connect, 'SELECT `id`, `name` FROM `group`');

							while($group = mysqli_fetch_array($groupQuery)) {
								echo '<option ';
								// to mark group as selected from current users group
								if($group['id'] == $getAssoc['group'] or $group['id'] == $_POST['group']) echo 'selected="selected"';
								echo ' value="'.$group['id'].'">'.$group['name'].'</option>';
							}

						?></select><br />
						<br />
						<input type="submit" name="submit" value="<?php echo lang('savebutton'); ?>" />
						<?php
							// save data if no error was found
							if(isset($_POST['submit']) and $fehler == false) {
								$insertSQL = 'INSERT INTO `authentication` (`username`, `password`, `realname`, `lang`, `group`)
												VALUES ("'.mysqli_real_escape_string($mysql_connect, $_POST['username']).'", MD5("'.mysqli_real_escape_string($mysql_connect, $_POST['password']).'"), "'.mysqli_real_escape_string($mysql_connect, $_POST['realname']).'", "'.mysqli_real_escape_string($mysql_connect, $_POST['language']).'", "'.mysqli_real_escape_string($mysql_connect, $_POST['group']).'")';

								if(mysqli_query($mysql_connect, $insertSQL))
									echo '<p class="medium bold green">'.lang('success').'</p>';
								else
									echo '<p class="red medium">'.lang('failedsaving').'</p>';
							}
						?>
					</div>
				</form>
		</div>
	<?php
			if($_POST['submit'] == true and $fehler == false) {

			}
		}
	?>
</div>
<div class="pda-dash short">
	<h2><?php echo lang('sidebar_title'); ?></h2>
	<div class="pda-main-content">
		<div class="blue big bold center">
			<?php echo lang('datemonth'); ?><br />
			<?php echo lang('time'); ?>
		</div>
	</div>
</div>
