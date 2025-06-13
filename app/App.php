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

function getTransactions(string $file, ?callable $transactionHandler= null): ?array{
    
    
    if(!file_exists($file)) {
        
        trigger_error("Can't find the file: '$file'", E_USER_NOTICE);
        
        return null;
    }
    
    if(!is_readable($file)) {
    
        trigger_error("Can't read the file: '$file'", E_USER_NOTICE);
        
        return null;
        
    }  

    $fh = fopen($file, 'r');

    fgetcsv($fh);
    
    while (($transaction = fgetcsv($fh)) !== false) {
        
        if($transactionHandler !== null) {
        
            $transactions[] = $transactionHandler($transaction);
        
        }
        
    }

    return $transactions;
}

function extractTransaction (array $transactionRow): array {
    
    [$date, $check, $description, $amount] = $transactionRow;
    
    $amount = (float) str_replace (['$', ','], '', $amount);
    
    return [
        'date'          =>  $date,
        'check'         =>  $check,
        'description'   =>  $description,
        'amount'        =>  $amount
    ];
    
}

function calculateTotals(array $transactions): array {
    
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];
    
    foreach ($transactions as $transaction) {
        
        $totals['netTotal'] += $transaction['amount'];
        
        if ($transaction['amount'] >= 0 ) {
            
            $totals['totalIncome'] +=$transaction['amount'];
            
        } else {
            
            $totals['totalExpense'] +=$transaction['amount'];
            
        }
        
    }
    
    return $totals;
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