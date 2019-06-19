<?php
/*
Plugin Name: WordpressActionsLogs
Plugin URI: https://github.com/golendercaria/
Description: Plugin logs all of action from user and display it
Version: 0.1
Author: Yann Vangampelaere
Author URI: https://github.com/golendercaria/
License: MIT
*/
if( !class_exists("gol_wordpressActionsLogs")){
	include "class.WordpressActionsLogs.php";
	new gol_wordpressActionsLogs();
}