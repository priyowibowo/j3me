<?php
/**
 * @version		$Id: apiarabia.php 20196 2012-10-01 02:40:25Z ian $
 * @copyright           Copyright (C) 2011 - 2012 Rejse-Eksperterne
 */

/**
 *
 * @package		Joomla.Administrator
 * @subpackage          com_apiarabia
 * @since		1.6
 */

include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apiarabia'.DS.'lib'.DS.'nusoap.php');
include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apiarabia'.DS.'lib'.DS.'class.wsdlcache.php');

class ApiarabiaHelper
{    
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */

    public static function addSubmenu($submenu){
        JSubMenuHelper::addEntry(JText::_('COM_APIARABIA_SUBMENU_DESTINATION'), 'index.php?option=com_apiarabia', $submenu == 'apiarabias');
	JSubMenuHelper::addEntry(JText::_('COM_APIARABIA_SUBMENU_ROUTES'), 'index.php?option=com_apiarabia&view=routes', $submenu == 'routes');
        JSubMenuHelper::addEntry(JText::_('COM_APIARABIA_SUBMENU_FLIGHTAVAIL'), 'index.php?option=com_apiarabia&view=apiarabia&layout=detil', $submenu = 'apiarabia');
        JSubMenuHelper::addEntry(JText::_('COM_APIARABIA_SUBMENU_FLIGHTSCHEDS'), 'index.php?option=com_apiarabia&view=schedules', $submenu = 'schedules');        
    }
    
    /**
     * @param $externalId = unique ID
     * @param $languageCode = code language
     */
    public function APIqueryDestinations($externalId, $languageCode){
        $soap_client = ApiarabiaHelper::conectAPI();
        $tmp = $soap_client -> queryDestinations(
            array(
                'externalId'        => $externalId,
                'languageCode'      => $languageCode
            )
        );
    
        return $tmp;
    }
    
    private function nusoapWsseHeader(){
        
        $header = "<wsse:Security xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\">"; // soap:mustUnderstand=\"1\"
            $header .= "<wsse:UsernameToken xmlns:wsu=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd\" wsu:Id=\"UsernameToken-17855236\">";
                $header .= "<wsse:Username>WSREJSE</wsse:Username>";
                $header .= "<wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">password123</wsse:Password>";
            $header .= "</wsse:UsernameToken>";
        $header .= "</wsse:Security> ";
        
        return $header;
    }
    
    /**
    * @param $externalId = unique id 
    * @param $currency = currency
    * @param $origin = From
    * @param $destination = To
    * @param $outboundDate = date
    * @param $roundtrip = round trip 
    */ 
    private function APIflightsearch($externalId, $currency, $origin, $destination, $outboundDate, $roundtrip = false){
        $timestamp1 = date('Y-m-d');
        $timestamp2 = date('H:i:s');
                
//        $mtime = microtime();
//   $mtime = explode(" ",$mtime);
//   $mtime = $mtime[1] + $mtime[0];
//   $starttime = $mtime;
   
        $client = ApiarabiaHelper::conectAPI();
        
        $header = ApiarabiaHelper::nusoapWsseHeader();
        
        $inf = new soapval('PassengerTypeQuantity', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('Quantity' => '1', 'Code' => 'INF')); 
        $chd = new soapval('PassengerTypeQuantity', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('Quantity' => '1', 'Code' => 'CHD')); 
        $adt = new soapval('PassengerTypeQuantity', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('Quantity' => '1', 'Code' => 'ADT')); 
        $traveller = new soapval('AirTravelerAvail', false, array($adt, $chd, $inf), 'http://www.opentravel.org/OTA/2003/05', false); 
        $travelinfo = new soapval('TravelerInfoSummary', false, $traveller, 'http://www.opentravel.org/OTA/2003/05', false); 

        $departuretime = new soapval('DepartureDateTime', false, $outboundDate, 'http://www.opentravel.org/OTA/2003/05', false); //.
        $origloc = new soapval('OriginLocation', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('LocationCode' => $origin)); //
        $destloc = new soapval('DestinationLocation', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('LocationCode' => $destination)); //
        $origindestination = new soapval('OriginDestinationInformation', false, array($departuretime, $origloc, $destloc), 'http://www.opentravel.org/OTA/2003/05', false); 

        $requestor = new soapval('RequestorID', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('Type' => '4', 'ID' => 'WSREJSE')); 
        $channel = new soapval('BookingChannel', false, null, 'http://www.opentravel.org/OTA/2003/05', false, array('Type' => "12"));
        $source = new soapval('Source', false, array($requestor, $channel), 'http://www.opentravel.org/OTA/2003/05', false, array('TerminalID' => 'TestUser/Test Runner'));
        $pos = new soapval('POS', false, $source, 'http://www.opentravel.org/OTA/2003/05', false);
        $ota = new soapval('OTA_AirAvailRQ', false, array($pos, $origindestination, $travelinfo), 'http://www.opentravel.org/OTA/2003/05', false, array('TimeStamp' => $timestamp1.'T'.$timestamp2, 'Version' => '20061.00', 'Target' => 'Test', 'PrimaryLangID' => 'en-us'));
        $xml = $ota->serialize('literal');

        $client->setHeaders($header);
        $result = $client->call('getAvailability', $xml);    

        $err = $client->getError();
        if($err){
            echo '<h2>Constructor error2</h2><pre>' . $err . '</pre>';
            echo '<pre>'.htmlspecialchars($client->request, ENT_QUOTES).'</pre>';
        } else {
            if($client->fault){
                echo 'FAULT: '.$client->fault;
            } else {
//                $mtime = microtime();
//   $mtime = explode(" ",$mtime);
//   $mtime = $mtime[1] + $mtime[0];
//   $endtime = $mtime;
//   $totaltime = ($endtime - $starttime);
//   echo '<pre>'.htmlspecialchars($client->request, ENT_QUOTES).'</pre>';
//   echo '<pre>'.htmlspecialchars($client->response, ENT_QUOTES).'</pre>';
//   echo "This page was created in ".$totaltime." seconds";
//   die;
   
                return $result;
            }
        }
    }
  
