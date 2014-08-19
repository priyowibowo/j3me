<?php
/**
 * @version		$Id: banners.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

//error_reporting(E_ALL);
//ini_set("display_errors", 0); 

/**
 * @todo : add getNasFee
 */

/**
 * Banners component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class ApinorwegianHelper
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
        JSubMenuHelper::addEntry(JText::_('COM_APINORWEGIAN_SUBMENU_DESTINATION'), 'index.php?option=com_apinorwegian', $submenu == 'apinorwegians');
	JSubMenuHelper::addEntry(JText::_('COM_APINORWEGIAN_SUBMENU_ROUTES'), 'index.php?option=com_apinorwegian&view=routes', $submenu == 'routes');
        JSubMenuHelper::addEntry(JText::_('COM_APINORWEGIAN_SUBMENU_FLIGHTAVAIL'), 'index.php?option=com_apinorwegian&view=apinorwegian&layout=detil', $submenu = 'apinorwegian');
        JSubMenuHelper::addEntry(JText::_('COM_APINORWEGIAN_SUBMENU_FLIGHTSCHEDS'), 'index.php?option=com_apinorwegian&view=schedules', $submenu = 'schedules');        
    }
    
    /**
     * @param $externalId = unique ID
     * @param $languageCode = code language
     */
    public function APIqueryDestinations($externalId, $languageCode){
        $soap_client = ApinorwegianHelper::conectAPI();
        $tmp = $soap_client -> queryDestinations(
            array(
                'externalId'        => $externalId,
                'languageCode'      => $languageCode
            )
        );
//print_r($soap_client->__getLastRequest());
        return $tmp;
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
        /*$client = ApinorwegianHelper::conectAPI();
        
        $lang = new soapval('languageCode', false, 'EN', null, false);
        $exID = new soapval('externalId', false, 'SAMPLE_MAROKKOEKSPERTEN', null, false);
        $qd = new soapval('queryDestinations', false, array($exID, $lang), 'http://napi1350.ws.nbb.norwegian.no/', false);
        $bd = new soapval('Body', false, $qd, 'http://schemas.xmlsoap.org/soap/envelope/', false);
        $env = new soapval('Envelope', false, $bd, 'http://schemas.xmlsoap.org/soap/envelope/', false);
        $xml = $env->serialize('literal');
        print_r($xml);
        $result = $client->call('queryDestinations', $xml);    
        
        $err = $client->getError();
        
        if($err){
            echo '<h2>Constructor error2</h2><pre>' . $err . '</pre>';
            echo 'Req<pre>'.htmlspecialchars($client->request, ENT_QUOTES).'</pre>';
            echo 'Res<pre>'.htmlspecialchars($client->response, ENT_QUOTES).'</pre>';
        } else {
            if($client->fault){
                echo 'FAULT: '.$client->fault;
            } else {
                print_r($result);
            }
        }die;*/
        
        try {
            $soap_client = ApinorwegianHelper::conectAPI();
            
            $tmp = $soap_client->flightSearch(
                array(
                    'externalId' => $externalId, 
                    'currency' => $currency,
                    'origin' => $origin,
                    'destination' => $destination,
                    'outboundDate' => $outboundDate,
                    'roundtrip' => $roundtrip,
                )
            );
//            print_r($tmp);
//            print_r($soap_client->__getLastRequest());
//            print_r($soap_client->__getLastResponse());die;
            return $tmp;
        } catch (SoapFault $e){
            echo "ERROR : ";
            print_r($e->getMessage());
        }
    }
  
    /**
    * 
    * booking that is < 3 months in the testing server will return none/rejected
    * Create Reservation For Norwegian API Flight
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
            $soap_client = ApinorwegianHelper::conectAPI();  
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
        
//        if(!extension_loaded('soap')){
//            echo 'sini';
//           dl('soap.so'); // Actually a deprecated method. See "notes" at http://no.php.net/dl
//        } else echo 'sana';
        
        // production wsdl : https://agent.norwegian.no/api/2?wsdl
        // endpoint : https://agent.norwegian.no/api/2

        // test wsdl : http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2?wsdl
        // endpoint : http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2
        try {  
//            $cert = file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apinorwegian'.DS.'helpers'.DS.'napiwsdl.pem');
            
            $soapclient_options = array();
            $soapclient_options['location'] = "http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2";
            $soapclient_options['login'] = 'DYAPIREJSEEKSP';
            $soapclient_options['password'] = 'X6HeDJpu4';
            $soapclient_options['trace'] = 1;
//            $soapclient_options['local_cert'] = $cert;
//            $soapclient_options['trace'] = TRUE;
            
//            $wsdl = 'https://' . $soapclient_options['login'] . ':' . $soapclient_options['password'] . '@agent.norwegian.no/api/2?wsdl' ;
            $wsdl = 'http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2?wsdl' ;
            
            return new SoapClient($wsdl, $soapclient_options);
            
//            $wsdlf = file_get_contents($wsdl);
//            echo $wsdlf;
        } catch(SoapFault $e){
            echo 'ERROR Connection: ';print_r($e->getCode()); echo '<br>';
            print_r($e->getMessage());
//            print_r($e->getFile()); echo '<br>';
//            print_r($e->getLine()); echo '<br>';
//            print_r($e->getTrace()); echo '<br>';
//            print_r($e->getTraceAsString());
        }
        
        /*include_once (JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apiarabia'.DS.'lib'.DS.'nusoap.php');

        $wsdl = 'http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2?wsdl';
        $client = new nusoap_client($wsdl, true);
        $client->soap_defencoding = 'UTF-8'; 
        $client->decode_utf8 = false;
        $client->setEndpoint('http://external-nbb.napi.norwegian.no.stage.osl.basefarm.net/api/2');
        $client->setCredentials('DYAPIREJSEEKSP', 'X6HeDJpu4');
        
        $err = $client->getError();
        
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        } else {
            return $client;
        }*/
    }
  
    /**
    * Grouping flights and prices
    */
    private function APIflightsearchprivate($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip){
        $hasil = ApinorwegianHelper::APIflightsearch($externalId, $currency, $origin, $destination, $outboundDate, $roundtrip);
        
        if(empty($hasil->priceLists))
            return false;

        if(is_object($hasil->priceLists))
            $pricelist[0] = $hasil->priceLists;
        else if(is_array($hasil->priceLists))
            $pricelist = $hasil->priceLists;

        if(is_array($hasil->flightLegQueryResponses->flightLegs))
            $tampilkan = $hasil->flightLegQueryResponses->flightLegs;
        else
            $tampilkan[0] = $hasil->flightLegQueryResponses->flightLegs;

        // filter, if $hours query is set	
        $tampil_tmp = array();
        foreach($tampilkan as $key => $tmpl){
            if(is_object($tmpl->flights)){
                // attach duration to non transit flight
                $tmpl->flights->duration = $tmpl->duration;
                if(isset($fromhour)&&isset($tohour)){
                    $tanggal = ApinorwegianHelper::pecahjam($tmpl->flights->departureTime);
                    if($tanggal >= $fromhour && $tanggal <= $tohour)
                    $tampil_tmp[$tmpl->flights->flightCode] = $tmpl->flights; 
                } else $tampil_tmp[$tmpl->flights->flightCode] = $tmpl->flights;
            }
            else if(is_array($tmpl->flights)){
                foreach($tmpl->flights as $temp){
                    if(isset($fromhour)&&isset($tohour)){
                        $tanggal = ApinorwegianHelper::pecahjam($temp->departureTime);
                        if($tanggal >= $fromhour && $tanggal <= $tohour)
                        $tampil_tmp[$temp->flightCode] = $temp;
                    } else $tampil_tmp[$temp->flightCode] = $temp;
                }
            }
        }

        foreach($tampil_tmp as $key=>$tmplny){
            $jembatan = $tmplny->fareInfoRef;
            foreach($pricelist as $prcl){
                if($prcl->fareInfoRef == $jembatan){
                    $tampil_tmp[$key]->pricelistny = $prcl->bookingClassFareInfos;
                }
            }
        }
        
        return $tampil_tmp;
    }
  
    public function pecahjam($tanggal){
        $tanggal_jam = explode('T', $tanggal);
        $jam_hour = explode(":", $tanggal_jam[1]);
        return $jam_hour[0];
    }

    public function formatDisplayDate($date, $format = true){
        $lang =& JFactory::getLanguage();
        
        if($lang->getTag()=='en-GB'){
          setlocale(LC_ALL, 'da_DK');  
        } else {
          $locale = explode("-", $lang->getTag());
          setlocale(LC_ALL, implode("_", $locale));   
        } 
        
        if($format)
            return htmlentities(ucfirst(strftime("%A %d. %B %Y", strtotime($date))));
        else
            return htmlentities(ucfirst(strftime("%d. %B %Y", strtotime($date)))); 
    }
    
    /**
     * @todo : pindahkan fungsi ini ke komponen "api manager", dimana nanti disana adalah tempat pengaturan pemanggilan dan pengembalian data flight
     */
    public static function APIflightsearchSrc($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf = null, $roundtrip = false, $totalpassangers = 0, $srcapi = 'norwegian'){
        if($srcapi=='')
            $srcapi = 'norwegian';
        
        if($srcapi=='norwegian'){
            $hasil = ApinorwegianHelper::APIflightsearchprivateclass($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip, $totalpassangers);
        } else { // if($srcapi=='arabia')
            require_once(JPATH_ROOT.'/administrator/components/com_apiarabia/helpers/apiarabia.php');
            $hasil = ApiarabiaHelper::APIflightsearchprivateclass($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip);    
        }
        
        return $hasil;
    }  
    
    public static function APIflightsearchprivateclass($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf = null, $roundtrip = false, $totalpassangers = 0){
        // dipake buat konek ke server yang terhubung ke napi
        $vars = json_encode(array(
                        'curr' => $currency,
                        'from' => $origin,
                        'to' => $destination,
                        'depart' => $outboundDate,
                        'passangers' => $totalpassangers,
                        'pass' => 'mammamia2013'
                    ));
        
        $url="http://www.marokkoeksperten.dk/new/index.php?option=com_rthelper&task=getFlightsRemote&vars=".$vars;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);    
        return unserialize($result);
        
        // dipake buat server yang terhubung langsung ke napi
