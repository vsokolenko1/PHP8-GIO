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

