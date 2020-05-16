<?php

	/* PDAconfig
	*
	* scripted by Michael Tanner for Schleuniger Montagen AG
	* www.white-tiger.ch
	* www.schleuniger-montagen.ch
	*/

	// to check required time to provide the html file
	$performancetime = microtime();

	error_reporting(4096);
	error_reporting(E_ERROR);
	session_start();

	// log out function
	if($_GET['go'] == 'away') session_unset();

	// redirect if not yet logged in
	if($_SESSION['login_session'] == false) header('Location: auth.php');

	// require once mysql config
	require_once('data/mysql.php');
	// require once custom functions
	require_once('data/functions.php');
	// require once classes
	require_once('data/classes.php');

	// parse the requsted url
	$request = $_SERVER['REQUEST_URI'];
	$request_url = htmlentities($request);

	// parse users preferences
	$user = new userPref();
	$user->readData();
	$userPref = $user->pref;

	// initialize module class
	$moduleOverview = new module();
	//$moduleOverview->dir = 'modules'; // Here you could define your own modules folder
	$moduleOverview->listModules();
	$module_array = $moduleOverview->listArray;

	// require language file
	require('data/lang.php');

	// include language files of modules
	foreach($module_array as $module) {
		if(file_exists($moduleOverview->dir.'/'.$module['name'].'/'.'lang.php')) require_once($moduleOverview->dir.'/'.$module['name'].'/'.'lang.php');
		else die(lang('nolanguage').' '.$module['name']);
	}
	//define module directory
	$modDir = $moduleOverview->dir.'/'.$_GET['module'].'/';
	// require language file again to prevent language string overriding by modules
	require('data/lang.php');

	// sets default page to module dash
	if(!isset($_GET['module'])) header('Location: ?module=dash');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo lang('pdaconfigtitle') ?></title>

	<meta http-equiv="content-language" content="en" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache-Control" content="no-cache" />

	<link rel="stylesheet" type="text/css" href="scripts/layout.css" />
	<?php
		// generate navigation structure from modules files
		foreach($module_array as $module) {
			echo '<link rel="stylesheet" type="text/css" href="'.$moduleOverview->dir.'/'.$module['name'].'/'.'style.css" />'."\n";
		}
	?>
	<link rel="shortcut icon" href="img/fav.ico" type="image/x-icon" />

	<script type="text/javascript" src="scripts/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="scripts/jquery-effects-core.js"></script>
	<script type="text/javascript" src="scripts/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="scripts/default.js"></script>
</head>

<body>
	<div id="pda-wrapper">
		<div id="pda-nav">
			<h1 id="header"><a href="./"><?php echo lang('software') ?></a></h1>
			<div id="nav">
				<?php
					// generate navigation structure from modules files
					foreach($module_array as $module) {
						if($userPref[$module['name']] > 0) {
							echo '<a';
							if($_GET['module'] == $module['name']) echo ' class="chosen"';
							echo ' href="?module=';
							echo $module['name'];
							echo '"><img src="';
							echo $moduleOverview->dir.'/'.$module['name'].'/'.$module['name'];
							echo '.png" alt="';
							echo $module['name'];
							echo '" /> <span>';
							echo $module['nav'];
							echo '</span></a>';
						}
					}
				?>
				<a class="logout" href="?go=away"><img src="img/logout.png" alt="Logout" /><?php echo lang('logout') ?></a>
			</div>
			<div class="clear"></div>
		</div>
		<?php

			// include additional files for requested module via $requires
			foreach($moduleOverview->listRequires[$_GET['module']] as $file) {
				$additionalFile = $moduleOverview->dir.'/'.$_GET['module'].'/'.$file.'.php';
				if(file_exists($additionalFile) and $userPref[$_GET['module']] > 0) require_once($additionalFile);
			}

			// include requested module
			$module_location = $moduleOverview->dir.'/'.$_GET['module'].'/index.php';
			$accessdenied = 'module/denied.php';
			if(file_exists($module_location) and !isset($_GET['request'])) include(( $userPref[$_GET['module']] > 0 ? $module_location : $accessdenied ));
			elseif(isset($_GET['request']) and file_exists($modDir.$_GET['request'].'.php')) include $modDir.$_GET['request'].'.php';
			else echo '<div class="pda-content tworow"><h2>#404 &gt; '.lang('nomoduletitle').'</h2><div class="pda-main-content red medium">'.lang('nomodule').'</div></div><div class="pda-content short"><h2>'.lang('nomoduletitleshort').'</h2><div class="pda-main-content red"><p>'.lang('recommendation').'</p></div></div>';

		?>
		<div class="clear"></div>
		<div id="pda-footer">
			<div>
				<?php echo lang('copyright') ?>
			</div>
		</div>
	</div>
</body>
</html>
<!-- <?php
	// Performance calculation
	echo round((microtime()-$performancetime)*1000, 1);
?> ms -->
