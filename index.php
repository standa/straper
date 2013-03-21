<?php

/**
 * StRaper
 *
 * Standa's web scraper and web spider and web watcher
 *
 * Long term goal:
 *
 * This programme is meant to download and parse files from the internet
 * in whatever format and save them to the database.
 * 
 * Short term goal:
 *
 * - download a file from my online banking with the currency exchange rates;
 * - save it into mongo for further processing.
 * 
 */

define('ROOT', dirname(__FILE__));

require_once ROOT.'/conf.php'; // secret config infos with passwords etc.

require_once ROOT.'/functions.php';


load_module('fio');

$fio = new Fio($conf->fio->username, $conf->fio->password);

for ($i = )
$fio->exchange_rates(date('d.m.Y'));
