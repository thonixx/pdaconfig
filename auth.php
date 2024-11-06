<?php

	/* PDAconfig
	*
	* scripted by Michael Tanner for Schleuniger Montagen AG
	* www.white-tiger.ch
	* www.schleuniger-montagen.ch
	*/
	error_reporting(0);
	session_start();

	// redirect if already logged in
	if($_SESSION['login_session'] > 0) header('Location: ./?module=dash');

	// require once language file
	require_once('data/lang.php');
	// require once mysql config
	require_once('data/mysql.php');
	// require once custom functions
	require_once('data/functions.php');
	// require once classes
	require_once('data/classes.php');

	/* login procedure */
	// only check if submit button was clicked
	if(isset($_POST['login'])) {
		// check for username and password combination and return id if successful and nothing if it failed
		$check_sql = 'SELECT `id` FROM `authentication` WHERE `username` = "'.mysqli_real_escape_string($mysql_connect, $_POST['username']).'" AND `password` = "'.mysqli_real_escape_string($mysql_connect, hash('sha3-512', $_POST['password'])).'"';
		$check_query = mysqli_query($mysql_connect, $check_sql);

		// fetch user id
		$result = mysqli_fetch_assoc($check_query);

		// set to false for failure check
		$failure = false;
		if($result == true) {
			// login success
			// set session cookie
			$_SESSION['login_session'] = $result['id'];
			// redirect
			header('Location: index.php?module=dash');
		} else {
			// login failure
			$failure = true;
		}
	}

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

	<link rel="stylesheet" type="text/css" href="scripts/login.css" />
	<link rel="shortcut icon" href="img/fav.ico" type="image/x-icon" />

	<script type="text/javascript" src="scripts/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="scripts/jquery-effects-core.js"></script>
	<script type="text/javascript" src="scripts/jquery.easing.1.3.js"></script>
	<script type="text/javascript">
	<!--
		$(document).ready(function(){
			// Slidedown effect by possible warning
			$("#warning div").hide();
			$("#warning").hide().slideDown({duration: 1000, easing: "easeOutElastic"});
			window.setTimeout(function(){
				$("#warning div").fadeIn(500);
			}, 1);

			// Slideup after a defined time
			window.setTimeout(function(){
				$("#warning").slideUp({duration: 500, easing: "easeInExpo"});
			}, 6000);
			window.setTimeout(function(){
				$("#warning div").fadeOut(600);
			}, 5800);

			<?php if($failure == true) { ?>
			$("#auth-dialog-content").effect("shake", { times:4 }, 50);
			<?php } ?>
		});

		function detect_caps(e){
		/* Source: http://forums.asp.net/t/1767589.aspx/1?CapsLock */

			e = (e) ? e : window.event;

			var charCode = false;
			if (e.which) {
				charCode = e.which;
			} else if (e.keyCode) {
				charCode = e.keyCode;
			}

			var shifton = false;
			if (e.shiftKey) {
				shifton = e.shiftKey;
			} else if (e.modifiers) {
				shifton = !!(e.modifiers & 4);
			}

			if (charCode >= 97 && charCode <= 122 && shifton) {
				$("#capslock").slideDown({duration: 1000, easing: "easeOutElastic"});
				$("#capslock div").fadeIn(500);
				return true;
			}

			if (charCode >= 65 && charCode <= 90 && !shifton) {
				$("#capslock").slideDown({duration: 1000, easing: "easeOutElastic"});
				$("#capslock div").fadeIn(500);
				return true;
			}

			$("#capslock").slideUp({duration: 500, easing: "easeInExpo"});
			$("#capslock div").fadeOut(600);
			return false;

		}
		// -->
	</script>
</head>

<body onload="javascript:document.getElementById('<?php echo isset($_POST['login']) ? 'p' : 'b' ?>').focus()">
	<div id="auth-wrapper">
		<div id="auth-dialog">
			<div id="auth-dialog-content" class="transparent-box">
				<div id="title"><?php echo lang('logindialogtitle') ?></div>
				<div id="auth-left">
					<?php echo lang('copyright') ?>
				</div>
				<div id="auth-right">
					<form id="frmLogin" name="frmLogin" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
						<div>
							<input type="text" name="username" id="b" class="transparent-box-reverse" onkeypress="detect_caps(event, 'b')" value="<?php echo $_POST['username'] ? $_POST['username'] : lang('usernameinput') ?>" onfocus="if(this.value=='<?php echo lang('usernameinput') ?>')this.value=''"
								onblur="if(this.value=='')this.value='<?php echo lang('usernameinput') ?>'" /><br />
							<input type="password" name="password" id="p" class="transparent-box-reverse" onkeypress="detect_caps(event, 'p')" value="******" onfocus="if(this.value=='******')this.value=''"
								onblur="if(this.value=='')this.value='******'"/><br />
							<br />
							<input type="submit" name="login" id="login" class="transparent-box-reverse" value="<?php echo lang('loginbutton') ?>" />
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- Capslock detection and general warnings here -->
		<div id="capslock" class="transparent-box blue">
			<div><?php echo lang('capslock') ?></div>
		</div>
		<?php if($failure == true) { ?>
		<div id="warning" class="transparent-box blue">
			<div><?php echo lang('warnlogin') ?></div>
		</div>
		<?php } ?>
	</div>
</body>
</html>
