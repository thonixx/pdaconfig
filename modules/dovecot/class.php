<?php

	// dashboard class

	class mail {
		// initialize private variables
		private $sql = '';
		private $query = '';
		private $userRights = '';
		private $pdaconfigRight = '';
		private $return = '';

		public function __construct() {
			global $dbh;
            $this->mysqli_link = $dbh;
			global $userPref;
			global $_GET;
			// load user privileges into private variable for using in other classes
			$this->userRights = $userPref;
			$this->pdaconfigRight = $this->userRights[$_GET['module']];
		}

		// list everything in a neat table
		public function mkTable() {
			global $modDir;

			$this->sql = 'SELECT * FROM `virtual_users` ORDER BY `domain_id`, `email`';
			$this->query = $this->loadQuery();

			// construct the whole table
			$this->return = '<table style="width: 50%; float: left; margin: 20px 0;">';
				$this->return .= '<tr>
									<th>ID</th>
									<th class="txRight">'.lang('email').'</th>
									<th>'.lang('domain').'</th>
									<th>'.lang('options').'</th>
								</tr>';
				$counter = 1;
				while($entry = mysqli_fetch_array($this->query)) {
					$this->return .= '<tr';
					if(is_float($counter/2)) $this->return .= ' class="background"';
					if($entry[0] == $_GET['delete'] or $entry[0] == $_GET['edit']) $this->return .= ' style="color: red;"';
					$this->return .= '>';
						$this->return .= '<td>'.$entry[0].'</td>';
						$email = chkMail($entry['email']);
						$this->return .= '<td class="txRight">'.$email[1][0].'</td>';
						$this->return .= '<td>@'.$email[5][0].$email[6][0].'</td>';
						$this->return .= '<td>';
						if($this->pdaconfigRight >= 6)
							$this->return .= '<a href="?module='.$_GET['module'].'&amp;edit='.$entry['id'].'#editemail"><img src="'.$modDir.'edit.png" alt="'.lang('edit').'" /></a> <a href="?module='.$_GET['module'].'&amp;delete='.$entry['id'].'#editemail"><img src="'.$modDir.'delete.png" alt="'.lang('delete').'" /></a>';
						else $this->return .= '&nbsp;';
						$this->return .= '</td>';
					$this->return .= '</tr>';
					$counter++;
				}
			$this->return .= '</table>';

			return $this->return;
		}


		// list every domain name in a nice table
		public function mkAliasTable() {
			global $modDir;

			$this->sql = 'SELECT `id`, `source`, `destination` FROM `virtual_aliases` WHERE `source` NOT LIKE `destination` ORDER BY `source`';
			$this->query = $this->loadQuery();

			// construct the whole table
			$this->return = '<table style="width: 50%; float: left; margin: 20px 0;">';
				$this->return .= '<tr>
									<th>ID</th>
									<th>'.lang('source').'</th>
									<th>'.lang('destination').'</th>
									<th>'.lang('options').'</th>
								</tr>';
				$counter = 1;
				while($entry = mysqli_fetch_array($this->query)) {
					$this->return .= '<tr';
					if(is_float($counter/2)) $this->return .= ' class="background"';
					if($entry[0] == $_GET['adelete'] or $entry[0] == $_GET['aedit']) $this->return .= ' style="color: red;"';
					$this->return .= '>';
						$this->return .= '<td>'.$entry['id'].'</td>';
						$this->return .= '<td>';
						if(strpos($entry['source'], '@') == 0) $this->return .= '<em>'.lang('catchallfor').'</em> ';
						$this->return .= $entry['source'].'</td>';
						$this->return .= '<td>'.$entry['destination'].'</td>';
						$this->return .= '<td>';
						if($this->pdaconfigRight >= 6)
							$this->return .= '<a href="?module='.$_GET['module'].'&amp;adelete='.$entry[0].'#editalias"><img src="'.$modDir.'delete.png" alt="'.lang('delete').'" /></a>'; // just a delete function. editing makes no sense.
						else $this->return .= '&nbsp;';
						$this->return .= '</td>';
					$this->return .= '</tr>';
					$counter++;
				}
			$this->return .= '</table>';

			return $this->return;
		}

		public function mkMailForm() {
			global $request_url;
			$this->return = false;

			// to prevent changes if user does not have any rights to edit something here
			if($this->pdaconfigRight < 6 and (isset($_GET['edit']) or isset($_GET['delete']))) return '<p class="red medium">'.lang('nowrite').'</p>';

			// form for adding email accounts
			if(!isset($_GET['edit']) and !isset($_GET['delete'])) {

				// general error warning for implementation
				$errorwarning = '<span class="red">'.lang('mustnotbeempty').'</span>';
				$wrongmail = '<span class="red">'.lang('wrongmail').'</span>';

				$this->return .= '<form id="editemail" style="width: 100%; margin: 25px 0;" class="border" action="'.$request_url.'#editemail" method="post">';
				$this->return .= '<h2>'.lang('add').'</h2>';
				$this->return .= '<div style="padding: 15px 15px 0 15px">';
				$this->return .= lang('email').': ';
				if(isset($_POST['submit']) and empty($_POST['email'])) {$this->return .= $errorwarning;$error = true;} // output of error message
				elseif(isset($_POST['submit']) and chkMail($_POST['email']) == false) {$this->return .= $wrongmail;$error = true;} // output of error message
				$this->return .= '<br /><input type="text" name="email" value="'.$_POST['email'].'" /><br />';
				$this->return .= lang('password').': ';
				if(isset($_POST['submit']) and empty($_POST['password'])) {$this->return .= $errorwarning;$error = true;} // output of error message
				$this->return .= '<br /><input type="password" name="password" value="" /><br />';
				$this->return .= '<br /><input type="submit" name="submit" value="'.lang('savebutton').'" />';

				// save if nothing is missed
				if($error == false and isset($_POST['submit'])) {

					// check if domain already exists and insert if not
					$domain = chkMail($_POST['email']);
					$domain = $domain[4][0].$domain[6][0]; // chain them together for the whole domain

					// query if domain exists
					$domainQuery = mysqli_fetch_assoc(mysqli_query($this->mysqli_link, 'SELECT `id` FROM `virtual_domains` WHERE `name` = "'.$domain.'"'));
					$domainID = $domainQuery['id'];
					// insert if output of former fetch says there is no such domain
					if($domainID == false) mysqli_query($this->mysqli_link, 'INSERT INTO `virtual_domains` (`name`) VALUES ("'.$domain.'")') or die(mysqli_error());

					$realdomainID = $domainID ? $domainID : mysqli_insert_id();

					$query1 = mysqli_query($this->mysqli_link, 'INSERT INTO `virtual_users` (`email`, `domain_id`, `password`) VALUES ("'.mysqli_real_escape_string($this->mysqli_link, $_POST['email']).'", "'.$realdomainID.'", "'.md5($_POST['password']).'" )');

					// check if alias already exists
					$aliasFetch = mysqli_fetch_array(mysqli_query($this->mysqli_link, 'SELECT 1 FROM `virtual_aliases` WHERE `source` LIKE `destination` AND `source` = "'.mysqli_real_escape_string($this->mysqli_link, $_POST['email']).'"'));
					if($aliasFetch[1] != 1) $query2 = mysqli_query($this->mysqli_link, 'INSERT INTO `virtual_aliases` (`domain_id`, `source`, `destination`) VALUES ("'.$realdomainID.'", "'.mysqli_real_escape_string($this->mysqli_link, $_POST['email']).'", "'.mysqli_real_escape_string($this->mysqli_link, $_POST['email']).'" )');

					// output a success notification or an error
					if($query1 and $query2)
						$this->return .= lang('successsaving');
					else
						$this->return .= lang('failedsaving').mysqli_error();
				}

				$this->return .= '</div>';
				$this->return .= '</form>';

				return $this->return;
			}

			// if email was marked for deletion
			if($_GET['delete'] > 0) {
				// query the email address
				$delEmail = mysqli_fetch_assoc(mysqli_query($this->mysqli_link, 'SELECT `email` FROM `virtual_users` WHERE `id` = '.mysqli_real_escape_string($this->mysqli_link, $_GET['reallyDelete'])));
				$delEmail = $delEmail['email'];

				// execute query and put output in variable
				$this->query = mysqli_query($this->mysqli_link, 'DELETE FROM `virtual_users` WHERE `id` = '.mysqli_real_escape_string($this->mysqli_link, $_GET['reallyDelete']));
				$this->query = mysqli_query($this->mysqli_link, 'DELETE FROM `virtual_aliases` WHERE `source` = "'.$delEmail.'" OR `destination` = "'.$delEmail.'"');

				if($this->query == true) $notification = '<span class="green bold">'.lang('success').'</span>';
				else $notification = '<span class="red bold">'.lang('fail').'<br /><br />Error: '.mysqli_error().'</span>';

				if($_GET['reallyDelete'] == $_GET['delete']) {
					$this->return .= '<div id="editemail" style="margin: 25px 0;" class="border">
							<h2>'.lang('reallydelete').'</h2>
							<p style="padding: 0pt 20px;">
								'.$notification.'
							</p>
						</div>';
				} else {
					$this->return .= '<div id="editemail" style="margin: 25px 0;" class="border">
							<h2>'.lang('reallydelete').'</h2>
							<p style="padding: 0pt 20px;">
								<a href="?module='.$_GET['module'].'&amp;delete='.$_GET['delete'].'&amp;reallyDelete='.$_GET['delete'].'#editemail">
									<img src="img/yes.png" alt="Yes"> '.lang('yes').', '.lang('deleteit').'</a><br><br>
								<a href="?module='.$_GET['module'].'">
									<img src="img/no.png" alt="Yes"> '.lang('no').', '.lang('dontdeleteit').'</a>
							</p>
						</div>';
				}

				// return the output
				return $this->return;
			}

			// fetch the choosen email address
			$editValues = mysqli_fetch_assoc(mysqli_query($this->mysqli_link, 'SELECT `email` FROM `virtual_users` WHERE `id` = '.mysqli_real_escape_string($this->mysqli_link, $_GET['edit'])));

			// general error warning for implementation
			$errorwarning = '<span class="red">'.lang('mustnotbeempty').'</span>';

			$this->return .= '<form id="editemail" style="width: 100%; margin: 25px 0;" class="border" action="'.$request_url.'#editemail" method="post">';
			$this->return .= '<h2>'.$editValues['email'].'</h2>';
			$this->return .= '<div style="padding: 15px 15px 0 15px">';
			$this->return .= lang('password').': ';
			if(isset($_POST['submit']) and empty($_POST['password'])) {$this->return .= $errorwarning;$error = true;} // output of error message
			$this->return .= '<br /><input type="password" name="password" value="" /><br />';
			$this->return .= '<br /><input type="submit" name="submit" value="'.lang('savebutton').'" />';

			// save if password is not empty
			if($error == false and isset($_POST['submit'])) {
				// output a success notification or an error
				if(mysqli_query($this->mysqli_link, 'UPDATE `virtual_users` SET `password` = "'.md5($_POST['password']).'" WHERE `id` = '.mysqli_real_escape_string($this->mysqli_link, $_GET['edit'])))
					$this->return .= lang('successsaving');
				else
					$this->return .= lang('failedsaving');
			}

			$this->return .= '</div>';
			$this->return .= '</form>';

			return $this->return;
		}

		// form for editing or deleting aliases
		public function mkAliasForm() {
			global $request_url;
			$this->return = false;

			// to prevent changes if user does not have any rights to edit something here
			if($this->pdaconfigRight < 6 and (isset($_GET['aedit']) or isset($_GET['adelete']))) return '<p class="red medium">'.lang('nowrite').'</p>';

			// form for adding forwarding rules
			if(!isset($_GET['adelete'])) {

				// general error warning for implementation
				$errorwarning = '<span class="red">'.lang('mustnotbeempty').'</span>';
				$wrongmail = '<span class="red">'.lang('wrongmail').'</span>';
				$alreadythere = '<span class="red">'.lang('alreadyrule').'<br /></span>';

				// check if rule already exists
				$checkQuery2 = mysqli_query($this->mysqli_link, 'SELECT `id` FROM `virtual_aliases` WHERE `source` = "'.mysqli_real_escape_string($this->mysqli_link, $_POST['from']).'" AND `destination` = "'.mysqli_real_escape_string($this->mysqli_link, $_POST['to']).'"');
				$checkArray2 = mysqli_fetch_array($checkQuery2);
				$checkifexists2 = $checkArray2[0] > 0 ? true : false;

				$this->return .= '<form id="editalias" style="width: 100%; margin: 25px 0;" class="border" action="'.$request_url.'#editalias" method="post">';
				$this->return .= '<h2>'.lang('add').'</h2>';
				$this->return .= '<div style="padding: 15px 15px 0 15px">';
				$this->return .= lang('frommail').': <br /><em class="small grey">'.lang('catchallinfo').'</em>';
				if(isset($_POST['asubmit']) and empty($_POST['from'])) {$this->return .= '<br />'.$errorwarning;$error = true;} // output of error message
				elseif(isset($_POST['asubmit']) and strpos($_POST['from'], '@') === false) {$this->return .= '<br />'.$wrongmail;$error = true;} // output of error message
				$this->return .= '<br /><input type="text" name="from" value="'.$_POST['from'].'" /><br />';
				$this->return .= lang('tomail').': ';
				if(isset($_POST['asubmit']) and empty($_POST['to'])) {$this->return .= $errorwarning;$error = true;} // output of error message
				elseif(isset($_POST['asubmit']) and chkMail($_POST['to'], 'catchall') == false) {$this->return .= $wrongmail;$error = true;} // output of error message
				$this->return .= '<br /><input type="text" name="to" value="'.$_POST['to'].'" /><br />';
				if(isset($_POST['asubmit']) and $checkifexists2 == true) {$this->return .= $alreadythere;$error = true;} // output of error message
				$this->return .= '<br /><input type="submit" name="asubmit" value="'.lang('savebutton').'" />';

				// save if nothing is missed
				if($error == false and isset($_POST['asubmit'])) {

					// check if domain already exists and insert if not
					$domain = chkMail($_POST['from'], 'catchall');
					$domain = $domain[4][0].$domain[6][0]; // chain them together for the whole domain
					// if domain is empty check if it could be a catchall (like @example.org)
					$chkMailOutput=chkMail($_POST['from'], 'catchall');
					if(empty($domain) && $chkMailOutput != false) {
						$domain = $chkMailOutput[0];
					}
					// check if domain is empty or not
					if(empty($domain)) $this->return .= $wrongmail;

					// query if domain exists
					$domainQuery = mysqli_fetch_assoc(mysqli_query($this->mysqli_link, 'SELECT `id` FROM `virtual_domains` WHERE `name` = "'.$domain.'"'));
					$domainID = $domainQuery['id'];
					// insert if output of former fetch says there is no such domain
					if($domainID == false) mysqli_query($this->mysqli_link, 'INSERT INTO `virtual_domains` (`name`) VALUES ("'.$domain.'")') or die(mysqli_error());

					$realdomainID = $domainID ? $domainID : mysqli_insert_id();

					$query = mysqli_query($this->mysqli_link, 'INSERT INTO `virtual_aliases` (`source`, `destination`, `domain_id`) VALUES ("'.mysqli_real_escape_string($this->mysqli_link, $_POST['from']).'", "'.mysqli_real_escape_string($this->mysqli_link, $_POST['to']).'", "'.$realdomainID.'")');

					// output a success notification or an error
					if($query)
						$this->return .= lang('successsaving');
					else
						$this->return .= lang('failedsaving').mysqli_error();
				}

				$this->return .= '</div>';
				$this->return .= '</form>';

				return $this->return;
			}

			// if email was marked for deletion
			if($_GET['adelete'] > 0) {
				// execute query and put output in variable
				$this->query = mysqli_query($this->mysqli_link, 'DELETE FROM `virtual_aliases` WHERE `id` = "'.mysqli_real_escape_string($this->mysqli_link, $_GET['reallyDelete']).'"');

				if($this->query == true) $notification = '<span class="green bold">'.lang('success').'</span>';
				else $notification = '<span class="red bold">'.lang('fail').'<br /><br />Error: '.mysqli_error().'</span>';

				if($_GET['reallyDelete'] == $_GET['adelete']) {
					$this->return .= '<div id="editalias" style="margin: 25px 0;" class="border">
							<h2>'.lang('reallydelete').'</h2>
							<p style="padding: 0pt 20px;">
								'.$notification.'
							</p>
						</div>';
				} else {
					$this->return .= '<div id="editalias" style="margin: 25px 0;" class="border">
							<h2>'.lang('reallydelete').'</h2>
							<p style="padding: 0pt 20px;">
								<a href="?module='.$_GET['module'].'&amp;adelete='.$_GET['adelete'].'&amp;reallyDelete='.$_GET['adelete'].'#editemail">
									<img src="img/yes.png" alt="Yes"> '.lang('yes').', '.lang('deleteit').'</a><br><br>
								<a href="?module='.$_GET['module'].'">
									<img src="img/no.png" alt="Yes"> '.lang('no').', '.lang('dontdeleteit').'</a>
							</p>
						</div>';
				}

				// return the output
				return $this->return;
			}
		}

		// execute mysql query
		private function loadQuery() {
			global $dbh; // to use the second mysql connection for the mailserver
			$this->return = mysqli_query($this->mysqli_link, $this->sql) or die($this->lang['loadqueryfailed'].' '.mysqli_error());
			return $this->return;
		}
	}

?>
