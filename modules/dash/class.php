<?php
	
	// dashboard class
	
	class useradmin {
		// initialize public variables
		
		// initialize private variables
		private $sql = '';
		private $query = '';
		private $userRights = '';
		private $pdaconfigRight = '';
		private $return = '';
		
		public function __construct() {
			global $userPref;
			// load user privileges into private variable for using in other classes
			$this->userRights = $userPref;
			$this->pdaconfigRight = $this->userRights['dash'];
		}
		
		// list everything in a nice table
		public function mkTable() {
			global $modDir;
			global $module;
			
			$this->sql = 'SELECT * FROM `authentication` INNER JOIN `group` on `group`.`id` = `authentication`.`group`';
			$this->query = $this->loadQuery();
			
			// construct the whole table
			$this->return = '<table>';
				$this->return .= '<tr>
									<th style="width: 10%;">ID</th>
									<th style="width: 40%;">'.lang('username').' ('.str_replace(' ', '&nbsp;', lang('realname')).')</th>
									<th style="width: 10%;">'.lang('shortlanguagelabel').'</th>
									<th style="width: 25%;">'.lang('group').'</th>
									<th style="width: 15%;">&nbsp;</th>
								</tr>';
				$counter = 1;
				while($userEntry = mysql_fetch_array($this->query)) {
					$this->return .= '<tr';
					if(is_float($counter/2)) $this->return .= ' class="background"';
					if($userEntry[0] == $_GET['delUser'] or $userEntry[0] == $_GET['editUser']) $this->return .= ' style="color: red;"';
					$this->return .= '>';
						$this->return .= '<td>'.$userEntry[0].'</td>';
						$this->return .= '<td>'.limit($userEntry['username'], 20).' ('.str_replace(' ', '&nbsp;', $userEntry['realname']).')'.'</td>';
						$this->return .= '<td>'.$userEntry['lang'].'</td>';
						$this->return .= '<td>'.limit($userEntry['name'], 15).'</td>';
						$this->return .= '<td>';
						if($this->pdaconfigRight >= 6)
							$this->return .= '<a href="?module='.$_GET['module'].'&amp;editUser='.$userEntry[0].'"><img src="'.$modDir.'edit.png" alt="'.lang('edit').'" /></a> <a href="?module='.$_GET['module'].'&amp;delUser='.$userEntry[0].'"><img src="'.$modDir.'delete.png" alt="'.lang('delete').'" /></a>';
						else $this->return .= '&nbsp;';
						$this->return .= '</td>';
					$this->return .= '</tr>';
					$counter++;
				}
			$this->return .= '</table>';
			
			return $this->return;
		}
		
		// execute mysql query
		private function loadQuery() {
			$this->return = mysql_query($this->sql) or die($this->lang['loadqueryfailed'].' '.mysql_error());
			return $this->return;
		}
	}
	
?>
