<p><?php echo lang('introduction_status'); ?></p>
<div class="status">
	<div><?php echo service('apache2') ?></div>
	<div><?php echo service('postfix') ?></div>
	<div><?php echo service('dovecot') ?></div>
</div>
<p>
	<a href="?module=<?php echo $_GET['module'] ?>&amp;restart=apache2"><?php echo lang('restart') ?> Apache2</a><br />
	<?php if($_GET['restart'] == 'apache2') echo service('apache2', 'restart') ?>
	<a href="?module=<?php echo $_GET['module'] ?>&amp;restart=mysql"><?php echo lang('restart') ?> MySQL</a><br />
	<?php if($_GET['restart'] == 'mysql') echo service('mysql', 'restart') ?>
	<a href="?module=<?php echo $_GET['module'] ?>&amp;restart=postfix"><?php echo lang('restart') ?> Postfix</a><br />
	<?php if($_GET['restart'] == 'postfix') echo service('postfix', 'restart') ?>
	<a href="?module=<?php echo $_GET['module'] ?>&amp;restart=dovecot"><?php echo lang('restart') ?> Dovecot</a><br />
	<?php if($_GET['restart'] == 'dovecot') echo service('dovecot', 'restart') ?>
</p>