    /**
    * 
    * booking that is < 3 months in the testing server will return none/rejected
    * Create Reservation For Arabia API Flight
    * @param array(
                'travelers' =>  array( 
                                            'id' => '1',
                                            'child' => false,
                                            'firstname' => 'priyo',
                                            'lastname' => 'wibowo',
                                            'gender' => 'MALE'

                                    ),   // if more than one travelers, use the same array format as flightLegs                             
                    'flightLegs' => array(
                                        array(
                                        'id' => '1', // if book more than one, than this is crucial (if using return flight)
                                        'departureTime' => '2012-05-20T19:00:00+02:00',
                                        'flightCode' => 'DY4120',
                                        'destination' => 'ARN',
                                        'origin' => 'OSL',
                                        'bookingClass' => 'B'
                                        ),
                                        array(
                                        'id' => '2', // if book more than one, than this is crucial (if using return flight)
                                        'departureTime' => '2012-07-20T09:10:00+02:00',
                                        'flightCode' => 'DY805',
                                        'destination' => 'OSL',
                                        'origin' => 'ARN',
                                        'bookingClass' => 'B'
                                        )                                        
                                    ),
                    'currency' => 'NOK',
                    'hideTotalFareOnReceipt' => false,                
                    'contactEmails' => 'priyo.wbw@gmail.com',
                    'contactTelephones' => array(       
                                            '_' =>   '4799999999',                             
                                            'type' => 'MOBILE'
                                        ),
                    'creditcard' => array(
                                        'travelAccount' => false,
                                        'type' => 'VI',
                                        'number' => '4444333322221111',
                                        'expiryDate' => '1214',
                                        'cvc' =>  '123',
                                        'nameOnCard' => 'priyo'  
                                    )                     
                )
    */
    public static function createReservationAPI($data){
        extract($data);
        
        // ||empty($payment)
        if(empty($travelers)||empty($flightLegs)||empty($currency)||!isset($hideTotalFare)||empty($paymentType))
            return array('Can Not Create Reservation: Parameter Missing.');

        try {
            $soap_client = ApiarabiaHelper::conectAPI();  
            $reservation = $soap_client->createReservation(
                    array(
                        'travelers' =>  $travelers,                               
                        'flightLegs' => $flightLegs,
                        'currency' => $currency,
                        'hideTotalFareOnReceipt' => $hideTotalFare,                
                        'contactEmails' => 'Mail@rejse-eksperterne.dk', //  priyo.wbw@gmail.com
                        'contactTelephones' => array("_" => '+4533242322', 'type' => 'LANDLINE')
                    )    
                );
//            'creditcard' => array(
//                                        'travelAccount' => false,
//                                        'type' => 'VI',
//                                        'number' => '4444333322221111',
//                                        'expiryDate' => '1214',
//                                        'cvc' =>  '123',
//                                        'nameOnCard' => 'priyo'  
//                                    )     
//              'creditcard' => $payment 
//    print_r($soap_client->__getLastRequest());

            return $reservation;
        } catch (SoapFault $e) {
            $obj->operationResult->description = $e->getMessage();
            return $obj;
        }  
    }
  
