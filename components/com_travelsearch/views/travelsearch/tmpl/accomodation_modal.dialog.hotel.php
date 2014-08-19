<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

foreach($this->zoo_data as $key => $element){
    // ||$key=='gallery' temporary removed
    if($key=='textarea'||$key=='text'||$key=='image'){
        foreach($element as $k){
            echo $k;
        }
    } 
}
?>


