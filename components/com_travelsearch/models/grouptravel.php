<?php
/**
 * @version		$Id: travelsearch.php 21097 2012-02-07 15:38:03Z priyowibowo $
 * @subpackage	com_travelsearcg
 * @copyright	Copyright (C) 2011 - 2012 Rejse-eksperterne, Inc. All rights reserved.
 */

// No direct access
defined('_JEXEC') or die;
include_once (JPATH_ROOT.DS.'modules'.DS.'mod_roundtrip'.DS.'helper.php');
jimport('joomla.application.component.model');

/**
 * Search Component Search Model
 *
 * @package		Joomla.Site
 * @subpackage	com_search
 * @since 1.5
 */
class TravelSearchModelGrouptravel extends JModel
{
    function __construct(){
        parent::__construct();
    }
    
    /**
     *
     * @param date $from_date (Y-m-d)
     * @param string $from_city
     * @param string $to_city
     * @return objectlist 
     */
    function getFlightByID($id){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  *
                FROM    #__online_offline_grouptravels
                WHERE   id = ".$id."
                AND     seats > 0";
        
        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
        
    function getUniqueFlight(){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  *
                FROM    #__online_offline_grouptravels
                WHERE   depart_outbound >= '".$from_date."'
                LIMIT 0, 20";

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    function getPackagesGroupTravel(){
        $db = JFactory::getDBO();
        
        $sql = "SELECT  pkg.*, company, depart_outbound
                FROM    #__online_pkg_desc_test as pkg
                INNER JOIN #__online_offline_grouptravels as gt
                    ON pkg.grouptravel_out_flight = gt.id
                WHERE   travel_type = 'G'
                AND     depart_outbound > '".date("Y-m-d")."'
                AND     seats_available > 0
                ORDER BY    depart_outbound";

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
    
    public function syncFlightPriceAndCurrency(&$flightdata, $currency){
        
        if(strtoupper($currency)!='EUR'){
            if(strtoupper($flightdata->currency)=='EUR'){
                $cur = $this->getCurrencyByID($currency);
               
                $flightdata->adt_price = $cur[0]->currency_value*$flightdata->adt_price;
                $flightdata->chd_price = $cur[0]->currency_value*$flightdata->chd_price;
                $flightdata->inf_price = $cur[0]->currency_value*$flightdata->inf_price;
                
                $flightdata->currency = $currency;
            } else if(strtoupper($flightdata->currency)!='EUR'&&strtoupper($flightdata->currency)!= strtoupper($currency)){
               $cur = $this->getCurrencyByID($flightdata->currency);
               $finalcurr = $cur[0]->currency_value_to_eur*$cur[0]->currency_value;
                
               $flightdata->adt_price = $finalcurr*$flightdata->adt_price;
               $flightdata->chd_price = $finalcurr*$flightdata->chd_price;
               $flightdata->inf_price = $finalcurr*$flightdata->inf_price;
               
               $flightdata->currency = $currency;
            } 
        } else {
            if(strtoupper($flightdata[0]->currency)!='EUR'){
                $cur = $this->getCurrencyByID($flightdata->currency);
                $flightdata->adt_price = $cur[0]->currency_value_to_eur*$flightdata->adt_price;
                $flightdata->chd_price = $cur[0]->currency_value_to_eur*$flightdata->chd_price;
                $flightdata->inf_price = $cur[0]->currency_value_to_eur*$flightdata->inf_price;
                $flightdata->currency = $currency;
            }
        }
    }
    
    public function calculatePriceFlightsPerson($departureprice, $arrivalprice){
        $obj->adt_price = $departureprice->adt_price+$arrivalprice->adt_price;
        $obj->chd_price = $departureprice->chd_price+$arrivalprice->chd_price;
        $obj->inf_price = $departureprice->inf_price+$arrivalprice->inf_price;
        $obj->currency = $departureprice->currency;

        return $obj;
    }
    
    function calculatePrice($out, $in, $grouptravel, $curr){
        $db = &JFactory::getDBO();
        
        // cek seats from both flights and package
        $availableseat = $this->checkSeatsAvailable($out->seats, $in->seats, $grouptravel->seats_available);
        
        if(!$availableseat) return false;
        
        // get days spent
        $days = modRoundtripHelper::getDatesByInterval(true, $out->depart_outbound, $in->depart_outbound);

        //get nights spent
        $nights = modRoundtripHelper::getDatesByInterval(false, $out->depart_outbound, $in->depart_outbound);
        $totalNights = sizeof($nights);
            
        // for every night, look for the hotel price
        $person = 0;
        $price = 0;
        $margin = 0;
        $disc = 0;
        $pernight = 0;
        $twoperson = 0;
        $twoprice = 0;
        $twomargin = 0;
        $twodisc = 0;
        $twopernight = 0;
        $pkg_ids = "";
        
//        if(is_array($nights)){
//            foreach($nights as $k => $v){
                $query = "SELECT    #__online_pkg_prices_test.id, pkg_id, from_date, vs, vvs, vvvs, bs, bxs, infant,
                                    to_date, sale_vs, sale_vvs, sale_vvvs, sale_bs, sale_bxs, sale_infant, currency, transfer, type_name";
                    
                if($curr!='EUR'){
                    $query .= " , currency_value ";
                }

                $query .= " FROM    #__online_pkg_prices_test ";

                if($curr!='EUR'){
                    $query .= " INNER JOIN
                                #__online_currency
                                        ON #__online_currency.id = ".$db->quote($curr);
                }

                $query .= " INNER JOIN
                                #__online_pkg_desc_test
                                        ON pkg_id = #__online_pkg_desc_test.id  
                        WHERE   #__online_pkg_desc_test.id = ".$db->quote($grouptravel->id)."
                        AND     from_date <= ".$db->quote($out->arrival_outbound)."
                        AND     to_date >= ".$db->quote($out->arrival_outbound)."
                        AND     travel_type = 'G'
                        AND     room_type = 'A'
                        ORDER BY from_date";
              
                $db->setQuery($query);
                $rows = $db->loadObjectList();
                
                // if theres data price
                if($rows[0]->sale_vvs!=''){
                    
                    $room_type = $rows[0]->type_name;
                    
                    if(strtolower($rows[0]->currency)!=strtolower($curr)){
                        if(strtolower($rows[0]->currency)=='eur'){
                                                        
                            // accumulate
                            $person = $rows[0]->sale_vs*$rows[0]->currency_value;
                            $twoperson = $rows[0]->sale_vvs*$rows[0]->currency_value;
                            $threeperson = $rows[0]->sale_vvvs;
                            $child = $rows[0]->sale_bs;
                            $twochild = $rows[0]->sale_bxs;
                            $infant = $rows[0]->sale_bxs;
                            
                            $person_cost = ($rows[0]->vs*$rows[0]->currency_value);
                            $twoperson_cost = ($rows[0]->vvs*$rows[0]->currency_value);
                            $threeperson_cost = $rows[0]->vvvs;
                            $child_cost = $rows[0]->bs;
                            $twochild_cost = $rows[0]->bxs;
                            $infant_cost = $rows[0]->infant;
                            $transport = $rows[0]->transport*$rows[0]->currency_value; 
                            
                            if(strtolower($threeperson)!='n/a'){
                                $threeperson = $threeperson*$rows[0]->currency_value;
                                $threeprice = ($threeprice*$rows[0]->currency_value);
                            }

                            if(strtolower($child)!='n/a'){
                                $child = $child*$rows[0]->currency_value;
                                $childprice = ($childprice*$rows[0]->currency_value);
                            }

                            if(strtolower($twochild)!='n/a'){
                                $twochild = $twochild*$rows[0]->currency_value;
                                $twochildprice = ($twochildprice*$rows[0]->currency_value);
                            }

                            if(strtolower($infant)!='n/a'){
                                $infant = $infant*$rows[0]->currency_value;
                                $infantprice = ($twochildprice*$rows[0]->currency_value);
                            }
                        } 
                    } else {
                        // accumulate
                        $person = $rows[0]->sale_vs;
                        $twoperson = $rows[0]->sale_vvs;
                        $threeperson = $rows[0]->sale_vvvs;
                        $child = $rows[0]->sale_bs;
                        $twochild = $rows[0]->sale_bxs;
                        $infant = $rows[0]->sale_bxs;

                        $person_cost = $rows[0]->vs;
                        $twoperson_cost = $rows[0]->vvs;
                        $threeperson_cost = $rows[0]->vvvs;
                        $child_cost = $rows[0]->bs;
                        $twochild_cost = $rows[0]->bxs;
                        $infant_cost = $rows[0]->infant;
                        $transport = $rows[0]->transport; 
                    }
                    
                    $flighttotal = $this->calculatePriceFlightsPerson($out, $in);
                    $flight = $flighttotal->adt_price;
                    $flightChd = $flighttotal->chd_price;
                    $flightInf = $flighttotal->inf_price;
                   
                    $margin = 0;
                }
//            }

            if($person!=0){
                $flights->product_id = $grouptravel->id;
                $flights->room_type = $room_type;
                $flights->room_price_forone = $person;
                $flights->room_price_fortwo_perperson = $twoperson;
                $flights->room_price_forthree_perperson = (strtoupper($threeperson)!='N/A')? $threeperson : 'N/A';
                $flights->room_price_onechild = (strtoupper($child)!='N/A')? $child : 'N/A';
                $flights->room_price_twochild = (strtoupper($twochild)!='N/A')? $twochild : 'N/A';
                $flights->room_price_infant = (strtoupper($infant)!='N/A')? $infant : 'N/A';
                $flights->room_price_fortwo = ($twoperson*2);
                $flights->vs_margin = $person_cost;
                $flights->vvs_margin = $twoperson_cost;
                $flights->vvvs_margin = $threeperson_cost;
                $flights->bs_margin = $child_cost;
                $flights->bxs_margin = $twochild_cost;
                $flights->infant_margin = $infant_cost;
                $flights->transport_margin = $transport;
                $flights->price_adt_margin = $flight;
                $flights->price_chd_margin = $flightChd;
                $flights->price_inf_margin = $flightInf;
                $flights->currency = $curr;
                $flights->days = sizeof($days);
                $flights->total_nights = $totalNights;
                $flights->margin = $margin;
                $flights->outbound_id = $out->id;
                $flights->outbound_city_depart = $out->from;
                $flights->outbound_city_via = $out->via_city;
                $flights->outbound_city_destination = $out->to;
                $flights->outbound_depart_date = $out->depart_outbound;
                $flights->outbound_arrive_date = $out->arrival_outbound;
                $flights->outbound_seats = $out->seats;
                $flights->outbound_time_depart = $out->time_depart;
                $flights->outbound_time_arrive = $out->time_arrive;
                $flights->outbound_flight_code = $out->flight_code;
                $flights->inbound_id = $in->id;
                $flights->inbound_city_depart = $in->from;
                $flights->inbound_city_via = $in->via_city;
                $flights->inbound_city_destination = $in->to;
                $flights->inbound_depart_date = $in->depart_outbound;
                $flights->inbound_arrive_date = $in->arrival_outbound;
                $flights->inbound_seats = $in->seats;
                $flights->inbound_time_depart = $in->time_depart;
                $flights->inbound_time_arrive = $in->time_arrive;
                $flights->inbound_flight_code = $in->flight_code;
                $flights->package = $grouptravel;
                $flights->seats = $availableseat;
                $flights->seats_display = $grouptravel->seats_display;
                $flights->zoo_data = isset($grouptravel->zooitem_id) ? $this->getZooElementByItemId($grouptravel->zooitem_id) : '';
        
            }
//        } 
        
        return $flights;
    }
    
    function calculatePriceOLD($out, $in, $grouptravel){
        
        $db = &JFactory::getDBO();
                                
        // get days spent
        $days = modRoundtripHelper::getDatesByInterval(true, $out->depart_outbound, $in->depart_outbound);

        //get nights spent
        $nights = modRoundtripHelper::getDatesByInterval(false, $out->depart_outbound, $in->depart_outbound);
        $totalNights = sizeof($nights);
            
        // for every night, look for the hotel price
        $person = 0;
        $price = 0;
        $margin = 0;
        $disc = 0;
        $pernight = 0;
        $twoperson = 0;
        $twoprice = 0;
        $twomargin = 0;
        $twodisc = 0;
        $twopernight = 0;
        $pkg_ids = "";
        
        if(is_array($nights)){
            foreach($nights as $k => $v){
                $query = "SELECT    #__online_pkg_prices_test.id, pkg_id, from_date, 
                                    to_date, vs, vvs, vvvs, bs, bxs, infant, currency, margin, discount, transfer, type_name";
                    
                if($out->currency!='EUR'){
                    $query .= " , currency_value ";
                }

                $query .= " FROM    #__online_pkg_prices_test ";

                if($out->currency!='EUR'){
                    $query .= " INNER JOIN
                                #__online_currency
                                        ON #__online_currency.id = ".$db->quote($out->currency);
                }

                $query .= " INNER JOIN
                                #__online_pkg_desc_test
                                        ON pkg_id = #__online_pkg_desc_test.id  
                        WHERE   #__online_pkg_desc_test.id = ".$db->quote($grouptravel->id)."
                        AND     from_date <= ".$db->quote($v)."
                        AND     to_date >= ".$db->quote($v)."
                        AND     travel_type = 'G'
                        AND     room_type = 'A'
                        ORDER BY from_date";
              
                $db->setQuery($query);
                $rows = $db->loadObjectList();
                
                // if theres data price
                if($rows[0]->vvs!=''){
                    // the price per-night for a person multiplies the currency
                    // a double per person a night = (vvs*currency);
                    if(strtolower($out->currency)!='eur'){
                        if(strtolower($rows[0]->currency)!='eur'&&(strtolower($rows[0]->currency)!=strtolower($out->currency))){
                            $price = ($rows[0]->vs*$rows[0]->currency_value);
                            $twoprice = ($rows[0]->vvs*$rows[0]->currency_value);
                            $threeprice = ($rows[0]->vvvs*$rows[0]->currency_value);
                            $childprice = ($rows[0]->bs*$rows[0]->currency_value);
                            $twochildprice = ($rows[0]->bxs*$rows[0]->currency_value);
                            $infantprice = ($rows[0]->infant*$rows[0]->currency_value);
                        } else {
                            $price = $rows[0]->vs;
                            $twoprice = $rows[0]->vvs;
                            $threeprice = $rows[0]->vvvs;
                            $childprice = $rows[0]->bs;
                            $twochildprice = $rows[0]->bxs;
                            $infantprice = $rows[0]->infant;
                        }
                    } else {
                        $price = $rows[0]->vs;
                        $twoprice = $rows[0]->vvs;
                        $threeprice = $rows[0]->vvvs;
                        $childprice = $rows[0]->bs;
                        $twochildprice = $rows[0]->bxs;
                        $infantprice = $rows[0]->infant;
                    }
                    
                    $room_type = $rows[0]->type_name;
                    
                    // markup multiplies %
                    $margin = ($price*($rows[0]->margin/100));
                    $twomargin = ($twoprice*($rows[0]->margin/100));

                    // discount
                    $disc = ($price*($rows[0]->discount/100));
                    $twodisc = ($twoprice*($rows[0]->discount/100));

                    // price per night = (normal price + markup) - discount
                    $pernight = ($price+$margin) - $disc;
                    $twopernight = ($twoprice+$twomargin) - $twodisc;

                    // accumulate
                    $person += ($pernight);
                    $twoperson += ($twopernight);

                    if(strtolower($threeprice)!='n/a'){
                        $threemargin = ($threeprice*($rows[0]->margin/100));
                        $threedisc = ($threeprice*($rows[0]->discount/100));
                        $threepernight = ($threeprice+$threemargin) - $threedisc;
                        $threeperson += ($threepernight);
                    } 

                    if(strtolower($childprice)!='n/a'){
                        $childmargin = ($childprice*($rows[0]->margin/100));
                        $childdisc = ($childprice*($rows[0]->discount/100));
                        $chilpernight = ($childprice+$childmargin) - $childdisc;
                        $child += ($chilpernight);
                    }

                    if(strtolower($twochildprice)!='n/a'){
                        $twochildmargin = ($twochildprice*($rows[0]->margin/100));
                        $twochilddisc = ($twochildprice*($rows[0]->discount/100));
                        $twochilpernight = ($twochildprice+$twochildmargin) - $twochilddisc;
                        $twochild += ($twochilpernight);
                    }

                    if(strtolower($infantprice)!='n/a'){
                        $infantmargin = ($infantprice*($rows[0]->margin/100));
                        $infantdisc = ($infantprice*($rows[0]->discount/100));
                        $infantpernight = ($infantprice+$infantmargin) - $infantdisc;
                        $infant += ($infantpernight);
                    }
                }
            }

            if($person!=0){
                // ROUND DOWN nearest 25
                //echo "1 ".$person."<br>2 ";
                //echo floor($person / 25) * 25;echo "<br>3 ";
                // ROUND UP nearest 25
                //echo ceil($person / 25) * 25;echo "<br>";
                //echo"<pre>";print_r($person);echo"</pre>";

                // flight + margin
                $flighttotal = $this->calculatePriceFlightsPerson($out, $in);

                $flightMargin = $flighttotal->adt_price*($rows[0]->margin/100);
                $flight = $flighttotal->adt_price+$flightMargin;
                
                // flight child
                $flightMarginChd = $flighttotal->chd_price*($rows[0]->margin/100);
                $flightChd = $flighttotal->chd_price+$flightMarginChd;

                // flight inf
                $flightMarginInf = $flighttotal->inf_price*($rows[0]->margin/100);
                $flightInf = $flighttotal->inf_price+$flightMarginInf;

                // transport + margin
                $transportMargin = $rows[0]->transfer*($rows[0]->margin/100); 
                $transport = $transportMargin+$rows[0]->transfer;

                $two_person = ($twoperson!=0)? floor(($twoperson + $flight) / 5) * 5 : 0;
                
                $flights->product_id = $grouptravel->id;
                $flights->room_type = $room_type;
                $flights->room_price_forone = ($person!=0)? floor(($person + $flight) / 5) * 5 : 0;
                $flights->room_price_fortwo_perperson = ($twoperson!=0)? floor(($twoperson + $flight) / 5) * 5 : 0;
                $flights->room_price_forthree_perperson = ($threeperson!=0)? floor(($threeperson + $flight) / 5) * 5 : 0;
                $flights->room_price_onechild = ($child!=0)? floor(($child + $flightChd) / 5) * 5 : 0;
                $flights->room_price_twochild = ($twochild!=0)? floor(($twochild + $flightChd) / 5) * 5 : 0;
                $flights->room_price_infant = (strtoupper($infant)!='N/A')? floor(($infant + $flightInf) / 5) * 5 : 0;
                $flights->room_price_fortwo = ($two_person*2);
                $flights->vs_margin = $person;
                $flights->vvs_margin = $twoperson;
                $flights->vvvs_margin = $threeperson;
                $flights->bs_margin = $child;
                $flights->bxs_margin = $twochild;
                $flights->infant_margin = $infant;
                $flights->transport_margin = $transport;
                $flights->price_adt_margin = $flight;
                $flights->price_chd_margin = $flightChd;
                $flights->price_inf_margin = $flightInf;
                $flights->currency = $out->currency;
                $flights->days = sizeof($days);
                $flights->total_nights = $totalNights;
                $flights->margin = $rows[0]->margin;
                $flights->outbound_id = $out->id;
                $flights->outbound_city_depart = $out->from;
                $flights->outbound_city_via = $out->via_city;
                $flights->outbound_city_destination = $out->to;
                $flights->outbound_depart_date = $out->depart_outbound;
                $flights->outbound_arrive_date = $out->arrival_outbound;
                $flights->outbound_seats = $out->seats;
                $flights->outbound_time_depart = $out->time_depart;
                $flights->outbound_time_arrive = $out->time_arrive;
                $flights->outbound_flight_code = $out->flight_code;
                $flights->inbound_id = $in->id;
                $flights->inbound_city_depart = $in->from;
                $flights->inbound_city_via = $in->via_city;
                $flights->inbound_city_destination = $in->to;
                $flights->inbound_depart_date = $in->depart_outbound;
                $flights->inbound_arrive_date = $in->arrival_outbound;
                $flights->inbound_seats = $in->seats;
                $flights->inbound_time_depart = $in->time_depart;
                $flights->inbound_time_arrive = $in->time_arrive;
                $flights->inbound_flight_code = $in->flight_code;
                $flights->package = $grouptravel;
                $flights->zoo_data = isset($grouptravel->zooitem_id) ? $this->getZooElementByItemId($grouptravel->zooitem_id) : '';
            }
        } 
        
        return $flights;
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
//                else if(trim($name)=='Category Subtitle Area') $zooElement[trim($type)][trim($name)] = $element->render(); 
            }            
//                $xml = $zoo->xml->loadString($element->toXML());
//                $zooElement[$type][$name] = (string)$xml->value;
        }
        
        return $zooElement;
    }
    
    function priceValidation($string, $total, $prices){
        $grupingroom = array();
        $rooms = explode("~~", $string);
        foreach($rooms as $key => $val){
            if($val!=''){
                $expl = explode("**", $val);
                $kunci = $expl[0];
                $isi = explode("__", $expl[1]);

                if($isi[0]=='title'){
                    if(strtolower($isi[1])=='hr'||strtolower($isi[1])=='fru'||strtolower($isi[1])=='frk'){
                        $grupingroom[$kunci]['adults'] += 1;
                        $grupingroom[$kunci]['child'] += 0;
                        $grupingroom[$kunci]['infant'] += 0;
                    } else if(strtolower($isi[1])=='dreng'||strtolower($isi[1])=='pige'){
                        $grupingroom[$kunci]['adults'] += 0;
                        $grupingroom[$kunci]['child'] += 1;
                        $grupingroom[$kunci]['infant'] += 0;
                    } 
                } else if($isi[0]=='fbabyname'){
                    $grupingroom[$kunci]['adults'] += 0;
                    $grupingroom[$kunci]['child'] += 0;
                    $grupingroom[$kunci]['infant'] += 1;
                }
            }
        }

        foreach($grupingroom as $room => $pax){
            if($pax['adults']==1){
                $grupingroom[$room]['price_adults'] = $prices->room_price_forone;
            } else if($pax['adults']==2){
                $grupingroom[$room]['price_adults'] = ($prices->room_price_fortwo_perperson*2);
            } else if($pax['adults']==3){
                $grupingroom[$room]['price_adults'] = ($prices->room_price_forthree_perperson*3);
            }

            if($pax['child']==1){
                $grupingroom[$room]['price_child'] = $prices->room_price_onechild; 
            } else if($pax['child']==2){
                $grupingroom[$room]['price_child'] = ($prices->room_price_twochild*2); 
            } else
                $grupingroom[$room]['price_child'] = 0; 

            if($pax['infant']==1){
                $grupingroom[$room]['price_infant'] = $prices->room_price_infant; 
            } else if($pax['infant']==2){
                $grupingroom[$room]['price_infant'] = ($prices->room_price_infant*2); 
            } else
                $grupingroom[$room]['price_infant'] = 0; 

            $grupingroom[$room]['total_room'] = $grupingroom[$room]['price_adults'] + $grupingroom[$room]['price_child'] + $grupingroom[$room]['price_infant'];
            $grupingroom['total_all_room'] += $grupingroom[$room]['total_room'];
        }

        // remove addons and insurance
//            $addon = $this->getAddon();
//            if($addon){
//                $days = abs(strtotime($flight->outgoing_date_departure) - strtotime($flight->return_date_arrival)) / (60 * 60 * 24);
//                $adultone = $addon->basic_price + ($addon->extra_day*($days-1)); 
//                $childone = $addon->chd_basic + ($addon->chd_extra*($days-1));
//                $infantone = 0;
//
//                // accumulate addon price and person with addon
//                $totaladdon = ($insurance[0]->text*$adultone)+($insurance[1]->text*$childone)+($insurance[2]->text*$infantone);
//            }
//            
//            if($cancel=='true'){
//                $sixtpercent = floor(($totaladdon+$grupingroom['total_all_room'])*6/100);
//            }
        $totaladdon = 0;
        $sixtpercent = 0;
        
        if(($sixtpercent+$totaladdon+$grupingroom['total_all_room'])==$total){
            return true;
        } else return false;
    }
    
    //$string, $pkg, $total, $client
    //$total, $client, $string, $pkgs, $bookflights, $flight, $insurance, $cancel
    function insertBooking($string, $pkg, $total, $client){
        
        $grupingroom = array();
        $rooms = explode("~~", $string);
        foreach($rooms as $key => $val){
            if($val!=''){
                $expl = explode("**", $val);
                $kunci = $expl[0];
                $isi = explode("__", $expl[1]);

                if($isi[0]=='title'){
                    if(strtolower($isi[1])=='hr'||strtolower($isi[1])=='fru'||strtolower($isi[1])=='frk'){
                        $grupingroom[$kunci]['adults'] += 1;
                        $grupingroom[$kunci]['child'] += 0;
                        $grupingroom[$kunci]['infant'] += 0;
                    } else if(strtolower($isi[1])=='dreng'||strtolower($isi[1])=='pige'){
                        $grupingroom[$kunci]['adults'] += 0;
                        $grupingroom[$kunci]['child'] += 1;
                        $grupingroom[$kunci]['infant'] += 0;
                    } 
                } else if($isi[0]=='fbabyname'){
                    $grupingroom[$kunci]['adults'] += 0;
                    $grupingroom[$kunci]['child'] += 0;
                    $grupingroom[$kunci]['infant'] += 1;
                }
                
                $grupingroom[$kunci]['data'][] = $expl[1];
            }
        }
        
        $totalpax = 0;
        foreach($grupingroom as $k => $v){
            $totalpax += $v['adults'] + $v['child'] + $v['infant'];
        }

        $available = $this->bookSeats($pkg, $totalpax);
        
        // no seats
        if(!$available)
            return false;
        
        // get cancelation addon
//        $data = $this->priceValidation($string, $total, $prices);
//        extract($data);

        $type = $pkg->package->travel_type;
        $pkgid = $pkg->package->id;

        $query = "SELECT  MAX(urut) as max
                    FROM    #__online_order_manager";

        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();
        $max =  ($rows[0]->max)+1;

        $ordernum = "RE".sprintf("%07d", $max);

        $fields = explode("**", $client);
        foreach($fields as $key => $val){
            $inputs = explode("__", $val);
            if($inputs[0]=='clientname')
                $clientname = $inputs[1];
            else if ($inputs[0]=='clientlastname')
                $clientlastname = $inputs[1];
            else if ($inputs[0]=='clientaddress')
                $clientaddress = $inputs[1];
            else if ($inputs[0]=='clientpostnr')
                $clientpostnr = $inputs[1];
            else if ($inputs[0]=='clientby')
                $clientby = $inputs[1];
            else if ($inputs[0]=='clientland')
                $clientland = $inputs[1];
            else if ($inputs[0]=='clientemail')
                $clientemail = $inputs[1];
            else if ($inputs[0]=='clienttele')
                $clienttele = $inputs[1];
            else if ($inputs[0]=='clientmobil')
                $clientmobil = $inputs[1];
            else if ($inputs[0]=='clientfirma')
                $clientfirma = $inputs[1];							
        }

        $sql = "INSERT INTO #__online_client_manager 
        (creation_date, first_name, last_name, address_one, zip_code, city_name, country, account_status, telephone_one, telephone_two, company, email)
        VALUES (".$this->_db->quote(date('Y-m-d')).", ".$this->_db->quote($clientname).", ".$this->_db->quote($clientlastname).", ".$this->_db->quote($clientaddress).", ".$this->_db->quote($clientpostnr).", ".$this->_db->quote($clientby).", ".$this->_db->quote($clientland).", 1, ".$this->_db->quote($clienttele).", ".$this->_db->quote($clientmobil).", ".$this->_db->quote($clientfirma).", ". $this->_db->quote($clientemail).")";        
        
        $this->_db->setQuery($sql);
        $this->_db->Query();
        $clientid = $this->_db->insertid();

        $sql = "INSERT INTO #__online_order_manager 
                        (client_id, order_number, order_date, order_total_amount, urut, travel_date) 
                        VALUES (".$clientid.", ".$this->_db->quote($ordernum).", ".$this->_db->quote(date('Y-m-d')).", ".$total.", ". $max .", ".$this->_db->quote($pkg->outbound_depart_date).")";        
        
        $this->_db->setQuery($sql);
        $this->_db->Query();
        $orderid = $this->_db->insertid();

        $fromairport = date("H:i:s", strtotime($pkg->outbound_time_arrive." +1 hour"));
        $gotoairport = date("H:i:s", strtotime($pkg->inbound_time_depart." -3 hours"));

        $sql = "INSERT INTO #__online_order_lines
                        (order_id, order_line_status, order_pkg_id, order_pkg_type, from_date, from_hour, to_date, to_hour, insurance_adt, 
                        insurance_chd, insurance_inf, flight_price_adt_margin, flight_price_chd_margin, flight_price_inf_margin, flight_code_depart, flight_code_return, 
                        booking_class_depart, booking_class_return, napi_external_id, napi_booking_reference, date_flight_depart, date_flight_return,
                        currency, vs_margin, vvs_margin, vvvs_margin, bs_margin, bxs_margin, infant_margin, transport_margin, margin, 
                        out_origin, out_via, out_destination, in_origin, in_via, in_destination)
                        VALUES (".(int)$orderid.", ". (int)3 .", ". (int)$pkgid .", ". $this->_db->quote($type) .", ".$this->_db->quote($pkg->outbound_arrive_date).", ".$this->_db->quote($fromairport).", ".$this->_db->quote($pkg->inbound_depart_date).", ".$this->_db->quote($gotoairport).", ".(int)$insurance[0]->value.", 
                        ".(int)$insurance[1]->value.", ".(int)$insurance[2]->value.", ". $this->_db->quote($pkg->price_adt_margin) .", ". $this->_db->quote($pkg->price_chd_margin) .", ". $this->_db->quote($pkg->price_inf_margin) .", ".$this->_db->quote($pkg->outbound_flight_code).", ".$this->_db->quote($pkg->inbound_flight_code)."
                        , ".$this->_db->quote($pkg->departure_class).", ".$this->_db->quote($pkg->return_class).", ".$this->_db->quote($empty).", ".$this->_db->quote($empty).", ".$this->_db->quote($pkg->outbound_depart_date."T".$pkg->outbound_time_depart).", ".$this->_db->quote($pkg->inbound_depart_date."T".$pkg->inbound_time_depart)."
                        , ".$this->_db->quote($pkg->currency).", ".$this->_db->quote($pkg->vs_margin).", ".$this->_db->quote($pkg->vvs_margin).", ".$this->_db->quote($pkg->vvvs_margin).", ".$this->_db->quote($pkg->bs_margin).", ".$this->_db->quote($pkg->bxs_margin).", ".$this->_db->quote($pkg->infant_margin).", ".$this->_db->quote($pkg->transport_margin).", ".$this->_db->quote($pkg->margin)."
                        , ".$this->_db->quote($pkg->outbound_city_depart).", ".$this->_db->quote($pkg->outbound_city_via).", ".$this->_db->quote($pkg->outbound_city_destination).", ".$this->_db->quote($pkg->inbound_city_depart).", ".$this->_db->quote($pkg->inbound_city_via).", ".$this->_db->quote($pkg->inbound_city_destination).")";        

        $this->_db->setQuery($sql);
        $this->_db->Query();
        $orderline = $this->_db->insertid();
        
        foreach($grupingroom as $key => $value){
            $title = '';
            $firstname = '';
            $lastname = '';
            $date = '';
            $args = array();
            
            $datapax = $value['data'];
            unset($value['data']);
            
            // insert order room
            $roomid = $this->insertOrderRooms($orderline, $value);
            
            foreach($datapax as $k => $v){ 
                $str = substr($v, 0, 5);

                if($str=='title'){
                    $ti = explode("__", $v);
                    $title = $ti[1];
                    $ta = explode("__", $datapax[$k+1]);
                    $firstname = $ta[1];
                    $to = explode("__", $datapax[$k+2]);
                    $lastname = $to[1];

                    if(strtolower($title)=='pige'||strtolower($title)=='dreng'){
                        $te = explode("__", $datapax[$k+3]);
                        $birthday = date('Y-m-d', strtotime($te[1]));
                    } 

                    $roombooking[$key][$title] += 1;
                    $passangers[] = $firstname." ".$lastname;
                    
                    $args = array('flightid' => $flightid, 'orderline' => $orderline, 'orderid' => $orderid, 'title' => $title, 'firstname' => $firstname, 'lastname' => $lastname, 'birthday' => $birthday, 'roomid' => $roomid);
                    $this->insertPassangers($args);
                    
                } else if($str=='fbaby'){
                    $title = "Baby";
                    $f = explode("__", $v);
                    $firstname = $f[1];
                    $l = explode("__", $datapax[$k+1]);
                    $lastname = $l[1];
                    $b = explode("__", $datapax[$k+2]);
                    $birthday = date('Y-m-d', strtotime($b[1]));

                    $roombooking[$key][$title] += 1;
                    $passangers[] = $firstname." ".$lastname;

                    $args = array('flightid' => $flightid, 'orderline' => $orderline, 'orderid' => $orderid, 'title' => $title, 'firstname' => $firstname, 'lastname' => $lastname, 'birthday' => $birthday, 'roomid' => $roomid);
                    $this->insertPassangers($args);
                } 
            }
        }

        $arrinsurance = array(
                                'insurance' => $insurance,
                                'cancelation'    => $sixtpercent
                            );

        if($this->sendClientMail($arrinsurance, $ordernum, $passangers, $pkg, $fields, $roombooking, $pkgid, $type))
            return $ordernum;
        else return false;
    }
    
    function bookSeats($pkg, $total){
        
        $db =& JFactory::getDBO();
        $sql = "SELECT  gt.id, gt.seats, seats_available, pkg.id as pkgid
                FROM    #__online_offline_grouptravels as gt, #__online_pkg_desc_test as pkg
                WHERE   ((gt.id = ".$db->quote($pkg->outbound_id)." AND grouptravel_out_flight = gt.id) OR (gt.id = ".$db->quote($pkg->inbound_id)." AND grouptravel_in_flight = gt.id))
                AND     pkg.id = ".$db->quote($pkg->package->id);
        /**
         *SELECT  gt.id, gt.seats, seats_available, pkg.id 
                FROM    j7xvz_online_offline_grouptravels as gt, j7xvz_online_pkg_desc_test as pkg
                WHERE   ((gt.id = '875' AND grouptravel_out_flight = gt.id) OR (gt.id = '876' AND grouptravel_in_flight = gt.id))
                AND     pkg.id = '123' 
         */
        
        $db->setQuery($sql);
        $data = $db->loadAssocList();
        
        $available = true;
        foreach($data as $k => $v){
            if($total>$v['seats']||$total>$v['seats_available']){
                $available = false;
                break;
            } 
        }
        
        if($available){
            foreach($data as $k => $v){
                $left = $v['seats'] - $total;
                
                $sql = "UPDATE  #__online_offline_grouptravels 
                        SET     seats = ".$left."
                        WHERE   id = ".$db->quote($v['id']);
                
                $db->setQuery($sql);
                $db->Query();
                
                // loop #1, decrement seat for pkg
                if($k==0){
                    $left_avail = $v['seats_available'] - $total;
                
                    $sql = "UPDATE  #__online_pkg_desc_test 
                            SET     seats_available = ".$left_avail."
                            WHERE   id = ".$db->quote($v['id']);

                    $db->setQuery($sql);
                    $db->Query();
                }
            }
            
            return true;
        } else return false;
    }
    
    function insertOrderRooms($orderline, $pax){
        
        switch (strtolower($pax['adults'])) {
                case 1:
                        $type = 'single';
                break;
                case 2:
                        $type = 'double';
                break;
                case 3:
                        $type = 'triple';
                break;
                default:
                        $type = 0;
                break;
        }

        $sql = "INSERT INTO #__online_order_room 
                (order_line_id, room_type)
                VALUES (".(int)$orderline.", ".$this->_db->quote($type).")";    

        $this->_db->setQuery($sql);
        $this->_db->Query();
        return $this->_db->insertid();
    }
    
    function insertPassangers($args){	
        
        // insert passanger list
        $sql = "INSERT INTO #__online_passenger_list 
                (order_id, order_line_id, title, first_name, last_name, birthday)
                VALUES (".(int)$args['orderid'].", ".(int)$args['orderline'].", ".$this->_db->quote($args['title']).", ".$this->_db->quote($args['firstname']).", ".$this->_db->quote($args['lastname']).", ".$this->_db->quote($args['birthday']).")";        

        $this->_db->setQuery($sql);
        $this->_db->Query();
        $passangerid = $this->_db->insertid();

        // insert room passenger
        $sql = "INSERT INTO #__online_room_passenger
                (room_id, passenger_id)
                VALUES (".(int)$args['roomid'].", ".(int)$passangerid.")";

        $this->_db->setQuery($sql);
        $this->_db->Query();

        // insert flight booking
//		$sql = "INSERT INTO #__online_flight_bookings
//                        (order_id, passanger_id, flight_id)
//                        VALUES (".(int)$args['orderid'].", ".(int)$passangerid.", ".$this->_db->quote($args['flightid']).")";        
//                
//		$this->_db->setQuery($sql);
//		$this->_db->Query();
    }
    
    function sendClientMail($arrinsurance, $ordernum, $passangers, $flight, $fields, $roombooking, $pkg, $type){
        extract($arrinsurance);

        require_once(JPATH_ROOT.'/libraries/pdf_kirim.php');
        require_once(JPATH_ROOT.'/libraries/newmail/mail.php');

        foreach($fields as $key => $val){
            $inputs = explode("__", $val);
            if($inputs[0]=='clientname')
                $clientname = $inputs[1];
            else if ($inputs[0]=='clientlastname')
                $clientlastname = $inputs[1];
            else if ($inputs[0]=='clientaddress')
                $clientaddress = $inputs[1];
            else if ($inputs[0]=='clientpostnr')
                $clientpostnr = $inputs[1];
            else if ($inputs[0]=='clientby')
                $clientby = $inputs[1];
            else if ($inputs[0]=='clientland')
                $clientland = $inputs[1];		
            else if ($inputs[0]=='clientemail')
                $clientemail = $inputs[1];
        }

        $booker = $clientname." ".$clientlastname."<br>".$clientaddress."<br>".$clientpostnr."<br>".$clientby."<br>".$clientland."<br>";
        $nama = $clientname." ".$clientlastname;

        foreach($passangers as $k => $v){
            if($k==0) $passangersname = $v;
            else $passangersname = $passangersname.", ".$v;
        }
        
        $packagename = $flight->package->product_name." ".$flight->room_type;

        $this->tcpdf = new PDFKirim();
        $this->surat = new newmail();

        foreach($roombooking as $key => $value){
            foreach($value as $k => $v){
                if($k=='Hr'||$k=='Fru'||$k=='Frk'){
                    $kamar[$key]['adt'] += $v;
                } else if($k=='Pige'||$k=='Dreng'){
                    $kamar[$key]['chd'] += $v;
                }  else if($k=='Baby'){
                    $kamar[$key]['inf'] += $v;
                }
            }
        }
        
        $i = 0;
        foreach($kamar as $tipe => $pax){
            $roomname = "";
            foreach($pax as $tipepax => $total){
                if($tipepax=='adt'){
                    $priceadt = 0;
                    if($total==1){
                        $priceadt = $flight->room_price_forone;
                        $roomname = " Enkeltværelse ";
                    } else if($total==2){
                        $priceadt = $flight->room_price_fortwo_perperson;
                        $roomname = " Dobbeltværelse ";
                    } else if($total==3){
                        $priceadt = $flight->room_price_forthree_perperson;
                        $roomname = " Tripleværelse ";
                    }

                    $row[$i] = array(
                                    'date_from' =>  date("d-m-Y", strtotime($flight->outbound_depart_date)),
                                    'date_to'   =>  date("d-m-Y", strtotime($flight->inbound_depart_date)),
                                    'product'   =>  $packagename.$roomname."(voksne)",
                                    'antal'     =>  $total,
                                    'desc'      =>  "",
                                    'price'     =>  $priceadt,
                                );
                }  else if($tipepax=='chd'){
                    $pricechd = 0;
                    if($total==1){
                        $pricechd = $flight->room_price_onechild;
                        $roomname = " Familieværelse ";
                    } else if($total==2){
                        $pricechd = $flight->room_price_twochild;
                        $roomname = " Familieværelse ";
                    } 

                    $row[$i] = array(
                                    'date_from' =>  date("d-m-Y", strtotime($flight->outbound_depart_date)),
                                    'date_to'   =>  date("d-m-Y", strtotime($flight->inbound_depart_date)),
                                    'product'   =>  $packagename.$roomname."(barn)",
                                    'antal'     =>  $total,
                                    'desc'      =>  "",
                                    'price'     =>  $pricechd,
                                );
                } else if($tipepax=='inf'){
                    $row[$i] = array(
                                    'date_from' =>  date("d-m-Y", strtotime($flight->outbound_depart_date)),
                                    'date_to'   =>  date("d-m-Y", strtotime($flight->inbound_depart_date)),
                                    'product'   =>  $packagename.$roomname."(baby)",
                                    'antal'     =>  $total,
                                    'desc'      =>  "",
                                    'price'     =>  $flight->room_price_infant,
                                );
                }
                $i++;
            }
        }

//        if($insurance[0]->text!=0)
//            $textins = " ".$insurance[0]->text." Voksen";
//
//        if($insurance[1]->text!=0)
//            $textins .= " ".$insurance[1]->text." Barn";
//
//        if($insurance[2]->text!=0)
//            $textins .= " ".$insurance[2]->text." Baby";
//
//        if($textins!=''){
//            $nuins = array(
//                            'date_from' =>  "",
//                            'date_to'   =>  "",
//                            'product'   =>  "Tilvalg Rejseforsikring for ".$textins,
//                            'antal'     =>  1,
//                            'desc'      =>  "",
//                            'price'     =>  ($insurance[0]->value*$insurance[0]->text)+($insurance[1]->value*$insurance[1]->text)+($insurance[2]->value*$insurance[2]->text),
//                            );
//
//            array_push($row, $nuins);
//        }

//        if($cancelation!=0){
//            $cancel = array(
//                            'date_from' =>  "",
//                            'date_to'   =>  "",
//                            'product'   =>  "Fakturabeløbet 6%",
//                            'antal'     =>  1,
//                            'desc'      =>  "",
//                            'price'     =>  $cancelation,
//                            );
//
//            array_push($row, $cancel);
//        }

        $flightdata = $this->getFlightDataExtract($flight);

        $namafile = "reservation ".date('H:m d-M-Y');
        $this->tcpdf->display($flightdata, $booker, $namafile, 
                                $row, 
                                $passangersname, 
                                $ordernum, 
                                "M Ajan", 
                                "mail@selected-tours.dk", 
                                date('d-M-Y', strtotime($flight->outgoing_date_departure)), 
                                date('d-M-Y'), 
                                "http://www.marokkoeksperten.dk");

        /**
            *Array
                (
                    [zooId] => Produkt ID
                    [hostSmtp] => mail.marokko-eksperten.dk
                    [portSmtp] => 25
                    [senderSmtp] => reservations@marokko-eksperten.dk
                    [senderSmtpPass] => xoxo1924
                    [ccMailOne] => Mail@rejse-eksperterne.dk
                    [ccMailTwo] => reservations@marokko-eksperten.dk
                ) 
            */
        $smtps = $this->getModuleParams('mod_roundtrip');
        extract($smtps);

        $pto = array(
                array('email'   => 	$ccMailOne, 'nama' =>  ''),
                array('email'   =>	$ccMailTwo, 'nama' =>	''),
                array('email'   =>  $clientemail, 'nama' => $nama)
//                    array('email'   =>  "milan_7mania@yahoo.com", 'nama' => "Priyo Wibowo")
        );            

        $subject = $mailSubject." (".$ordernum.")";//"Trip Reservation - Selected Tours";
        $pesan = "Hej ".$nama.",<br /><br />".$mailContent;

        //  $body = isi email
        //  $psubject = subject
        //  $phost = host smtp
        //  $pport = port smtp
        //  $pusername = username smtp
        //  $psandi = sandi smtp
        //  $pfrom = email pengirim
        //  $pfromname = nama pengirim
        //  $pto = array email penerima
        //			array(	
        //				array('email' => 'satu@satu.com', 'nama' => 'satu 1'),
        //				array('email' => 'dua@satu.com', 'nama' => 'satu 2'),
        //				array('email' => 'tiga@satu.com', 'nama' => 'satu 3')
        //				);
        //  $pto = email penerima
        //  $ptoname = nama penerima
        //  $patc = array attachment

        $hasil = $this->surat->sendmail($pesan, $subject, $hostSmtp, $portSmtp, $senderSmtp, 
                                                                $senderSmtpPass, $senderSmtp, "Marokko Eksperten", $pto, array(JPATH_ROOT.'/email_pdf/'.$namafile.'.pdf'));

//            return ;
//            $hasil = $this->tcpdf->mail_attachment($namafile.".pdf", $tujuan, $subject, $pesan);
        if($hasil==true)
            return true;
        else return false;

    }
    
    function getFlightDataExtract($flight){
        require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');

        $outgoing_origin = $this->getCityByCode($flight->outbound_city_depart);        
        $outgoing_destination = $this->getCityByCode($flight->outbound_city_destination);

        $return_origin = $this->getCityByCode($flight->inbound_city_depart);        
        $return_destination = $this->getCityByCode($flight->inbound_city_destination);        

        $data = array();
        $data['out_date_departure'] = $flight->outbound_depart_date;
        $data['out_time_departure'] = substr($flight->outbound_time_depart, 0, 5);
        $data['out_time_arrival'] = substr($flight->outbound_time_arrive, 0, 5);
        $data['out_city_departure'] = $outgoing_origin[0]['displayName'];
        $data['out_city_arrival'] = $outgoing_destination[0]['displayName'];
        $data['out_code_flight'] = $flight->outbound_flight_code;

        $data['in_date_departure'] = $flight->inbound_depart_date;
        $data['in_time_departure'] = substr($flight->inbound_time_depart, 0, 5);
        $data['in_time_arrival'] = substr($flight->inbound_time_arrive, 0, 5);
        $data['in_city_departure'] = $return_origin[0]['displayName'];
        $data['in_city_arrival'] = $return_destination[0]['displayName'];
        $data['in_code_flight'] = $flight->inbound_flight_code;

        return $data;
    }
    
    public function getCityByCode($code){
        $db =& JFactory::getDBO();
        $sql = "SELECT * FROM #__online_api_destination WHERE iataAirportCode = ".$db->quote($code);
        $db->setQuery($sql);
        return $db->loadAssocList();
    }
    
    function getModuleParams($module){
        $db =& JFactory::getDBO();
        $sql = "SELECT params FROM #__modules WHERE module = ".$db->quote($module);
        $db->setQuery($sql);
        $data = $db->loadAssocList();
        $params = json_decode($data[0][params], true);
        return $params;
    }
    
    public function checkSeatsAvailable($out, $in, $travel){
        $min = min($out, $in, $travel);
        if($min>0) return $min; else return false;
    }
};
