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

<jdoc:include type="message" />

<h3 class="h3top"> <?php echo JText::_("COM_TRAVELSEARCH_CHOOSEPKG")." ".$this->arrival['city']['displayName'] ?></h3>
<?php

if($this->srcapi=='norwegian')
echo $this->loadTemplate('flight');
else if($this->srcapi!='norwegian')
echo $this->loadTemplate('flightarabia');

// re-sorting based on price
foreach($this->packagedata as $desc => $data){
    if($data['price']!=''&&($data['package']!='')){
        if(is_array($data['rooms'])){
            foreach($data['rooms'] as $k => $room){
                $this->packagedata[$desc]['grandtotal'] += $data['rooms'][$k]['total'];
            }    
        }
    }
}

function sortbyprice($a, $b){
    if ($a['grandtotal'] == $b['grandtotal']) {
        return 0;
    }
    
    return ($a['grandtotal'] < $b['grandtotal']) ? -1 : 1;
}

usort($this->packagedata, "sortbyprice");

foreach($this->packagedata as $desc => $data){
    
    if($data['price']!=''&&($data['package']!='')){
        $image = '';
        $teaser = '';
        $includes = '';
        if($data['zoo_data']!=''){
            foreach($data['zoo_data'] as $key => $element){
                if($key=='image'){
                    foreach($element as $k => $v){
                        if($k=='Main Image'){
                            $image = $v;
                        } 
                    }
                } else if($key=='textarea'||$key='text'){
                    foreach($element as $k => $v){
//                        take include from data package
//                        if($k=='category teaser'){
//                            $includes = $v;
//                        } else 
                        if($k=='Category Teaser Area') {
                            $teaser = $v;
                        }
                    }
                }
            }
        }
        
        $package = "<form action=".JRoute::_('index.php')." method=post id=travel-search-".$desc.">    
                    <div id=list><table width=100% id=img>
                        <tr class=bx1>
                            <td class=img>".$image;
                                 
//                                <div class=link-more-hotel id=more-hotel-".$desc."><a href=#>Læs mere</a></div>    
                                
                                
                                /** * <div id=hotel-detail-".$desc." title='Hotel Information'>*/
                                
//                $flightmargin = ($data['perpax']['ADT']*$data['price']['basicprice']['margin'])/100;
//                $flightprice = $data['perpax']['ADT'] + $flightmargin;
//
//                // ROUND UP nearest 25
//                $totalprice = floor(((int)$data['price']['basicprice']['twoad']+$flightprice)/ 5) * 5;                
                                
                $package .= "</td>
                            <td class=info>
                                <ul>
                                    <li class=rating".$data['package'][0]->category."><strong>".$data['package'][0]->product_name."</strong><span></span></li>
                                    <li>".$teaser."</li>";
	
                if($this->seatavailable<6)                   
                        $package .= "<li style=font-size:11px;>Kun ".$this->seatavailable." pladser i denne prisklasse </li>";
                
                $params = unserialize($data['package'][0]->parameters);
                
                $package .= "</ul>
                            </td>
                            <td class=last>
                                <ul class=checklistcf>";
                            if($params!=''){
                                foreach($params as $k => $v){
                                    if($v['label']!=''){
                                        foreach($this->defaultlabels as $k => $o){
                                            if($o->id==$v['label'])
                                                $label = $o->label;
                                        }
                                        
                                        if($v['type']=='t'){
                                            $package .= "<li class=check><span>".$label." : ".$v['value']."</span></li>";
                                        } else if($v['type']=='c') {
                                            if($v['value']){
                                                $package .= "<li class=check><span>".$label."</span></li>"; 
                                            } else {
                                                $package .= "<li><span>".$label."</span></li>";
                                            }
                                        }
                                    }
                                }
                            }
                            
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
              
              if(is_array($data['rooms'])){
                  $cheapest = 0;
                  foreach($data['rooms'] as $k => $room){
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
                  }    
              }
              
              $package .=      "</ul>
                            </td>
                        </tr>
                    </table>
                    <table id=bottom>
                        <tr class=bx2>
                            <td class=one><p>".$data['package'][0]->includes."</p></td>
                            <td class=two>
                                <ul>
                                    <li><strong>".JText::_("COM_TRAVELSEARCH_CHECKOUT")."</strong> ".ApinorwegianHelper::formatDisplayDate($depart[0], false)." (".$this->days." nætter, ".$totalpassangers." pers.)</li>
                                    <li><em>".$currencyfront." ".number_format($cheapest, 0, '.', '.').",- ".$currencyback." /".JText::_("COM_TRAVELSEARCH_ADT")."</em></li>";
//                                    if(is_array($data['rooms'])){
//                                        foreach($data['rooms'] as $k => $v){
//                                            if($data['rooms'][$k]['single']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($data['rooms'][$k]['single'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                            else if($data['rooms'][$k]['double']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($data['rooms'][$k]['double'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                            else if($data['rooms'][$k]['triple']==$cheapest) $package .= "<li><em>".$currencyfront." ".number_format($data['rooms'][$k]['triple'], 0, '.', '.').",- ".$currencyback." /voksen</em></li>";
//                                        }
//                                    }
                  
               $package .=      "</ul>
                            </td>
                            <td class=three>
                                <a rel=nofollow class=fetchdata>".JText::_("COM_TRAVELSEARCH_TOTAL")." ".$currencyfront." ".number_format($data['grandtotal'], 0, '.', '.').",- ".$currencyback."</a>";
                                    if($data['package'][0]->zooitem_id!=''){
                                        $package .= "<input id=book type=submit value=".JText::_("COM_TRAVELSEARCH_BOOK")."><br>
                                                    <a href=".JRoute::_('index.php?option=com_zoo&task=item&item_id='.$data['package'][0]->zooitem_id).">".JText::_("COM_TRAVELSEARCH_SEEOTHERDATES")."</a>";
                                    }

                foreach($data['rooms'] as $k => $v){
                    unset($data['rooms']['$k']['total']);
                    unset($data['rooms']['$k']['double']);
                    unset($data['rooms']['$k']['triple']);
                    unset($data['rooms']['$k']['single']);
                }
                                    
               $package .=      "</td>
                        </tr>
                    </table>
                    </div>
                    <input type=hidden name=option value=com_travelsearch />
                    <input type=hidden name=task value=savesearch />
                    <input type=hidden name=traveltype value=".base64_encode($data['package'][0]->travel_type)." />
                    <input type=hidden name=fromdest value=".base64_encode($this->departure['city']['iataAirportCode'])." />
                    <input type=hidden name=todest value=".base64_encode($this->copydest)." />
                    <input type=hidden name=package value=".base64_encode($data['package'][0]->id)." />
                    <input type=hidden name=item_id value=".base64_encode($data['package'][0]->zooitem_id)." />
                    <input type=hidden name=passangers value=".htmlentities(base64_encode(serialize($this->passangers)))." />
                    <input type=hidden name=bookingflight value=".htmlentities(base64_encode(serialize($this->booking)))." /> 
                    <input type=hidden name=currency value=".base64_encode($this->currency)." />    
                    <input type=hidden name=rooms value=".htmlentities(base64_encode(serialize($data['rooms'])))." />
                    <input type=hidden name=srcapi value=".htmlentities(base64_encode(serialize($this->srcapi)))." />
                </form>";

        echo $package;
    }
}
?>

<!--div id="flight-detail" title="Fly Information">
    <?php // echo $this->loadTemplate('modal.dialog.flight');?>
</div-->