    private function conectAPI(){
//        $wsdlurl = 'http://airarabia3o.isaaviations.com/webservices/services/AAResWebServices?wsdl';
        //
        $wsdlurl = 'http://marokko-eksperten.dk/web/administrator'.DS.'components'.DS.'com_apiarabia'.DS.'helpers'.DS.'wsdls/AAResWebServices.xml';
        
//        $cache = new nusoap_wsdlcache(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apiarabia'.DS.'lib'.DS.'tmp', 2592000);
//        $wsdl = $cache->get($wsdlurl);
//        if(is_null($wsdl)){
//            $wsdl = new wsdl($wsdlurl, '', '', '', '', 5);
//            $cache->put($wsdl);
//        }
        
        $client = new nusoap_client($wsdlurl, true);
        $client->soap_defencoding = 'UTF-8'; 
        $client->decode_utf8 = false;

        $err = $client->getError();
        
        if ($err) {
                echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        } else {
            return $client;
        }
        
    }
  
    /**
    * @todo : process hasil 
    */
    private function APIflightsearchprivate($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip){
        // get offline price
        $hasil = ApiarabiaHelper::APIflightsearchOffline($externalId, $currency, $origin, $destination, $outboundDate, $roundtrip);
        
//        $hasil = ApiarabiaHelper::APIflightsearch($externalId, $currency, $origin, $destination, $outboundDate, $roundtrip);
        
        // cek ada hasil atau tidak, kalo tidak return false (sementara mungkin di return semua hasilnya)
        if(sizeof($hasil['OriginDestinationInformation']['OriginDestinationOptions'])<1){
            return false;
        } else return $hasil;
        
        // cek direct flight - asumsi sementara hanya ada direct flight, karena dibatasi oleh airarabia untuk query hanya BLL ke RAK dan sebaliknya
                
        // cek ada berapa penerbangan dan ambil penerbangan paling murah
        
        // buat array baru sesuai dengan napi
        
    }
  
    public function pecahjam($tanggal){
        $tanggal_jam = explode('T', $tanggal);
        $jam_hour = explode(":", $tanggal_jam[1]);
        return $jam_hour[0];
    }

    public function formatDisplayDate($date, $format = true){
        setlocale(LC_ALL, 'da_DK');
        if($format)
            return htmlentities(ucfirst(strftime("%A %d, %B %Y", strtotime($date))));
        else
            return htmlentities(ucfirst(strftime("%d, %B %Y", strtotime($date)))); 
    }
  
    /**
     *
     * @to-do : change based on airarabia
     */
    public static function APIflightsearchprivateclass($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf = null, $roundtrip = false, $totalpassangers = 0){  
            $hasil = ApiarabiaHelper::APIflightsearchprivate($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip);
            
            // cek ada hasil atau tidak, kalo tidak return false (sementara mungkin di return semua hasilnya)
            if(sizeof($hasil['OriginDestinationInformation']['OriginDestinationOptions'])<1){
                return false;
            } else return $hasil;            
    }
  
    /** 
    * Get cheapest class from all flight, no transit
    * @param $hasil array all flight grouped with prices 
    * @return array of code flight and class with cheapest price 
    */
    private function getCheapestAllFlights($hasil, $passangers){
        // get all price for adults 
        $priceclass = array();
        foreach($hasil as $k_hsl => $hsl){
            
            // remove pricelists that are not available (from bookingClasses)
            if(is_object($hsl->bookingClasses)){
                foreach($hsl->pricelistny as $index => $price){
                    if($price->bookingClass!=$hsl->bookingClasses->code){
                        unset($hasil[$k_hsl]->pricelistny[$index]);
                    }
                }
            } else {
                foreach($hsl->pricelistny as $index => $price){
                    $found = false;
                    foreach($hsl->bookingClasses as $k => $v){
                        if($v->code==$price->bookingClass){
                            $found = true;
                        }
                    }
                    
                    if(!$found){
                        unset($hasil[$k_hsl]->pricelistny[$index]);
                    }
                }
            }
            
            // filter seat and price by class if available is > $passangers (default is 0)
            foreach($hsl->pricelistny as $price){
                $seats = ApiarabiaHelper::getSeatAvailableByClass($hsl->bookingClasses, $price->bookingClass);
                if($price->paxType=='ADT'&&($seats>=$passangers)&&($price->fareType!='FULLFLEX')){
                    $priceclass[$k_hsl][] = array('bookingClass' => $price->bookingClass, 'amount' => $price->totalFare->amount, 'available' => $seats);
                }
            }
            
            // get cheapest class
            foreach($priceclass as $code => $price){  
                $size = sizeof($price);     
                for($i = 0; $i<($size-1); $i++){
                    for($j = $i+1; $j<$size; $j++){
                        if($price[$i]['amount']>$price[$j]['amount'])
                            unset($priceclass[$code][$i]);
                        else if($price[$i]['amount']<$price[$j]['amount'])
                            unset($priceclass[$code][$j]);
                    }
                }
            }

            foreach($priceclass as $k => $v){
                foreach($v as $key => $value)
                    $prices[$k] = $value;
            }
        }

        return $prices;
    }
  
