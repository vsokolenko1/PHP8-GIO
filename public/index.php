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
    
    if(!is_readable($file)) {
    
        trigger_error("Can't read the file: '$file'", E_USER_NOTICE);
        
    } else {
    
        $transactions = array_merge($transactions, getTransactions($file));
    
    }
    
}
vd($transactions, true);
    
    $totals = calculateTotals($transactions);
    
    
    exit();
    
//    if(!is_null($data)) {
//        
//        $storage = [];
//        
//        //3. Transform & sava data to storage.
//        $storage['transactions']   = saveData($data);
//        
////vd($storage['transactions'],false);
//
//        //4. Calculate different summ.
//        $storage['income']         = getInfo($storage['transactions'], 'income');
//        $storage['expense']        = getInfo($storage['transactions'], 'expense');
//        $storage['profit']         = round($storage['income'] - abs($storage['expense']), 2, PHP_ROUND_HALF_DOWN);
//
//    } else {
//        
//        trigger_error("No data in the files", E_USER_WARNING);
//        
//    }
//
//extract($storage);
//vd($profit);
require_once VIEWS_PATH . 'transactions.php';
