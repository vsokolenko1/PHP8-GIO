<?php

declare(strict_types = 1);

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);

require_once APP_PATH . 'App.php';
require_once APP_PATH . 'helpers.php';

set_error_handler('errorHandler', E_ALL);

//1. Get file lists in directory 
$files = getTransactionsFiles(FILES_PATH);

//2. Get transactions from all files.
$transactions = [];

foreach ($files as $file) {
    
    if(!is_null($transaction = getTransactions($file, 'extractTransaction'))) {
        
        $transactions = array_merge($transactions, $transaction);
        
    }
 
}
//vd($transactions, true);
    
$totals = calculateTotals($transactions);
    
extract($totals);
    //formatDate
    //formatDollarAmount
require_once VIEWS_PATH . 'transactions.php';
