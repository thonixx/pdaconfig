<?php

	/* PDAconfig MODULE class file
	 *
	 * scripted by Michael Tanner for Schleuniger Montagen AG
	 * www.white-tiger.ch
	 * www.schleuniger-montagen.ch
	 */

	// this is the oop class for easy editing virtual host configuration for apache
	class apache {
		public $mainConfigFile = '/etc/apache2/apache2.conf';
		public $customConfigFile = '/etc/apache2/http.conf';
		public $virtualhostFile = '/etc/apache2/pdaconfig/virtualhosts';

		private $perm = ''; // used to set the permission
		private $hint = '# pdaconfig include';
		private $include = 'Include pdaconfig/';
		private $vhosts = array();
		private $query = '';
		private $sql = '';
		private $fetch = '';
		private $fail = '';
		private $success = '';

		// construction function
		public function __construct() {
			global $userPref;
            global $mysql_connect;
            $this->mysqli_link = $mysql_connect;

			$this->checkConfig();
			$this->checkDatabase();

			// set messages
			$this->fail = '<span class="red medium bold">'.lang('fail').'</span>';
			$this->success = '<span class="green medium bold">'.lang('success').'</span>';

			// load user privileges into private variable for using in other functions
			$userRights = $userPref;
			$this->perm = $userRights[$_GET['module']];
		}

		private function checkDatabase() {
			// creates tables if they do not exist
			// 253 chars for a full domain name is maximum so i set the maximum to 253
			// basic virtualhost table
			mysqli_query($this->mysqli_link, 'CREATE TABLE IF NOT EXISTS `apache_vhost` (
							`servername` VARCHAR(253) NOT NULL,
							`documentroot` VARCHAR(300) NOT NULL,
							PRIMARY KEY (`servername`)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1;') or die(mysqli_error());
			// server alias table
			mysqli_query($this->mysqli_link, 'CREATE TABLE IF NOT EXISTS `apache_aliases` (
							`servername` VARCHAR(253) NOT NULL,
							`serveralias` VARCHAR(300) NOT NULL,
							PRIMARY KEY (`serveralias`),
							CONSTRAINT `apache_vhost`
							FOREIGN KEY (`servername`)
							REFERENCES `apache_vhost`(`servername`)
								ON UPDATE CASCADE ON DELETE CASCADE
						) ENGINE=InnoDB;') or die(mysqli_error());
			// custom config table
			mysqli_query($this->mysqli_link, 'CREATE TABLE IF NOT EXISTS `apache_custom` (
							`servername` VARCHAR(253) NOT NULL,
							`option` TEXT NULL,
							INDEX (`servername`),
							FOREIGN KEY (`servername`)
							REFERENCES `apache_vhost`(`servername`)
								ON UPDATE CASCADE ON DELETE CASCADE
						) ENGINE=InnoDB;') or die(mysqli_error());
		}

		private function checkConfig() {
			// check config file for hint of custom include option
			// read content
			$content = file_get_contents($this->mainConfigFile);

			// check for hint
			if(strpos($content, $this->hint) === false) {
				// note:
				// sudo chown root:www-data /etc/apache2/apache2.conf
				// sudo chmod 771 /etc/apache2/apache2.conf
				// include hint and "include" option
				$fp = fopen($this->mainConfigFile, 'a');
				fwrite($fp, $this->hint."\n".$this->include);
				fclose($fp);
			}

			// create directory if it does not exist
			if(!file_exists('/etc/apache2/pdaconfig/')) {
				// for this you have to change some permissions with the following commands
				// 1. sudo chown root:www-data /etc/apache2
				// 2. sudo chmod 771 /etc/apache2
				//shell_exec('mkdir bla');
				mkdir('/etc/apache2/pdaconfig', 0771);
			}
		}

		// adding a domain with options if requested
		public function addDomain($domain = '', $docroot = '', $alias = '', $custom = '') {

			// prevents from adding something if user has no sufficient permissions
			if($this->perm < 6) return lang('insufficient');

			// return error if domain or documentroot is missing
			if(empty($domain) or empty($docroot)) return lang('hintmissing');

			// generate the sql statement for the database
			$this->sql = 'INSERT INTO `apache_vhost` (
											servername,
											documentroot
												) VALUES (
											"'.mysqli_real_escape_string($this->mysqli_link, $domain).'",
											"'.mysqli_real_escape_string($this->mysqli_link, $docroot).'"
												)';
			$this->loadQuery();
			if(mysqli_error() == true) return '<span class="red medium bold">'.lang('fail').' (Duplicate?)</span>';

			if($alias[0] == true) {
				// generate the sql statement for the database
				foreach($alias as $serveralias) {
					$this->sql = 'INSERT INTO `apache_aliases` (
								servername,
								serveralias
									) VALUES (
								"'.mysqli_real_escape_string($this->mysqli_link, $domain).'",
								"'.mysqli_real_escape_string($this->mysqli_link, $serveralias).'"
									)';
					$this->loadQuery();
					if(mysqli_error() == true) return $this->fail;
				}
			}

			if($custom == true) {
				// generate the sql statement for the database
				$this->sql = 'INSERT INTO `apache_custom` (
												servername,
												`option`
													) VALUES (
												"'.mysqli_real_escape_string($this->mysqli_link, $domain).'",
												"'.mysqli_real_escape_string($this->mysqli_link, $custom).'"
													)';

				$this->loadQuery();
				if(mysqli_error() == true) return $this->fail;
			}

			return $this->success;
		 }

		 public function rmDomain($domain = '') {
			// prevents from adding something if user has no sufficient permissions
			if($this->perm < 6) return lang('insufficient');

			// return error if domain or documentroot is missing
			if(empty($domain)) return lang('hintmissing');

			// delete it! thanks to the constraint and foreign keys everything will be deleted
			$this->sql = 'DELETE FROM `apache_vhost` WHERE `servername` = "'.mysqli_real_escape_string($this->mysqli_link, $domain).'"';

			$this->loadQuery();
			return mysqli_error() == true ? $this->fail : $this->success;
		}

		// this function parses all database values into hard coded configuration files for apache
		public function rebuild() {
			global $moduleOverview;
			// remember to use $this->virtualhostFile

			$br = "\n";
			$t = "\t";
			$vhost = '# this file was generated by using PDAconfig'.$br;
			$vhost .= '# apache module written by '.$moduleOverview->listAttributes['apache']['author'].$br.$br;
			$vhost .= 'NameVirtualHost *'.$br.$br;

			$this->sql = 'SELECT * FROM apache_vhost';
			$query = mysqli_query($this->mysqli_link, $this->sql);

			while($vhosts = mysqli_fetch_array($query)) {
				$vhost .= '<VirtualHost *>'.$br;
				$vhost .= $t.'ServerName '.$vhosts['servername'].$br;

				// parse server aliases if available
				$this->sql = 'SELECT * FROM `apache_aliases` WHERE `servername` = "'.$vhosts['servername'].'"';
				$query2 = mysqli_query($this->mysqli_link, $this->sql);

				while($aliases = mysqli_fetch_array($query2)) {
					$vhost .= $t.'ServerAlias '.$aliases['serveralias'].$br;
				}

				// parse the document root folder
				$vhost .= $br.$t.'DocumentRoot '.$vhosts['documentroot'].$br;

				// parse custom configuration
				$this->sql = 'SELECT * FROM `apache_custom` WHERE `servername` = "'.$vhosts['servername'].'"';
				$query3 = mysqli_query($this->mysqli_link, $this->sql);

				while($custom = mysqli_fetch_array($query3)) {
					$vhost .= $br.$custom['option'].$br;
				}

				$vhost .= '</VirtualHost>'.$br.$br;
			}

			file_put_contents($this->virtualhostFile, $vhost); // save config to file

			if(strpos(shell_exec('sudo apache2ctl configtest 2>&1'), 'Syntax OK') === 0 or strpos(shell_exec('sudo apache2ctl configtest 2>&1'), 'Syntax OK') > 0) {
				// workaround of two if statements because == true didnt do the trick alone. and >0 too because it could be at beginning, so 0
				$success = service('apache2', 'restart');
			} else {
				$success == false;
			}

			return $success == true ? $this->success : '<br />'.$this->fail.'<br />'.lang('checkconfig').'<p class="status">'.nl2br(shell_exec('sudo apache2ctl configtest 2>&1')).'</p>';
		}

		 // load a query and output an error message by some error
		 private function loadQuery($fetch = true) {
			 // output an error if sql statement is missing
			 if($this->sql == false) die(lang('loadqueryfailed'));

			 $this->query = false;
			 $this->query = mysqli_query($this->mysqli_link, $this->sql);
			 // only fetch if required
			 if($fetch == true) $this->fetch = mysqli_fetch_array($this->query);
			 else $this->fetch = false;
			 // reset sql variable to avoid double queries
			 $this->sql = false;
		 }
	}

?>
