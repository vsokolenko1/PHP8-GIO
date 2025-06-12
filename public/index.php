<?php

declare(strict_types = 1);

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);

require_once APP_PATH . 'App.php';

//Extensions our files
$ext = 'csv';

//Array for store transactions
$storage = array_fill_keys(['transactions', 'income', 'expense', 'profit'], 0);

$data = run($storage, $ext);

extract($data);
//vd($profit);
require_once VIEWS_PATH . 'transactions.php';
