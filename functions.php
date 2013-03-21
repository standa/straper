<?php if (!defined('ROOT')) die('ROOT const not set.');

// functions.php

load_module('module'); // load the general abstract module implementation

bootstrap(); // perform the initial file checking

$dependencies = array(
	'MongoClient'
);

function bootstrap() {
	check_functions($GLOBALS['dependencies']);
}

/**
 * check if the functions in the param exist
 * if they do not exists - the module is not installed, so die
 * serves as basic dependency checking
 */
function check_functions($functions, $die_on_error = true) {
	foreach ($functions as $f) {
		if (!function_exists($f)) {
			if (class_exists($f)) {
				continue;
			}
			msg('check_functions() Error: function '.$f.' does not exist. '.
				'Install the related php module first!');
			if ($die_on_error) {
				exit;
			}
		}
	}
}

function msg($message, $level = 0) {
	echo $message."\n";
}

/**
 * load module from the modules directory
 */
function load_module($module) {
	
	$candidate = ROOT.'/modules/'.$module.'/'.$module.'.php';

	if (!file_exists($candidate)) {
		msg('Module '.$module.' does not exist!');
		return;
	}
	require_once $candidate;
}

/**
 * get the mongo database. Mongo is the main database storage.
 */
function get_mongo($db = null, $collection = null) {
	$m = new MongoClient();
	if ($db && $collection) {
		return $m->{$db}->{$collection};	
	} else if (!$collection) {
		return $m->{$db};
	} else {
		return $m;
	}	
}