//        $hasil = ApinorwegianHelper::APIflightsearchprivate($fromhour, $tohour, $externalId, $currency, $origin, $destination, $outboundDate, $classf, $roundtrip);
//        
//            if(!$hasil) return false;
//
//            $hasil = ApinorwegianHelper::removeTransits($hasil, $classf, $origin, $destination);
//            
//            if(!$hasil) return false;
//            $cheapestflightclass = ApinorwegianHelper::getCheapestAllFlights($hasil, $totalpassangers); 
//            
//            if(is_array($cheapestflightclass)){
//                foreach($cheapestflightclass as $code => $data){
//                    foreach($hasil as $flight => $info){
//                        if($flight==$code){
//                            // remove other class that is not cheap
//                            foreach($info->bookingClasses as $i => $obj){
//                                if($obj->code!=$data['bookingClass']){
//                                    unset($hasil[$flight]->bookingClasses[$i]);
//                                }
//                            }
//
//                            // remove pricelist that is not in the cheapest bookingClass
//                            foreach($info->pricelistny as $i => $obj){
//                                if($obj->bookingClass!=$data['bookingClass'])
//                                    unset($hasil[$flight]->pricelistny[$i]);
//                            }
//                        }
//                    }
//                } 
//            }
//            
//            // get the 1st flight, remove others
//            $i = 0;
//            foreach($hasil as $k => $v){
//                if($i==0) $hasil[$k] = $hasil[$k];
//                else unset($hasil[$k]);
//                $i++;
//            }
//            
//            return $hasil;
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
                $seats = ApinorwegianHelper::getSeatAvailableByClass($hsl->bookingClasses, $price->bookingClass);
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
    }
