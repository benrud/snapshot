<!--If youre not Kyle or Cosme, get out of this file... dont touch it... dont execute it... dont play with it... this literally can kill the server...-->
<!--Either you understand how to use a terminal or not, get out... even if you think you know what youre doing-->
<?php
	//$command = escapeshellcmd('./python/index.py');
	//cat ./backend/content.php &
	$command = "cat ../socialnetwork1/resources/backend/user.php";
	$output = shell_exec($command);
	echo '<pre>' . $output; ?>

	

