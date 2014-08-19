<?php
/**
 * @version		$Id: travelsearch.php 21097 2012-02-07 15:38:03Z priyowibowo $
 * @subpackage	com_travelsearcg
 * @copyright	Copyright (C) 2011 - 2012 Rejse-eksperterne, Inc. All rights reserved.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Search Component Search Model
 *
 * @package		Joomla.Site
 * @subpackage	com_search
 * @since 1.5
 */
class TravelSearchModelTravelSearch extends JModel
{
    function __construct(){
        parent::__construct();
    }
    
    function getPackageDetailsCombi($id, $date){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  descr.*, pkg.id as pkgid, pkg.includes as includeprice
                FROM    #__online_pkg_desc_test descr
                    INNER JOIN #__online_pkg_prices_test pkg
                        ON descr.id = pkg.pkg_id
                WHERE   descr.id = '".$id."'
                AND     from_date <= '".$date."' 
                AND     to_date >= '".$date."'
                AND     room_type = 'a'";

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPackageDetailsByID($id, $date){
        $db = JFactory::getDBO();
        
        /**
         * hotel.pool, hotel.childrenpool, hotel.bar, hotel.category, hotel.spa,
                        hotel.resto, hotel.beach, hotel.internet, hotel.localcentre, 
         */
        
        $sql = "SELECT  descr.*, pkg.id as pkgid, 
                        hotel.id as hotelid, hotel.category, pkg.includes as includeprice
                FROM    #__online_pkg_desc_test descr
                    INNER JOIN #__online_pkg_prices_test pkg
                        ON descr.id = pkg.pkg_id
                    INNER JOIN #__online_hotels_test hotel
                        ON hotel.id = descr.hotel_id
                WHERE   descr.id = '".$id."'
                AND     from_date <= '".$date."' 
                AND     to_date >= '".$date."'
                AND     room_type = 'a'";

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPackageNameID($id){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  product_name
                FROM    #__online_pkg_desc_test
                WHERE   id = ".$db->quote($id);

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPackageDetailsNoHotelByID($id, $date){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  descr.*, pkg.id as pkgid, 
                        pkg.includes as includeprice
                FROM    #__online_pkg_desc_test descr
                    INNER JOIN #__online_pkg_prices_test pkg
                        ON descr.id = pkg.pkg_id
                WHERE   descr.id = '".$id."'
                AND     from_date <= '".$date."' 
                AND     to_date >= '".$date."'
                AND     room_type = 'a'";

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPeriodPackageByID($id){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  period 
                FROM    #__online_pkg_desc_test
                WHERE   id = ".(int)$id;
        
        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getDestinationCodeByID($id){
        $db = JFactory::getDBO();
        
//        $sql = "SELECT  location_code
//                FROM    #__online_hotels_test hotel
//                    INNER JOIN #__online_pkg_desc_test pkg
//                    ON pkg.hotel_id = hotel.id
//                WHERE  pkg.id = '".$id."'";
        
        $sql = "SELECT  return_city_code, destination_city_code
                FROM    #__online_pkg_desc_test
                WHERE   id = ".(int)$id;

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPriceByIDTypeDate($id, $date, $room){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  pkg.id, pkg_id, from_date, to_date, room_type, vs, vvs, vvvs, bs, bxs, infant, margin, discount, transfer
	        FROM    #__online_pkg_prices_test pkg
                WHERE   pkg_id = '".$id."'
	        AND     from_date <= '".$date."' 
                AND     to_date >= '".$date."'
                AND     room_type = '".$room."'
	        ORDER BY from_date";
        
        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    public function getCurrencyByID($currency){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  *
	        FROM    #__online_currency
                WHERE   id = '".$currency."'";
        
        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function formatDate($date){
        $expl = explode("-", $date);
        $ret = $expl[2]."-".$expl[1]."-".$expl[0];
        return $ret;
    }
    
    function getDays($date1, $date2){
        $date1 = explode("-", $date1);
        $date2 = explode("-", $date2);
        $date1 = gregoriantojd($date1[1], $date1[2], $date1[0]);
        $date2 = gregoriantojd($date2[1], $date2[2], $date2[0]);
        return abs($date1 - $date2);
    }
    
    function priceBaseOnCurrency($pkgprice, $currency){
//        print_r($pkgprice);
        // if prices use other currency than eur, round it down to 25
        if(isset($currency)&&is_array($pkgprice)){
            foreach($pkgprice as $key => $val){
                foreach($val as $k => $v){
                    if(!is_numeric($k)){
                        if($v!=0&&$k!='margin'){
                            $pkgprice[$key][$k] = ($v*$currency[0]->currency_value);
    //                        $pkgprice[$key][$k] = ($pkgprice[$key][$k]!=0)? floor(($pkgprice[$key][$k]) / 25) * 25 : 0;
                        }
                    } else {
                        foreach($v as $i => $j){
                            $pkgprice[$key][$k][$i] = $j * $currency[0]->currency_value;
                        }
                    }
                }
            }
        }
        
        return $pkgprice;
    }
    
    function calculatePriceTrip($id, $rooms, $startstaydate, $toDate, $room){
        $check_date = $startstaydate;
        
        $prodname = $this->getPackageNameID($id);
            
        // accumulate price per passanger type
        $adult = 0;
        $twoadult = 0;
        $threeadult = 0;
        $onebaby = 0;
        $twobaby = 0;
        $infant = 0;
        $margin = 0;
        
        $transfer = 0;
        while ($check_date != $toDate) {
            $pricepkg = $this->getPriceByIDTypeDate($id, $check_date, $room);
            
            if(!empty($pricepkg)){
                $marginadt = ($pricepkg[0]->vs*($pricepkg[0]->margin/100));
                $discadt = ($pricepkg[0]->vs*($pricepkg[0]->discount/100));
                $adultnight = ($pricepkg[0]->vs+$marginadt) - $discadt;
                $adult += $adultnight;
                
                $marginTadt = ($pricepkg[0]->vvs * ($pricepkg[0]->margin/100));
                $discTadt = ($pricepkg[0]->vvs * ($pricepkg[0]->discount/100));
                $tadultnight = ($pricepkg[0]->vvs + $marginTadt) - $discTadt;
                $twoadult += $tadultnight;
                
                if(strtolower($pricepkg[0]->vvvs)!='n/a') {
                    $marginThadt = ($pricepkg[0]->vvvs * ($pricepkg[0]->margin/100));
                    $discThadt = ($pricepkg[0]->vvvs * ($pricepkg[0]->discount/100));
                    $thadultnight = ($pricepkg[0]->vvvs + $marginThadt) - $discThadt;
                    $threeadult += $thadultnight;
                } else
                    $threeadult = $pricepkg[0]->vvvs;
                
                if(strtolower($pricepkg[0]->bs)!='n/a'){   
                    $marginbaby = ($pricepkg[0]->bs * ($pricepkg[0]->margin/100));
                    $discbaby = ($pricepkg[0]->bs * ($pricepkg[0]->discount/100));
                    $babynight = ($pricepkg[0]->bs + $marginbaby) - $discbaby;
                    $onebaby += $babynight;
                } else 
                    $onebaby = $pricepkg[0]->bs;
                
                if(strtolower($pricepkg[0]->bxs)!='n/a'){
                    $marginTbaby = ($pricepkg[0]->bxs * ($pricepkg[0]->margin/100));
                    $discTbaby = ($pricepkg[0]->bxs * ($pricepkg[0]->discount/100));
                    $tbabynight = ($pricepkg[0]->bxs + $marginTbaby) - $discTbaby;
                    $twobaby += $tbabynight;
                } else 
                    $twobaby = $pricepkg[0]->bxs;
                
                if(strtolower($pricepkg[0]->infant)!='n/a'){
                    $margininfant = ($pricepkg[0]->infant * ($pricepkg[0]->margin/100));
                    $discinfant = ($pricepkg[0]->infant * ($pricepkg[0]->discount/100));
                    $infantnight = ($pricepkg[0]->infant + $margininfant) - $discinfant;
                    $infant += $infantnight;
                } else 
                    $infant = $pricepkg[0]->infant;
                
                $margin = $pricepkg[0]->margin;
                $transfer = $pricepkg[0]->transfer;
            } else return false;
            $check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date)));
        }  
        
        $calculated = array('onead' => $adult, 'twoad' => $twoadult, 'threead' => $threeadult, 'oneb' => $onebaby, 'twob' => $twobaby, 'inf' => $infant, 'transer' => $transfer, 'margin' => $margin);
        
        $errors = array();
        $roomprice = array();
        
        /**
         * kalau ada harga yang ga available, pisahin kamar dengan opsi lain
         */
        
        foreach($rooms as $key => $value){
            foreach($value as $k => $v){
                
                if(isset($rooms[$key]['type']))
                    unset($rooms[$key]['type']);
                if(!isset($rooms[$key]['adults']))
                    unset($rooms[$key]);
                
                if($k=='adults'){
                    if ($v==3){
                        if(strtolower($threeadult)=='n/a'){
                            $errors['errors'] += 1;
                            $errors['msg'][$key]['adults'] = JText::_('COM_TRAVELBOOKING_UNAVAIL_THREEADT')." ".$prodname[0]->product_name;
                            
                            if(strtolower($twoadult)=='n/a'){
                                // split jadi tiga
                                $rooms[$key]['adults'] = 1;
                                array_push($rooms, array('adults' => 1, 'child' => 0, 'infants' => 0));
                                array_push($rooms, array('adults' => 1, 'child' => 0, 'infants' => 0));
                            } else {
                                $rooms[$key]['adults'] = 2;
                                array_push($rooms, array('adults' => 1, 'child' => 0, 'infants' => 0));
                            }
                        } 
                    }
                } else if ($k=='child'){
                    if($v==2){
                        if(strtolower($twobaby)=='n/a'){
                            $errors['errors'] += 1;
                            $errors['msg'][$key]['child'] = JText::_('COM_TRAVELBOOKING_UNAVAIL_TWOCHD')." ".$prodname[0]->product_name;
                            
                            $rooms[$key]['child'] = 0;
                            
                            array_push($rooms, array('adults' => 2, 'child' => 0, 'infants' => 0));
                        } 
                    }
                } else if($k=='infants'){
                    if($v>0){
                        if(strtolower($infant)=='n/a'){
                            $errors['errors'] += 1;
                            $errors['msg'][$key]['infants'] = JText::_('COM_TRAVELBOOKING_UNAVAIL_BABY')." ".$prodname[0]->product_name;
                            
                            $rooms[$key]['infants'] = 0;
                            if($v==1){
                                array_push($rooms, array('adults' => 1, 'child' => 0, 'infants' => 0));
                            } else if($v==2){
                                array_push($rooms, array('adults' => 2, 'child' => 0, 'infants' => 0));
                            }
                        } 
                    } 
                }
            }
        }
        
        foreach($rooms as $key => $value){
            
            // get room type
            if($value['adults']==1){
                $prices[$key]['type'] = '1';
            } else if($value['adults']==2){
                if($value['child']==0){
                    if($value['baby']<=2){
                        $prices[$key]['type'] = '2'; 
                    }
                } else if($value['child']==1){
                    if($value['baby']<=1){
                        $prices[$key]['type'] = '4'; 
                    }
                } else if($value['child']==2){
                    if($value['baby']<1){
                        $prices[$key]['type'] = '5'; 
                    }
                }
            } else if($value['adults']==3){
                $prices[$key]['type'] = '3'; 
            }
            
            // pricing per room
            if($prices[$key]['type']==1){
                $amountadult += $adult;
                $prices[$key]['adults'] = $adult;

                $amountchild += 0;
                $prices[$key]['child'] = 0;

                if($k=='infants'){
                    if($v>0){
                        $amountinf += ($infant*$v);
                        $prices[$key]['infants'] = ($infant*$v);
                    } else if($v==0){
                        $amountinf += 0;
                        $prices[$key]['infants'] = 0;
                    }
                }
                $prices[$key]['total'] = $prices[$key]['adults']+$prices[$key]['child']+$prices[$key]['infants'];
            } else if($prices[$key]['type']==2){
                $amountadult += ($twoadult*2);
                $prices[$key]['adults'] = ($twoadult*2);

                $amountchild += 0;
                $prices[$key]['child'] = 0;

                if($k=='infants'){
                    if($v>0){
                        $amountinf += ($infant*$v);
                        $prices[$key]['infants'] = ($infant*$v);
                    } else if($v==0){
                        $amountinf += 0;
                        $prices[$key]['infants'] = 0;
                    }
                }

                $prices[$key]['total'] = $prices[$key]['adults']+$prices[$key]['child']+$prices[$key]['infants'];
            } else if($prices[$key]['type']==3){
                $amountadult += ($threeadult*3);
                $prices[$key]['adults'] = ($threeadult*3);

                $amountchild += 0;
                $prices[$key]['child'] = 0;

                $amountinf += 0;
                $prices[$key]['infants'] = 0;
                $prices[$key]['total'] = $prices[$key]['adults']+$prices[$key]['child']+$prices[$key]['infants'];
            } else if($prices[$key]['type']==4){

                $amountadult += ($twoadult*2);
                $prices[$key]['adults'] = ($twoadult*2);

                $amountchild += $onebaby;
                $prices[$key]['child'] = $onebaby;

                if($k=='infants'){
                    if($v>0){
                        $amountinf += ($infant*$v);
                        $prices[$key]['infants'] = ($infant*$v);
                    } else if($v==0){
                        $amountinf += 0;
                        $prices[$key]['infants'] = 0;
                    }
                }
                $prices[$key]['total'] = $prices[$key]['adults']+$prices[$key]['child']+$prices[$key]['infants'];
            } else if($prices[$key]['type']==5){
                $amountadult += ($twoadult*2);
                $prices[$key]['adults'] = ($twoadult*2);

                $amountchild += ($twobaby*2);
                $prices[$key]['child'] = ($twobaby*2);

                $amountinf += 0;
                $prices[$key]['infants'] = 0;
                $prices[$key]['total'] = $prices[$key]['adults']+$prices[$key]['child']+$prices[$key]['infants'];
            }
            
        }
        
        $priceall = array('nurooms' => $rooms, 'roompricing' => $prices, 'errors' => $errors, 'basicprice' => $calculated, 'amount' => array('adult' => $amountadult, 'child' => $amountchild, 'inf' => $amountinf, 'total' => ($amountadult+$amountchild+$amountinf)));
        
        return $priceall;
    } 
    
    public function getFlightDatesCombiPlain($from, $to, $returncity){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound > '".date("Y-m-d")."'
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadObjectList();
        $dates = array();
        if($results!=null){
            foreach($results as $key){
                // sini
                $indate = date ("Y-m-d", strtotime ("+7 days", strtotime($key->depart_outbound)));
                $flight_home = $this->getFlightByCitiesDate($returncity, 'CPH', $indate);
                
                if($flight_home){
                    $date = date ("j-n-Y", strtotime ($key->depart_outbound));
                    $dates[] = $date;
                }
            }
        }
        
        return $dates;
    }
    
    public function getFlightDatesCombi($from, $to){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound > '".date("Y-m-d")."'
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadObjectList();
        $dates = array();
        if($results!=null){
            foreach($results as $key){
                // sini
                $indate = date ("Y-m-d", strtotime ("+7 days", strtotime($key->depart_outbound)));
                
                $flight_home_a = $this->getFlightByCitiesDate('RAK', 'CPH', $indate);
                $flight_home_b = $this->getFlightByCitiesDate('AGA', 'CPH', $indate);
                
                if($flight_home_a&&$flight_home_b){
                    $date = date ("j-n-Y", strtotime ($key->depart_outbound));
                    $dates[] = $date;
                }
            }
        }
        
        return $dates;
    }
    
    public function getFlightByCitiesDate($from, $to, $date){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound = '".$date."'
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadObjectList();
        if($results!=null) return true;
        else return false; 
    }
    
    /**
     *  norwegian removed temporarily
     */
    public function getFlightDates($from, $to){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound > '".date("Y-m-d")."'
                ORDER BY depart_outbound";
//        AND     api != 'norwegian' 
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadObjectList();
        $dates = array();
        if($results!=null){
            foreach($results as $key){
                $date = date ("j-n-Y", strtotime ($key->depart_outbound));
                $dates[] = $date;
            }
        }
        
        return $dates;
    }
    
    public function getFlightDatesRoundtrip($from, $to, $returncity, $period){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound > '".date("Y-m-d")."'
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadObjectList();
        $dates = array();
        
        if($results!=null){
            foreach($results as $key){
                $date = date ("j-n-Y", strtotime ($key->depart_outbound));
                $nextflight = $this->checkNextFlight($from, $returncity, $key->depart_outbound, $period);
                if($nextflight)
                    $dates[] = $date;
            }
        }
        
        return $dates;
    }
    
    public function checkNextFlight($from, $to, $date, $period){
        $db = JFactory::getDBO();
        
        $nextdate = date ("Y-m-d", strtotime ("+".$period." days", strtotime($date)));
        
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = ".$db->quote($to)."
	        AND     `to` = ".$db->quote($from)."
                AND     depart_outbound = ".$db->quote($nextdate)."
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $result = $db->loadObjectList();
        if($result[0]->depart_outbound!='') return true;
        else return false;
    }
    
    public function getNextFlight($from, $to, $date){
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound
	        FROM    #__online_api_schedule
                WHERE   `from` = ".$db->quote($from)."
	        AND     `to` = ".$db->quote($to)."
                AND     depart_outbound > ".$db->quote(date('Y-m-d', strtotime($date)))."
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $result = $db->loadObjectList();
        return date('d-m-Y', strtotime($result[0]->depart_outbound));
    }
    
    public function getHotelByDest($todest){
        $db =& JFactory::getDBO();
        $query = "SELECT pkg.id as pkg, hotels.id as id, hotels.name as hotel, hotels.category as category, hotels.city
                  FROM #__online_hotels_test as hotels
                  INNER JOIN #__online_pkg_desc_test as pkg
                    ON pkg.hotel_id = hotels.id
                  WHERE pkg.travel_type = 'A'";
        
        //hotels.name LIKE ".$db->quote('%'.$term.'%')." AND   
        if($todest!=null)
            $query .= " AND   hotels.location_code = ".$db->quote($todest);
        
//        if($category!=null)
//            $query .= " AND   hotels.category = ".$db->quote($category);
        
        // LIMIT 0, 10
        $query .= " ORDER BY hotels.category";
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    public function getCityByCode($code){
        $db =& JFactory::getDBO();
        $sql = "SELECT * FROM #__online_api_destination WHERE iataAirportCode = ".$db->quote($code);
        $db->setQuery($sql);
        return $db->loadAssocList();
    }
    
    public function getZooElementByItemIdRt($id){
        // load ZOO config
        require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

        // Get the ZOO App instance
        $zoo = App::getInstance('zoo');
        $zoo_item = $zoo->table->item->get($id);
                
        $zooElement = array();
        foreach ($zoo_item->getElements() as $element) {
            // get label and value
            $name = $element->getConfig()->get('name');
            $type = $element->getConfig()->get('type');
            
            // ||$type=='gallery' temporary removed
            if(trim($type)=='image'){
                $zooElement[trim($type)][trim($name)] = $element->render();
            } else if(trim($type)=='textarea'){
                if(trim($name)=='Category teaser area') $zooElement[trim($type)][trim($name)] = $element->render();
                
            } else if (trim($type)=='text'){
                if(trim($name)=='Item subtitle') $zooElement[trim($type)][trim($name)] = $element->render(); 
                
            }            
//                $xml = $zoo->xml->loadString($element->toXML());
//                $zooElement[$type][$name] = (string)$xml->value;
        }
        
        return $zooElement;
    }
    
    public function getZooElementByItemId($id){
        // load ZOO config
        require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

        // Get the ZOO App instance
        $zoo = App::getInstance('zoo');
        $zoo_item = $zoo->table->item->get($id);
                
        $zooElement = array();
        foreach ($zoo_item->getElements() as $element) {
            // get label and value
            $name = $element->getConfig()->get('name');
            $type = $element->getConfig()->get('type');
            
            // ||$type=='gallery' temporary removed
            if(trim($type)=='image'){
                $zooElement[trim($type)][trim($name)] = $element->render();
            } else if(trim($type)=='textarea'){
                if(trim($name)=='Category Teaser Area') $zooElement[trim($type)][trim($name)] = $element->render();
                
            } else if (trim($type)=='text'){
                if(trim($name)=='category teaser') $zooElement[trim($type)][trim($name)] = $element->render();
            }
            
//                $xml = $zoo->xml->loadString($element->toXML());
//                $zooElement[$type][$name] = (string)$xml->value;
        }
        
        return $zooElement;
    }
    
    public function getAllPackageIdByDest($dest, $type = 'A'){
        $db =& JFactory::getDBO();
        $sql = "SELECT pkg.id, destination_city_code, return_city_code FROM #__online_pkg_desc_test as pkg 
                WHERE pkg.destination_city_code = ".$db->quote($dest);           
        /**
         *INNER JOIN #__online_hotels_test as hotel
                        ON hotel.id = pkg.hotel_id 
         * WHERE hotel.location_code = ".$db->quote($dest)
         */
        
        if($type!=null)
            $sql .= " AND pkg.travel_type = ".$db->quote($type);
                
//        $sql .= " ORDER BY hotel.category";
            
        $db->setQuery($sql);
        return $db->loadAssocList();
    }
    
    public function syncFlightPriceAndCurrency($flightdata, $currency){
        if(strtoupper($currency)!='EUR'){
            if(strtoupper($flightdata['booking']['currency'])=='EUR'){
                $cur = $this->getCurrencyByID($currency);
               return array(
                        'ADT' => ($flightdata['booking']['ADT']*$cur[0]->currency_value),
                        'CHD' => ($flightdata['booking']['CHD']*$cur[0]->currency_value),
                        'INF' => ($flightdata['booking']['INF']*$cur[0]->currency_value),
                        'currency' => $currency
                            );
            } else if(strtoupper($flightdata['booking']['currency'])!='EUR'){
               $cur = $this->getCurrencyByID($flightdata['booking']['currency']);
               $finalcurr = $cur[0]->currency_value_to_eur*$cur[0]->currency_value;
               
               return array(
                        'ADT' => ($flightdata['booking']['ADT']*$finalcurr),
                        'CHD' => ($flightdata['booking']['CHD']*$finalcurr),
                        'INF' => ($flightdata['booking']['INF']*$finalcurr),
                        'currency' => $flightdata['booking']['currency']
                            );
            }
        } else {
            if(strtoupper($flightdata['booking']['currency'])!='EUR'){
                $cur = $this->getCurrencyByID($flightdata['booking']['currency']);
                
               return array(
                        'ADT' => ($flightdata['booking']['ADT']*$cur[0]->currency_value_to_eur),
                        'CHD' => ($flightdata['booking']['CHD']*$cur[0]->currency_value_to_eur),
                        'INF' => ($flightdata['booking']['INF']*$cur[0]->currency_value_to_eur),
                        'currency' => $flightdata['booking']['currency']
                            );
            } 
        }
    }
    
    public function calculatePriceFlightsPerson($departureprice, $arrivalprice){
//        if($departureprice['booking']['currency']!=''){
//            $cur = $this->getCurrencyByID($departureprice['booking']['currency']);
//            
//            // make the price to euro and calculate them back to system's currency
//            if(strtoupper($departureprice['booking']['currency'])!='EUR'){    
//                $finalcurr = $cur[0]->currency_value_to_eur*$cur[0]->currency_value;
//            
//                return array(
//                        'ADT' => ($departureprice['booking']['ADT']*$finalcurr)+($arrivalprice['booking']['ADT']*$finalcurr),
//                        'CHD' => ($departureprice['booking']['CHD']*$finalcurr)+($arrivalprice['booking']['CHD']*$finalcurr),
//                        'INF' => ($departureprice['booking']['INF']*$finalcurr)+($arrivalprice['booking']['INF']*$finalcurr),
//                            );
//            } else {
//            
//                return array(
//                            'ADT' => ($departureprice['booking']['ADT'])+($arrivalprice['booking']['ADT']),
//                            'CHD' => ($departureprice['booking']['CHD'])+($arrivalprice['booking']['CHD']),
//                            'INF' => ($departureprice['booking']['INF'])+($arrivalprice['booking']['INF']),
//                            );
//            }
//        } else {
            return array(
            'ADT' => $departureprice['booking']['ADT']+$arrivalprice['booking']['ADT'],
            'CHD' => $departureprice['booking']['CHD']+$arrivalprice['booking']['CHD'],
            'INF' => $departureprice['booking']['INF']+$arrivalprice['booking']['INF'],
            'currency' => $departureprice['booking']['currency']    
                    );
//        }
    }
    
    public function calculatePriceFlightsPassangers($passangers, $perpax){
        $total = array();
        foreach($passangers as $k => $v){
            if($k=='adults'){
                $total[$k] = $v*$perpax['ADT'];
            } else if($k=='child'){
                $total[$k] = $v*$perpax['CHD'];
            } else if($k=='infants'){
                $total[$k] = $v*$perpax['INF'];
            }
        }
        $total['total'] = 0;
        foreach($total as $k => $v){
            $total['total'] += $v;
        }
        
        return $total;
    }
    
    public function saveQuerySearchTravel($mixed){
        $session =& JFactory::getSession();
        $session->set('search_address', $_SERVER['REMOTE_ADDR']);
        $session->set('search_time', $_SERVER['REQUEST_TIME']);
               
        $q = base64_encode($session->get('search_time').$session->get('search_address'));
        
        $db = JFactory::getDBO();
        $sql = "INSERT INTO #__online_search_session_test (`query`, `displayid`)
                VALUES('".serialize($mixed)."', '".$q."')";
        
        $db->setQuery($sql);
        $db->Query();
    }
    
    public function getQuerySearchTravel($address, $time){
        $q = base64_encode($time.$address);
        if($q!=''){
            $db = JFactory::getDBO();
            $sql = "SELECT * FROM #__online_search_session_test WHERE `displayid` = '".$q."'";

            $db->setQuery($sql);
            $db->Query();

            return $db->loadAssocList();
        }
    }
    
    public static function getDaysInterval($date1, $date2){
        $db =& JFactory::getDBO();

        //Mencari selisih tanggal dengan DATEDIFF
        $sql = "SELECT DATEDIFF('$date2','$date1') as selisih";
        $db->setQuery($sql);
        $rows = $db->loadObjectList();
        $range = $rows[0]->selisih;
        return $range;
    }
    
    function checkSeatsForPassangers($passangers, $departureFlight, $arrivalFlight, $arrivalFlight2 = null){
        $total = $passangers['adults']+$passangers['child']+$passangers['infants'];
        $depart = 0;
        $arrive = 0;
        $arrive2 = 0;
        
        if(is_array($departureFlight['OriginDestinationInformation'])){
            // for apiarabia
            return array('depart' => 0, 'arrive' => 0);
        }
        
        foreach($departureFlight as $k => $v){
            if(!is_array($v->bookingClasses)){
                if($v->bookingClasses->seatsAvailable<$total){
                    $depart = $v->bookingClasses->seatsAvailable;
                }
            } else {
                if($v->bookingClasses[0]->seatsAvailable<$total){
                   $depart = $v->bookingClasses[0]->seatsAvailable;
                }
            }
        }
        
        foreach($arrivalFlight as $k => $v){
            if(!is_array($v->bookingClasses)){
                if($v->bookingClasses->seatsAvailable<$total){
                    $arrive = $v->bookingClasses->seatsAvailable;
                }
            } else {
                if($v->bookingClasses[0]->seatsAvailable<$total){
                    $arrive = $v->bookingClasses[0]->seatsAvailable;
                }
            }
            
        }
        
        if($arrivalFlight2!=null){
            foreach($arrivalFlight2 as $k => $v){
                if(!is_array($v->bookingClasses)){
                    if($v->bookingClasses->seatsAvailable<$total){
                        $arrive2 = $v->bookingClasses->seatsAvailable;
                    }
                } else {
                    if($v->bookingClasses[0]->seatsAvailable<$total){
                        $arrive2 = $v->bookingClasses[0]->seatsAvailable;
                    }
                }
            }
        }
        
        return array('depart' => $depart, 'arrive' => $arrive, 'arrive2' => $arrive2);
    }
    
    /**
     * norwegian removed temporarily
     */
    public function getAPIsrc($from, $to){
        $db =& JFactory::getDBO();

        $query = "SELECT api 
                  FROM #__online_api_schedule 
                  WHERE `from` =  ".$db->quote($from)."
                  AND `to` = ".$db->quote($to)."
                  
                  ORDER BY api
                  LIMIT 0, 1";
//        AND  api != 'norwegian'
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    /**
     * norwegian removed temporarily
     */
    public function getAPIsrcCombi($from, $to){        
        $db = JFactory::getDBO();
                
        $sql = "SELECT  depart_outbound, api
	        FROM    #__online_api_schedule
                WHERE   `from` = '".$from."'
	        AND     `to` = '".$to."' 
                AND     depart_outbound > '".date("Y-m-d")."'
                ORDER BY depart_outbound";
        
        $db->setQuery($sql);
        $db->Query();
        $results = $db->loadAssocList();
        
        $dates = array();
        if($results!=null){
            foreach($results as $key){
                // sini
                $indate = date ("Y-m-d", strtotime ("+7 days", strtotime($key['depart_outbound'])));
                
                $flight_home_a = $this->getFlightByCitiesDate('RAK', 'CPH', $indate);
                $flight_home_b = $this->getFlightByCitiesDate('AGA', 'CPH', $indate);
                
                if($flight_home_a&&$flight_home_b){
                    $val[]['api'] = $key['api'];
                    return $val;
                }
            }
        }
        
        return false;
    }
    
    public function getDefaultLabel(){
        // Create a new query object.           
        $db = JFactory::getDBO();

        $q = "SELECT * FROM #__online_labels ORDER BY label";

        $db->setQuery($q);

        $results = $db->loadObjectList(); 
        return $results;
    }
};