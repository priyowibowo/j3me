<?php
/**
 * @version     $Id: default 2012-01-19 16:00:59 priyowibowo $
 * @subpackage  com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// no direct access
defined('_JEXEC') or die;
require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');
?>

<h3 class="h3top">Rundrejse til <?php echo $this->pkgdetails[0]->product_name ?></h3>
<!--h4 class="h4top"> </h4-->
<?php

if($this->srcapi=='norwegian')
echo $this->loadTemplate('flight');
else if($this->srcapi=='arabia')
echo $this->loadTemplate('flightarabia');

foreach($this->zoo_data as $key => $element){
    if($key=='image'){
        foreach($element as $k => $v){
            if($k=='Mainimage'){
                $image = $v;
            } 
        }
    } else if($key=='textarea'||$key=='text'){
        foreach($element as $k => $v){
            if($k=='Item subtitle'){
                $includes = $v;
            } else if($k=='Category teaser area') {
                $teaser = $v;
            }
        }
    } 
}
$package = "
            <form action=".JRoute::_('index.php')." method=post id=travel-search1>    
            <div id=list><table width=100% id=img>
                        <tr class=bx1>
                            <td class=img>".$image;
        $grandtotal = 0;
        $cheapest = 0;
        if(is_array($this->rooms)){
            foreach($this->rooms as $k => $room){
                if($room['single']!=''){
                    if($cheapest==0)
                        $cheapest = $room['single'];
                    else if($cheapest>$room['single'])
                        $cheapest = $room['single'];
                } else if($room['double']!=''){
                    if($cheapest==0)
                        $cheapest = $room['double'];
                    else if($cheapest>$room['double'])
                        $cheapest = $room['double'];
                } else if($room['triple']!=''){
                    if($cheapest==0)
                        $cheapest = $room['triple'];
                    else if($cheapest>$room['triple'])
                        $cheapest = $room['triple'];

                }
                
                $grandtotal += $this->rooms[$k]['total'];
            }    
        }
            
//        $flightmargin = ($this->pkgprice['perpax']['ADT']*$this->pkgprice['basicprice']['margin'])/100;
//        $flightprice = $this->pkgprice['perpax']['ADT'] + $flightmargin;
//        
//        // ROUND UP nearest 25
//        $totalprice = floor(((int)$this->pkgprice['basicprice']['twoad']+$flightprice)/ 5) * 5;
//        <div id=more-hotel><a href=#>Læs mere</a></div>
        $package .= "</td>
                    <td class=info>
                        <ul>
                            <li class=rating".$this->pkgdetails[0]->category."><strong>".$this->pkgdetails[0]->product_name."</strong><span></span></li>
                            <li>".$teaser.".</li>";

            if($this->seatavailable<6)                   
                $package .= "<li style=font-size:11px;>Kun ".$this->seatavailable." pladser i denne prisklasse </li>";
        
        $package .= "</ul></td>
                     <td class=last>
                        <ul class=checklistcf>";
        
                          $params = explode("_", $this->pkgdetails[0]->parameters);
                          foreach($params as $k => $v){
                              $vals = explode(":", $v);
                              if($vals[1]=='0'||$vals[1]=='1'){
                                  $package .= ($vals[1]) ? "<li class=check><span>".$vals[0]."</span></li>" : "<li><span>".$vals[0]."</span></li>";
                              } else {
                                  $package .= ($vals[1]!='') ? "<li class=check><span>".$vals[0].": ".$vals[1]."</span></li>" : "<li><span>".$vals[0]."</span></li>";
                              }
                          }
                          
//                          $package .= ($this->pkgdetails[0]->childrenpool) ? "<li class=check><span>Børnepool</span></li>" : "<li><span>Børnepool</span></li>";
//                          $package .= ($this->pkgdetails[0]->spa) ? "<li class=check><span>Spa</span></li>" : "<li><span>Spa</span></li>";
//                          $package .= ($this->pkgdetails[0]->bar) ? "<li class=check><span>Bar</span></li>" : "<li><span>Bar</span></li>";
//                          $package .= ($this->pkgdetails[0]->resto) ? "<li class=check><span>Restaurant</span></li>" : "<li><span>Restaurant</span></li>";
//                          
//                          $package .= ($this->pkgdetails[0]->beach!='') ? "<li class=check><span>Strand: ".$this->pkgdetails[0]->beach."</span></li>" : "<li><span>Strand</span></li>";
//                          $package .= ($this->pkgdetails[0]->internet!='') ? "<li class=check><span>Internet: ".$this->pkgdetails[0]->internet."</span></li>" : "<li><span>Internet</span></li>";
        
        $currencyfront = '';
        $currencyback = '';
        if($this->currency=='NOK'||$this->currency=='SEK'||$this->currency=='DKK')
            $currencyback = 'Kr.';
        else if($this->currency=='EUR')
            $currencyfront = '€';
        else if($this->currency=='GBP')
            $currencyfront = '£';                 
        
        $totalpassangers = $this->passangers['adults']+$this->passangers['child']+$this->passangers['infants'];
                          
        $depart = explode("T", $this->booking['outbound']['departureTime']);
        $package .= "</ul>
                    </td>
                </tr></table><table id=bottom>
                <tr class=bx2>
                    <td class=one><p>".$includes."</p></td>
                    <td class=two>
                        <ul>
                            <li><strong>Afrejse</strong> ".ApinorwegianHelper::formatDisplayDate($depart[0], false)." (".$this->days." nætter, ".$totalpassangers." pers.)</li>
                            <li><em>".$currencyfront." ".number_format($cheapest, 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                            if(is_array($this->rooms)){
//                                foreach($this->rooms as $k => $room){
//                                    if($this->rooms[$k]['single']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($this->rooms[$k]['single'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                    else if($this->rooms[$k]['double']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($this->rooms[$k]['double'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                    else if($this->rooms[$k]['triple']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($this->rooms[$k]['triple'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                }    
//                            }
                            
        $package .= "</ul>
                    </td>
                    <td class=three>
                        <a rel=nofollow class=fetchdata>I alt ".$currencyfront." ".number_format($grandtotal, 0, '.', '.').",- ".$currencyback."</a>
                        <input id=book type=submit value=Bestil><br>
                        <a href=".JRoute::_('index.php?option=com_zoo&task=item&item_id='.$this->pkgdetails[0]->zooitem_id).">Se andre datoer</a>
                    </td>
                </tr>
            </table></div><input type=hidden name=option value=com_travelsearch />
            <input type=hidden name=task value=savesearch />
            <input type=hidden name=traveltype value=".base64_encode(1)." />
            <input type=hidden name=fromdest value=".base64_encode($this->departure['city']['iataAirportCode'])." />
            <input type=hidden name=todest value=".base64_encode($this->departure['city_dest']['iataAirportCode'])." />
            <input type=hidden name=return_city value=".base64_encode($this->arrival['city']['iataAirportCode'])." />
            <input type=hidden name=package value=".base64_encode($this->pkgdetails[0]->id)." />
            <input type=hidden name=item_id value=".base64_encode($this->pkgdetails[0]->zooitem_id)." />
            <input type=hidden name=passangers value=".htmlentities(base64_encode(serialize($this->passangers)))." />
            <input type=hidden name=bookingflight value=".htmlentities(base64_encode(serialize($this->booking)))." /> 
            <input type=hidden name=currency value=".base64_encode($this->currency)." />
            <input type=hidden name=rooms value=".htmlentities(base64_encode(serialize($this->rooms)))." />
            <input type=hidden name=srcapi value=".htmlentities(base64_encode(serialize($this->srcapi)))." />    
            </form>";

echo $package;
/**
 *
<div id="hotel-detail" title="Hotel Information">
    <?php echo $this->loadTemplate('modal.dialog.hotel');?>
</div> 
 */
?>
   