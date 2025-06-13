<?php

/**
 * +Зробити задачу, +а потім зробити обробку помилок, а потім пройтися по темах
 * і переписати все з використанням тем типу анонімні функції і так далі.
 */

declare(strict_types = 1);

function getTransactionsFiles (string $path): array {
    
    if(!is_dir($path)) {
        trigger_error("Can't open the path: '$path'", E_USER_ERROR);
    }
    
    if(!is_readable($path)) {
        trigger_error("Can't open the directory, permission denied: '$path'", E_USER_ERROR);
    }
    
    $files = [];
    
    foreach (scandir($path) as $file) {
        
        if(is_dir($file)) {
            continue;
        }

        $files[] = $path . $file;
                
    }
    
    if(empty($files)) {

        trigger_error('No founded transactions files', E_USER_ERROR);
        
    }    
    
    return $files;
    
}

function getTransactions(string $file): array{
    

    $fh = fopen($file, 'r');

    fgetcsv($fh);
    
    while (($transaction = fgetcsv($fh)) !== false) {
        
        $transactions[] = $transaction;
        
    }

    return $transactions;
}

function calculateTotals(array $transactions): array {
    
    
    return [];
}

/**
 * Save data with formatted fields
 * @param array $transactions
 * @return array|null
 */
function saveData (array $transactions): array {
    
    $keys = ['date', 'check', 'description', 'amount'];
    
    $data = [];
    
    foreach ($transactions as $row) {

        //here need formatted amount- remove $ and , in amount value.
        //todo change to preg_replace_callback_array.
        $temp = explode('$', $row);
        $temp[1] = str_replace([','], '', $temp[1]);
        $row = implode('', $temp);
        
        //remove "" in amount
        $row = str_replace('"', '', $row);
        
        $data[] = array_combine($keys, explode(',', $row));

    }
    
    return $data;
}

/**
 * Get sum of income, expense, profit
 * @param array $data
 * @param string|bool $operation
 * @return float
 */
function getInfo(array $data, string|bool $operation = false): float {
    
    switch ($operation){
        
        case 'income':

            $data = array_filter($data, function($var){

                return $var['amount'] > 0 ? $var['amount'] : null;

            });
            break;
        
        case 'expense':
            
            $data = array_filter($data, function($var){

                return $var['amount'] < 0 ? $var['amount'] : null;

            });
            break;
        default :
            break;
   
    }
    
    return round(__getSum($data), 2, PHP_ROUND_HALF_DOWN);
}

function serviceTransformAmount(float $num): string {
    
    return $num < 0 ? '-$' . abs($num) : '$' . $num;
}

/**
 * Get sum inner column of arrays
 * @param array $array
 * @return float
 */
function __getSum (array $array): float {
    
    $summ = 0;
    
    foreach ($array as $row) {
        $summ += $row['amount'];
    }
    
    return $summ;    
}

function errorHandler(
        int $type,
        string $message,
) {
    echo "$type: $message";
    
    switch ($type) {
        
        case E_USER_NOTICE:
        case E_USER_WARNING:
            break;
        
        case E_USER_ERROR:
            exit();        
        default:
            return false;
    }
    
    return;
}