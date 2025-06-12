<?php

/**
 * Зробити задачу, а потім зробити обробку помилок, а потім пройтися по темах
 * і переписати все з використанням тем типу анонімні функції і так далі.
 */

declare(strict_types = 1);

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

function run(array $storage, string $ext): array {
    
    //1. Get file lists in directory
    $files = getFileNames(FILES_PATH, $ext);
//vd($files, false);
    //if no files throw error
    if(!$files) {

        trigger_error("No founded files with extensitons \"$ext\"", E_USER_ERROR);
    }

    //2. Get data from all files.
    $data = readAllFiles(FILES_PATH, $files);
//vd($data, false);
    if(!is_null($data)) {

        //3. Transform & sava data to storage.
        $storage['transactions']   = saveData($data);
//vd($storage['transactions'],false);
        //4. Calculate different summ.
        $storage['income']         = getInfo($storage['transactions'], 'income');
        $storage['expense']        = getInfo($storage['transactions'], 'expense');
        $storage['profit']         = round($storage['income'] - abs($storage['expense']), 2, PHP_ROUND_HALF_DOWN);

    } else {
        
        trigger_error("No data in the files", E_USER_WARNING);
        
    }
    
//vd($storage, false);    
    return $storage;
    
}

/**
 * Get list filename by path
 * 
 * @param string $path
 * @param string $ext
 * @return array|null
 */
function getFileNames(string $path, string $ext = 'txt'): ?array {
    
    if (!is_dir($path)) {
        
        trigger_error("This is not a directory \"$path\"", E_USER_ERROR);
        
    }
    
    if(false === $dh = opendir($path)) {
          
        trigger_error("Can't open the dir \"$path\"", E_USER_ERROR);
        
    }
    
    $files = [];
        
    while (false !== $fname = readdir($dh)) {
            
        if($fname === '.' || $fname === '..') {
                
            continue;
                
        }
                    
        $arr = explode('.', $fname);
        $fExt = $arr[count($arr)-1];
            
        if($ext === $fExt) {
            
            $files[] = $fname;
            
        }
            
    }

    closedir();

    return $files ?? null;
}

/**
 * Reading files from path
 * 
 * @param string $path
 * @param array $files
 * @return array|null
 */
function readAllFiles (string $path, array $files): ?array {
    
    $transactions = [];
    
    foreach ($files as $file) {
        
        //2.1 read each file and add to $transactions.
        $data = __readFile($path, $file);
        
        if(!is_null($data)) {
        
            $transactions[] = $data;
        }
        
    }

    if(!is_array($transactions) || empty($transactions)) {
        return null;
    }
    
    //Return transactions from all files by the $path
    return array_merge(...$transactions);
}

/**
 * Read file
 * 
 * Read file and broken on array & remove 1 line (header) or empty element.
 * 
 * @param string $filePath
 * @return array|null
 */
function __readFile(string $path, string $file): ?array {

    $filePath = $path . $file;
//    echo $path, PHP_EOL, $file, $filePath, filesize('/var/www/STUDY/PHP8GIO/section1/practice/transaction_files/sample_1_1.csv');
    if(! $fh = fopen($filePath, 'r')) {
        
        trigger_error("Can't read the file  \"$file\"", E_USER_WARNING);
        
    }

    $fsize = filesize($path . $file);

    if($fsize === 0) {
        
        trigger_error("Empty file  \"$file\"", E_USER_NOTICE);
        
        fclose($fh);
        return null;
    }
    
    $content = fread($fh, $fsize);
    
    //broken content to array & remove 1 line with header.
    $lines = array_slice(explode(PHP_EOL, $content), 1);

    fclose($fh);
    
    //remove empty elements (last element)
    return array_filter($lines);
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

function __errorHandler(
        int $type,
        string $message,
//        ?string $file = null,
//        ?int $line = null,
) {
    
//    echo "$type: $message in $file on line $line";
    echo "$type: $message";
    
    switch ($type) {
        case E_USER_ERROR:
            exit();
        case E_USER_WARNING:
            break;
        case E_USER_NOTICE:
            break;            
        default:
            return false;
    }
    
    return;
}

set_error_handler('__errorHandler', E_ALL);

function vd($array, $visible = true) {
    
    if($visible) {
        echo '<pre>';
        var_dump($array);
        echo '</pre>';
    }
    
}
