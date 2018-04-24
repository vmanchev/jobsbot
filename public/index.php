<?php

error_reporting(false);

require_once realpath(__DIR__ . '/../vendor/autoload.php');

$dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/../'));
$dotenv->load();

$keywords = ['angular', 'angularjs', 'frontend', 'php'];

$bot = new Toxic\Jobs\Scraper($keywords);
$bot->search();

