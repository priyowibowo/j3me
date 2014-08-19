<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<div id="tabletop">
<h5 class="h5top">Oplysninger om flyrejse</h5>
<!-- departure -->
<table width="100%" class="flight">
    
    <?php
        if(is_array($this->departure['flight'][OriginDestinationInformation])){
            $theader = "<th>Udrejse</th>";
            $depart = explode("T", $this->departure['flight'][OriginDestinationInformation][DepartureDateTime]);
            $arivedest = explode("T", $this->departure['flight'][OriginDestinationInformation][ArrivalDateTime]);
            $theader .= "<th>".ApinorwegianHelper::formatDisplayDate($depart[0])."</th>";
            echo $theader;
        }
        
    ?>
    </tr>
        <tr>
            <th class="head">Afgang</th>
            <th class="head">Ankomst</th>
            <th class="head">Flight Code</th>
            <!--th class="head">Available Seats</th-->
        </tr>
    </thead>  
    <?php 
        $tbody = "<tbody>";
        
        if(is_array($this->departure['flight'][OriginDestinationInformation])){
            $tbody .= "<tr>";
                $tbody .= "<td><a class=tes>".substr($depart[1], 0, 5)."</a> ".$this->departure['city']['displayName']."</td>";
                $tbody .= "<td><a class=tes>".substr($arivedest[1], 0, 5)."</a> ".$this->arrival['city']['displayName']."</td>";
                $tbody .= "<td>".$this->departure['flight'][OriginDestinationInformation][OriginDestinationOptions][OriginDestinationOption][FlightSegment]['!FlightNumber']."</td>";
//                if(is_object($value->bookingClasses)){
//                    $tbody .= "<td>".$value->bookingClasses->seatsAvailable."</td>";
//                } else {
//                    foreach($value->bookingClasses as $k => $obj){
//                        $tbody .= "<td>".$obj->seatsAvailable."</td>";
//                    }
//                }
            $tbody .= "</tr>";            
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
    
        if(is_array($this->arrival['flight'][OriginDestinationInformation])){
            $theader = "<th>Hjemrejse</th>";
            $depart = explode("T", $this->arrival['flight'][OriginDestinationInformation][DepartureDateTime]);
            $arrivehome = explode("T", $this->arrival['flight'][OriginDestinationInformation][ArrivalDateTime]);
            $theader .= "<th>".ApinorwegianHelper::formatDisplayDate($depart[0])."</th>";
            echo $theader;
        }
    
    ?>
    </tr>
    <tr>
      <th class="head">Afgang</th>
      <th class="head">Ankomst</th>
      <th class="head">Flight Code</th>
      <!--th class="head">Available Seats</th-->
    </tr>
  </thead>  
  <?php 
    $tbody = "<tbody>";
    
      if(is_array($this->arrival['flight'][OriginDestinationInformation])){
        $tbody .= "<tr>";
            $tbody .= "<td><a class=tes>".substr($depart[1], 0, 5)." </a>".$this->arrival['city']['displayName']."</td>";
            $tbody .= "<td><a class=tes>".substr($arrivehome[1], 0, 5)." </a>".$this->departure['city']['displayName']."</td>";
            $tbody .= "<td>".$this->arrival['flight'][OriginDestinationInformation][OriginDestinationOptions][OriginDestinationOption][FlightSegment]['!FlightNumber']."</td>";
//            if(is_object($value->bookingClasses)){
//                $tbody .= "<td>".$value->bookingClasses->seatsAvailable."</td>";
//            } else {
//                foreach($value->bookingClasses as $k => $obj){
//                    $tbody .= "<td>".$obj->seatsAvailable."</td>";
//                }
//            }
        $tbody .= "</tr>";            
      }
    
        
    $tbody .= "</tbody>
        </table>";  
    echo $tbody;
  ?>  
</table></div>