    // remove transits and unwanted classes
    private function removeTransits($hasil, $classf, $origin, $destination){
        foreach($hasil as $k_hsl  => $hsl){
        if($hsl->origin != $origin || $hsl->destination != $destination){
            unset($hasil[$k_hsl]);
            }

        if($classf!=''){
            if(!is_array($hsl->bookingClasses)){
            if($hsl->bookingClasses->code != $classf)
                $hasil[$k_hsl]->bookingClasses = array();
            else
                $hasil[$k_hsl]->bookingClasses[0] = $hsl->bookingClasses;
            }
            else{
            foreach($hsl->bookingClasses as $k_bking => $bking){
                if($bking->code != $classf)
                unset($hasil[$k_hsl]->bookingClasses[$k_bking]);
            }
            }

            foreach($hsl->pricelistny as $k_pricel => $pricel){
            if($pricel->bookingClass != $classf)
                unset($hasil[$k_hsl]->pricelistny[$k_pricel]);
            }
        }

        if(!$hasil[$k_hsl]->bookingClasses)
            unset($hasil[$k_hsl]);
        } 

        return $hasil;
    } 
  
    private function getSeatAvailableByClass($bookingClasses, $class){
        foreach($bookingClasses as $k => $v){
            if($v->code==$class){
                return $v->seatsAvailable;
            }
        }
    }

    /**
    * 
    * Get destination informations
    * @param array('OSL', 'RAK', ...) $iataairportcode
    * @return array object of informations 
    */
    public static function getDestination($iataairportcode = array()){
        $db = JFactory::getDBO();
        $query_in = $db->getQuery(true);
        $sql = "SELECT * FROM #__online_api_destination";

        if(sizeof($iataairportcode)>=1){
            foreach($iataairportcode as $k => $v){
            if($k==0)
                $sql .= " WHERE iataAirportCode = '".$v."'";
            else
                $sql .= " OR iataAirportCode = '".$v."'";
            }
        }    

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
    
    private static function APIflightsearchOffline($externalId, $currency, $origin, $destination, $outboundDate, $roundtrip){
        
        $departing = explode("T", $outboundDate);
        
        $db = JFactory::getDBO();
        $query_in = $db->getQuery(true);
        $sql = "SELECT * 
                FROM #__online_api_schedule
                WHERE `from` = ".$db->quote($origin)."
                AND `to` = ".$db->quote($destination)."
                AND `depart_outbound` = ".$db->quote($departing[0])."
                AND api != 'arabia'
                AND api != 'norwegian'
                AND `seats` > 0";

        $db->setQuery($sql);
        $db->Query();
        
        $flight = $db->loadObjectList();
        // format of airarabia
        $result = array();
        $result['OriginDestinationInformation']['OriginDestinationOptions'] = array(array('Data is from db, not real time'), array());
        $result['OriginDestinationInformation']['DestinationLocation']['!LocationCode'] = $flight[0]->to;
        $result['OriginDestinationInformation']['DepartureDateTime'] = $flight[0]->depart_outbound."T".$flight[0]->time_depart;
        $result['OriginDestinationInformation']['ArrivalDateTime'] = $flight[0]->arrival_outbound."T".$flight[0]->time_arrive;
        
        switch ($flight[0]->from) {
            case 'BLL':
                $ori = "Billund";
                break;
            case 'CPH':
                $ori = "København";
                break;
            case 'RAK':
                $ori = "Marrakech";
                break;
            case 'AGA':
                $ori = "Agadir";
                break;
            default:
                break;
        }
        
        $result['OriginDestinationInformation']['OriginLocation']['!'] = $ori;
        $result['OriginDestinationInformation']['OriginLocation']['!LocationCode'] = $flight[0]->from;
        $result['AAAirAvailRSExt']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]['FareBasisCodes']['FareBasisCode'] = '';
        $result['AAAirAvailRSExt']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]['PassengerFare']['TotalFare']['!Amount'] = $flight[0]->adt_price;
        $result['AAAirAvailRSExt']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][1]['PassengerFare']['TotalFare']['!Amount'] = $flight[0]->chd_price;
        $result['AAAirAvailRSExt']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][2]['PassengerFare']['TotalFare']['!Amount'] = $flight[0]->inf_price;
        $result['AAAirAvailRSExt']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]['PassengerFare']['TotalFare']['!CurrencyCode'] = $flight[0]->currency;
        $result['OriginDestinationInformation']['OriginDestinationOptions']['OriginDestinationOption']['FlightSegment']['!FlightNumber'] = $flight[0]->flight_code;
        return $result;
    }
}
