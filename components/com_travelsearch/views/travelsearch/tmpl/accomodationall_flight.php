<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<div id="tabletop">
    <h5 class="h5top"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTINFO");?></h5>
<!-- departure -->
<table width="100%" class="flight">
    <?php
        foreach($this->departure['flight'] as $key => $value){ 
            if(is_object($value)){
                $theader = "<th>".JText::_("COM_TRAVELSEARCH_FLIGHTDEPARTURE")."</th>";
                $depart = explode("T", $value->departureTime);
                $arivedest = explode("T", $value->arrivalTime);
                $theader .= "<th>".ApinorwegianHelper::formatDisplayDate($depart[0])."</th>";
                echo $theader;
            }
        }
    ?>
    </tr>
        <tr>
            <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTDEPART");?></th>
            <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTARRIVE");?></th>
            <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTCODE");?></th>
            <!--th class="head">Available Seats</th-->
        </tr>
    </thead>  
    <?php 

        $tbody = "<tbody>";
        foreach($this->departure['flight'] as $key => $value){
        if(is_object($value)){
            $tbody .= "<tr>";
                $tbody .= "<td><a class=tes>".substr($depart[1], 0, 5)."</a> ".$this->departure['city']['displayName']."</td>";
                $tbody .= "<td><a class=tes>".substr($arivedest[1], 0, 5)."</a> ".$this->arrival['city']['displayName']."</td>";
                $tbody .= "<td>".$value->flightCode."</td>";
//                if(is_object($value->bookingClasses)){
//                    $tbody .= "<td>".$value->bookingClasses->seatsAvailable."</td>";
//                } else {
//                    foreach($value->bookingClasses as $k => $obj){
//                        $tbody .= "<td>".$obj->seatsAvailable."</td>";
//                    }
//                }
            $tbody .= "</tr>";            
        }
        }

        $tbody .= "</tbody>
            </table>";  
        echo $tbody;
  ?>  
   
    
<!-- Arival -->
<table width="100%" class="flight">
  <thead>
    <tr>
    <?php 
    foreach($this->arrival['flight'] as $key => $value){ 
        if(is_object($value)){
            $theader = "<th>".JText::_("COM_TRAVELSEARCH_FLIGHTHOME")."</th>";
            $depart = explode("T", $value->departureTime);
            $arrivehome = explode("T", $value->arrivalTime);
            $theader .= "<th>".ApinorwegianHelper::formatDisplayDate($depart[0])."</th>";
            echo $theader;
        }
    }
    ?>
    </tr>
    <tr>
      <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTDEPART");?></th>
      <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTARRIVE");?></th>
      <th class="head"><?php echo JText::_("COM_TRAVELSEARCH_FLIGHTCODE");?></th>
      <!--th class="head">Available Seats</th-->
    </tr>
  </thead>  
  <?php 
    $tbody = "<tbody>";
    foreach($this->arrival['flight'] as $key => $value){
      if(is_object($value)){
        $tbody .= "<tr>";
            $tbody .= "<td><a class=tes>".substr($depart[1], 0, 5)." </a>".$this->arrival['city']['displayName']."</td>";
            $tbody .= "<td><a class=tes>".substr($arrivehome[1], 0, 5)." </a>".$this->departure['city']['displayName']."</td>";
            $tbody .= "<td>".$value->flightCode."</td>";
//            if(is_object($value->bookingClasses)){
//                $tbody .= "<td>".$value->bookingClasses->seatsAvailable."</td>";
//            } else {
//                foreach($value->bookingClasses as $k => $obj){
//                    $tbody .= "<td>".$obj->seatsAvailable."</td>";
//                }
//            }
        $tbody .= "</tr>";            
      }
    }
        
    $tbody .= "</tbody>
        </table>";  
    echo $tbody;
  ?>  
</table></div>