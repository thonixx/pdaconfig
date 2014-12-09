<?php
	
	/* PDAconfig
	 * 
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */
	
	// read user preferences
	class userPref {
		// initialize private variables
		private $sql = false;
		private $query = false;
		private $user = false;
		private $requestedUser = false;
		private $lang = false;
		private $return = false;
		private $array = false;
		
		// initialize public variables
		public $pref = array();
		
		// function executing by calling class for the first time
		public function __construct($userRequest = '') {
			// to use languages
			global $language;
			
			$this->lang = $language[parse_lang()];
			$this->user = $userRequest > 0 ? $userRequest : $_SESSION['login_session'];
		}
		
		// read user data
		public function readData() {
			$this->sql = 'SELECT
						*
					FROM
						`authentication`
					INNER JOIN
						`group` on `group`.`id` = `authentication`.`group`
					WHERE
						`authentication`.`id` = "'.$this->user.'"';
			$this->return = $this->loadQuery();
			$this->result = mysql_fetch_assoc($this->return);
			
			// parse rights into user array
			$this->sql = 'SELECT
						`rights`.`module`,
						`rights`.`access`
					FROM
						`rights`, `authentication`
					WHERE
						`authentication`.`id` = "'.$this->user.'" AND `rights`.`group` = `authentication`.`group`';
			$this->return = $this->loadQuery();
			while($array = mysql_fetch_array($this->return)) {
				$this->result[$array['module']] = $array['access'];
			}
			
			$this->pref = $this->result;
		}
		
		// execute mysql query
		private function loadQuery() {
			$this->return = mysql_query($this->sql) or die($this->lang['loadqueryfailed'].' '.mysql_error());
			return $this->return;
		}
	}
	// read user preferences
	
	
	// all about basics of modules
	class module {
		// initialize private variables
		private $sql = false;
		private $query = false;
		private $lang = false;
		
		// initialize public variables
		public $listArray = array();
		public $listRequires = array();
		public $listAttributes = array();
		public $dir = false;
		
		public function __construct($dir = '', $module) {
			$this->dir = 'modules';
		}
		
		// list all modules and put into array with config
		public function listModules() {
			// to use languages
			global $language;
			$this->lang = $language[parse_lang()];
			
			// only open when directory really exists
			$handler = file_exists($this->dir) ? opendir($this->dir) : die($this->lang['nodirectory']);
			
			while (false !== ($entry = readdir($handler))) {
				if(preg_match('/^[m][o][d][_][0-9][A-Za-z]{3,}[.][p][h][p]$/', $entry)) {
					include($this->dir.'/'.$entry);
					if($config['innav'] == true) $this->listArray[$entry] = $config;
					$this->listRequires[$config['name']] = $requires;
					$this->listAttributes[$config['name']] = $config;
				}
				ksort($this->listArray);
			}
		}
	}
	// all about basics of modules
	
?>
