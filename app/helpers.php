<?php
declare(strict_types=1);

/**
 * Short view var_dump for testing
 */
function vd(array|string $data, bool $visible = true): void 
{    
    if(!$visible) {
        return;
    }
    
    echo '<pre>';
    
    if(is_array($data)) 
    {    
        var_dump($data);
    } 
    else if (is_string($data))
    {
        echo $data;  
    }
    
    echo '</pre>';
}


function formatDollarAmount(float $amount): string {
    
    $isNegative = $amount < 0;
    
    return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2) ;
    
}

function formatDate(string $date): string {
    
    return date('M j, Y', strtotime($date));
    
}