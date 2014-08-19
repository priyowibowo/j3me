<?php

/**
 * @version     $Id: notification 2012-02-14 15:30:59 priyowibowo $
 * @subpackage  com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// no direct access
defined('_JEXEC') or die;
setlocale(LC_ALL, 'da_DK');

$month = "";
$table = "<table class=group-travel>";
foreach($this->items as $k => $v){
    
    $row = $k%2;
    $curmonth = ucfirst(strftime("%B %Y", strtotime(date("F" , strtotime($v->outbound_depart_date)))));
        
    $seats = '';
    if($v->seats_display != ''){
        $seats = $v->seats_display;
    } else {
       $seats = ($v->seats) ? $v->seats." ".JText::_("COM_TRAVELSEARCH_SEATS") : JText::_("COM_TRAVELSEARCH_SOLDOUT");
    }
    
    $currencyfront = '';
    $currencyback = '';
    if($v->currency=='NOK'||$v->currency=='SEK'||$v->currency=='DKK')
        $currencyback = 'Kr.';
    else if($v->currency=='EUR')
        $currencyfront = '€';
    else if($v->currency=='GBP')
        $currencyfront = '£';       
    
    if($month!=$curmonth){
        // nu table 
        $table .= "<tr>
                    <td colspan=6 class=gt-white><strong>".$curmonth."</strong></td>
                </tr>
                ";
        
        $month = $curmonth;  
        
    } 
    
    // add row
    $table .= "<tr class=gt-".$row.">
                    <td class=gt-td width=15%><a href=".JRoute::_('index.php?option=com_zoo&task=item&item_id='.$v->package->zooitem_id."&gtr=".base64_encode($k)).">".$v->zoo_data['image']['Main Image']."</a></td>
                    <td class=gt-td width=30%><strong><a href=".JRoute::_('index.php?option=com_zoo&task=item&item_id='.$v->package->zooitem_id."&gtr=".base64_encode($k)).">".$v->package->product_name."</a></strong><br>".$v->package->intro."</td>
                    <td class=gt-td>".$v->days." ".JText::_("COM_TRAVELSEARCH_DAY")."</td>
                    <td class=gt-td>".date("d/m/Y", strtotime($v->outbound_depart_date))."</td>
                    <td class=gt-td>".$seats."</td>
                    <td class=gt-td><strong><a href=".JRoute::_('index.php?option=com_zoo&task=item&item_id='.$v->package->zooitem_id."&gtr=".base64_encode($k)).">".$currencyfront." ".number_format($v->room_price_fortwo_perperson, 0, '.', '.').",- ".$currencyback."</a></strong></td>
                </tr>";
}

$table .= "</table>";

echo $table;
?>