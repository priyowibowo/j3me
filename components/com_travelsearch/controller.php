<?php
/**
 * @version	$Id: controller.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage	com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Search Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_search
 * @since 1.5
 */
class TravelSearchController extends JController{
    /**
     * Method to display a view.
     */
    public function display($cachable = false, $urlparams = false){
        require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');
	
        $session =& JFactory::getSession();
        $model = $this->getModel();
        
        $searchagain = $session->get('com_travelsearch_again', 0);
        $typesearch = $session->get('com_travelsearch_type', 0);
        
        if($searchagain){
            //reset search from redirect, if no price (flight and room) found
            $datasearch = unserialize($session->get('mod_travelsearch_data'));    
            extract($datasearch);
            
            if($typesearch==1){
                if($type=='A'||$type=='D'||$type=='I'){
                    $dest = explode("-", $todest);
                    $copydest = (isset($dest[1])) ? $dest[0]."-".$dest[1] : $dest[0];

                    $transport = false;
                    if(!empty($dest[1])){
                        $transport = true;
                        $cityhotel = $dest[1];
                    } else 
                        $cityhotel = $dest[0];

                    $todest = $dest[0];
                }
                
                // shift date flight to nearest available date
                $fromDate = $model->getNextFlight($fromdest, $todest, $fromdate);
                $toDate = $model->getNextFlight($todest, $fromdest, $todate);
                
                JFactory::getApplication()->enqueueMessage('Vi beklager, der er det fly for udvalgte datoer fuldt bookede.', 'error');
            }
            
            $datasession = array( 
                            "type"          => $type,
                            "fromdest"      => $fromdest,
                            "todest"        => $copydest,
    //                        "category"      => $category,
    //                        "accomodation"  => $accomodation,
                            "roundtrip"     => $roundtrip,
                            "combi"         => $combi,
                            "fromdate"      => $fromDate,
                            "currency"      => $currency,
                            "todate"        => $toDate,
                            "rooms"         => $rooms,
                            "srcapi"        => $srcapi
                        );
            
            $session->set('com_travelsearch_again', 0);
            $session->set('com_travelsearch_type', 0);
            $session->set('mod_travelsearch_data', serialize($datasession));
        } else {
            $type = JRequest::getVar('holidayType', '0');
            $fromdest = JRequest::getVar('fromdest', '-');

            if($type=='A'||$type=='D'||$type=='I'){
    //            $category = JRequest::getVar('category', '-');
    //            $accomodation = JRequest::getVar('accomodation', '-');                    
                $dest = explode("-", JRequest::getVar('todest', '-'));

                $copydest = (isset($dest[1])) ? $dest[0]."-".$dest[1] : $dest[0];

                $transport = false;
                if(!empty($dest[1])){
                    $transport = true;
                    $cityhotel = $dest[1];
                } else 
                    $cityhotel = $dest[0];

                $todest = $dest[0];

            } else if($type=='R'){
                $roundtrip = JRequest::getVar('roundtrip', '-');
                $todest = JRequest::getVar('todest', '-');
            } 

            // DD-MM-YYYY
            $fromDate = JRequest::getVar('fromDate', '-');
            $toDate = JRequest::getVar('toDate', '-');

            $currency = JRequest::getVar('currency', '-');
            $srcapi = base64_decode(JRequest::getVar('mod_srcapi', '-'));

            $rooms = array();
            $numAdults = 0;
            $numChild = 0;
            $numInfants = 0;
            for($i = 1; $i <= 20; $i++){
                $adult = JRequest::getVar('adultroom'.$i, '0');
                $child = JRequest::getVar('childroom'.$i, '0');
                $baby = JRequest::getVar('babyroom'.$i, '0');
                if(isset($adult)&&$adult!=0){
                    $rooms[$i] = array('adults' => $adult, 'child' => $child, 'infants' => $baby);
                    $numAdults += $adult;
                    $numChild += $child;
                    $numInfants += $baby;
                }
            }

            $datasession = array( 
                            "type"          => $type,
                            "fromdest"      => $fromdest,
                            "todest"        => $copydest,
    //                        "category"      => $category,
    //                        "accomodation"  => $accomodation,
                            "roundtrip"     => $roundtrip,
                            "combi"         => $combi,
                            "fromdate"      => $fromDate,
                            "currency"      => $currency,
                            "todate"        => $toDate,
                            "rooms"         => $rooms,
                            "srcapi"        => $srcapi
                        );

            $session->set('mod_travelsearch_data', serialize($datasession));
        }
                
	$passangers = array('adults' => $numAdults, 'child' => $numChild, 'infants' => $numInfants);  
        
        // sets the view to someview.html.php
        $view = & $this->getView('travelsearch', 'html');        
        
        if(!empty($fromDate)){
            $departure = $model->formatDate($fromDate);
            $departureDate = $departure."T00:00:00";
            
            if(isset($fromdest)&&isset($todest)){
                if($type=='R'){
                    $id = $roundtrip;
                    $period = $model->getPeriodPackageByID($id);
                    $toDate = date("d-m-Y", strtotime('+'.$period[0]->period.' days', strtotime($fromDate)));
                    $destcode = $model->getDestinationCodeByID($id);
                    $todest = $destcode[0]->destination_city_code;
                } else if ($type=='I'){
                    $toDate = date("d-m-Y", strtotime('+7 days', strtotime($fromDate)));
                    
                    switch ($todest){
                        case 'RAK':
                            $todest2 = 'AGA';
                            break;
                        case 'AGA':
                            $todest2 = 'RAK';
                            break;
                    }
                    
                }
                
                $totalpassanger = $passangers['adults']+$passangers['child']+$passangers['infants'];
                
                $departureFlight = ApinorwegianHelper::APIflightsearchSrc($fromhour, $tohour, 'RejseEksperterneSearchDepart-'.date('d-m-Y'), $currency, $fromdest, $todest, $departureDate, null, false, $totalpassanger, $srcapi);

                if(!$departureFlight){
                    // sets the template to someview.php
                    $session->set('com_travelsearch_again', 1);
                    $session->set('com_travelsearch_type', 1);
                    $this->setRedirect(JRoute::_('index.php?option=com_travelsearch'));
                    return;
                } else {
                    
                    foreach($departureFlight as $code => $data){
                        if($srcapi!='norwegian'){ // airarabia
                            if($code=='OriginDestinationInformation')
                                $arrivetime = $data[ArrivalDateTime];
                        } else if(is_object($data)){ // napi
                            $arrivetime = $data->arrivalTime;
                        }
                        
                        $expl = explode("T", $arrivetime);
                        $startstaydate = $expl[0];
                    }
                    
                    $arrivalFlight = array();
                    if(!empty($toDate)){
                        $arrival = $model->formatDate($toDate);
                        $arrivalDate = $arrival."T00:00:00";
                        
                        if($type=='R'){
                            $todest = $destcode[0]->return_city_code;
                        }
                        
                        $arrivalFlight = ApinorwegianHelper::APIflightsearchSrc($fromhour, $tohour, 'RejseEksperterneSearchReturn-'.date('d-m-Y'), $currency, $todest, $fromdest, $arrivalDate, null, false, $totalpassanger, $srcapi);  
                        
                        if(!$arrivalFlight){
                            // redirect and sugest other dates
                            $session->set('com_travelsearch_again', 1);
                            $session->set('com_travelsearch_type', 1);
                            $this->setRedirect(JRoute::_('index.php?option=com_travelsearch'));
                            return;
                        } else {
                            $arrivalFlight2 = null;
                            if($type=='I'){
                                $arrivalFlight2 = ApinorwegianHelper::APIflightsearchSrc($fromhour, $tohour, 'RejseEksperterneSearchReturn2-'.date('d-m-Y'), $currency, $todest2, $fromdest, $arrivalDate, null, false, $totalpassanger, $srcapi);                                
                            }
                            
                            $seatsavailable = $model->checkSeatsForPassangers($passangers, $departureFlight, $arrivalFlight, $arrivalFlight2);
                            
                            if($seatsavailable['depart']>0||$seatsavailable['arrive']>0||$seatsavailable['arrive2']>0){
                                // redirect and sugest other dates
                                $session->set('com_travelsearch_again', 1);
                                $session->set('com_travelsearch_type', 1);
                                $this->setRedirect(JRoute::_('index.php?option=com_travelsearch'));
                                return;
                            }
                        }
                    }
                }
            } else {
                // sets the template to someview.php
                $viewLayout  = JRequest::getVar( 'tmpl', 'notification' );
                JFactory::getApplication()->enqueueMessage('Ingen valgte destination.', 'error');
            }
        } else {
            // sets the template to someview.php
            $viewLayout  = JRequest::getVar( 'tmpl', 'notification' );
            JFactory::getApplication()->enqueueMessage('Ikke valgte dato.', 'error');
        }
        
        if(empty($viewLayout)){
            $currvalue = null;
            if($currency!='EUR') $currvalue = $model->getCurrencyByID($currency);
            
            if($type=='A'||$type=='I'||$type=='D'){
                $pkgs = $model->getAllPackageIdByDest($cityhotel, $type);
                    
                $packagedata = array();
                foreach($pkgs as $k => $v){
                    $price = $model->calculatePriceTrip($v['id'], $rooms, $startstaydate, $arrival, 'a');
                    
                    // error price check
                    if(sizeof($price['errors']['errors'])==1){
                        if(isset($price['errors']['msg'][1]['adults'])||isset($price['errors']['msg'][1]['child'])||isset($price['errors']['msg'][1]['infants'])){
                            $msg = $price['errors']['msg'][1]['adults']." ".$price['errors']['msg'][1]['child']." ".$price['errors']['msg'][1]['infants'];
                            JFactory::getApplication()->enqueueMessage($msg, 'message');
                        }
                        unset($price['errors']);        
                    }
                    
                    if(is_array($price['roompricing'])){
                        foreach($price['roompricing'] as $key => $value){
                            $rooms[$key]['type'] = $price['roompricing'][$key]['type'];
                        }
                        unset($price['roompricing']);
                        $nurooms = $price['nurooms'];
                        unset($price['nurooms']);
                    }
                    
                    // no price check
                    if($price['basicprice']['onead']!=0&&$price['basicprice']['twoad']!=0){
                        $realprice = $model->priceBaseOnCurrency($price, $currvalue);
                    
                        if($type=='I'){
                            $pkgdetails = $model->getPackageDetailsCombi($v['id'], $startstaydate);                        
                        } else {
                            $pkgdetails = $model->getPackageDetailsByID($v['id'], $startstaydate);                        
                        }
                        
                        if($realprice!=''&&$pkgdetails!=''){
                            $packagedata[$v['id']] = array('price' => $realprice, 'package' => $pkgdetails, 'nurooms' => $nurooms);
                        } 
                    }
                }
                
                // loop check if no price
                $dataexists = false;
                foreach($packagedata as $id => $data){
                    if(is_array($data['price'])){
                        $dataexists = true;
                    }
                }

                if($dataexists){
                    if($type=='I'){
                        // sets the template to someview.php
                        $viewLayout  = JRequest::getVar( 'tmpl', 'combiall' );
                        // tell the view which tmpl to use 
                        $view->setLayout($viewLayout);
                        // go off to the view and call the displaySomeView() method, also pass in $var variable
                        
                        $mixed = array('departureFlight'    => $departureFlight, 
                                    'arrivalFlight'      => $arrivalFlight, 
                                    'arrivalFlight2'    => $arrivalFlight2,
                                    'transport'         => $transport,
                                    'currency'           => $currency, 
                                    'passangers'         => $passangers, 
                                    'packagedata'        => $packagedata,
                                    'model'              => $model,
                                    'rooms'              => $rooms,
                                    'srcapi'            => $srcapi,
                                    'copydest'          => $copydest);

                        $view->displayCombiAll($mixed);
                    } else {
                        // sets the template to someview.php
                        $viewLayout  = JRequest::getVar( 'tmpl', 'accomodationall' );
                        // tell the view which tmpl to use 
                        $view->setLayout($viewLayout);
                        // go off to the view and call the displaySomeView() method, also pass in $var variable

                        $mixed = array('departureFlight'    => $departureFlight, 
                                    'arrivalFlight'      => $arrivalFlight, 
                                    'transport'         => $transport,
                                    'currency'           => $currency, 
                                    'passangers'         => $passangers, 
                                    'packagedata'        => $packagedata,
                                    'model'              => $model,
                                    'rooms'              => $rooms,
                                    'srcapi'            => $srcapi,
                                    'copydest'          => $copydest);

                        $view->displayAccomodationAll($mixed);
                    }
                } else {
                    // sets the template to someview.php
                    $viewLayout  = JRequest::getVar( 'tmpl', 'notification' );
                    JFactory::getApplication()->enqueueMessage('Hotelprisen ikke findes.', 'error');
                    $view->setLayout($viewLayout);
                    $view->displayNotif();
                }
            } else if($type=='R'){
                $price = $model->calculatePriceTrip($id, $rooms, $startstaydate, $arrival, 'a');
                    
                if(sizeof($price['errors']['errors'])<1&&(isset($price['roompricing']))){
                    unset($price['errors']);
                    foreach($price['roompricing'] as $k => $v){
                        $rooms[$k]['type'] = $price['roompricing'][$k]['type'];
                    }
                    unset($price['roompricing']);

                    $currprice = $model->priceBaseOnCurrency($price, $currvalue);

                    $pkgdetails = $model->getPackageDetailsNoHotelByID($id, $startstaydate);

                    // sets the template to someview.php
                    $viewLayout  = JRequest::getVar( 'tmpl', 'roundtrip' );
                    // tell the view which tmpl to use 
                    $view->setLayout($viewLayout);

                    // Model load didnt work in view, so passing the model here
                    $mixed = array('departureFlight'    => $departureFlight, 
                                'arrivalFlight'      => $arrivalFlight, 
                                'currprice'           => $currprice, 
                                'currency'           => $currency, 
                                'passangers'         => $passangers,
                                'pkgdetails'         => $pkgdetails,
                                'model'              => $model,
                                'rooms'              => $rooms,
                                'srcapi'            => $srcapi);

                    // go off to the view and call the displaySomeView() method, also pass in $var variable
                    $view->displayTravelSearch($mixed);
                } else {

                    // sets the template to someview.php
                    $viewLayout  = JRequest::getVar( 'tmpl', 'notification' );
                    JFactory::getApplication()->enqueueMessage('Hotelprisen ikke findes.', 'error');
                    $view->setLayout($viewLayout);    
                    $view->displayNotif();   
                }
            }
              
//            single accomodation, unused
//            $price = $model->calculatePriceTrip($id, $rooms, $startstaydate, $arrival, 'a');
//
//            if(sizeof($price['errors']['errors'])<1&&(isset($price['roompricing']))){
//                unset($price['errors']);
//                foreach($price['roompricing'] as $k => $v){
//                    $rooms[$k]['type'] = $price['roompricing'][$k]['type'];
//                }
//                unset($price['roompricing']);
//
//                $currprice = $model->priceBaseOnCurrency($price, $currvalue);
//
//                $pkgdetails = $model->getPackageDetailsByID($id, $startstaydate);
//
//                // sets the template to someview.php
//                $viewLayout  = JRequest::getVar( 'tmpl', 'accomodation' );
//                // tell the view which tmpl to use 
//                $view->setLayout($viewLayout);
//
//                // Model load didnt work in view, so passing the model here
//                $mixed = array('departureFlight'    => $departureFlight, 
//                            'arrivalFlight'      => $arrivalFlight, 
//                            'currprice'           => $currprice, 
//                            'currency'           => $currency, 
//                            'passangers'         => $passangers, 
//                            'message'            => $message, 
//                            'pkgdetails'         => $pkgdetails,
//                            'model'              => $model,
//                            'rooms'              => $rooms,
//                            'srcapi'            => $srcapi);
//
//                // go off to the view and call the displaySomeView() method, also pass in $var variable
//                $view->displayAccomodation($mixed);
//            } else {
//
//                // sets the template to someview.php
//                $viewLayout  = JRequest::getVar( 'tmpl', 'notification' );
//                if(is_array($price['errors']['msg'])){
//                        $message = $price['errors']['msg'];
//                } else $message = 'Hotelprisen ikke findes';
//
//                $view->setLayout($viewLayout);    
//                $view->displayNotif($message);   
//            }
                    
        } else {
            $view->setLayout($viewLayout);
            $view->displayNotif();
        } 
    }
    
    public function grouptravel(){
        $view = & $this->getView('grouptravel', 'html');
        $viewLayout  = JRequest::getVar( 'tmpl', 'default' );
        $view->setLayout($viewLayout);
        $view->displayGrouptravel($this->getModel('Grouptravel', 'TravelSearchModel'));   
    }

    public function testcon(){
        try {  
//            $cert = file_get_contents(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_apinorwegian'.DS.'helpers'.DS.'napiwsdl.pem');
            
            $soapclient_options = array();
            // $soapclient_options['location'] = "http://webservice.traveloffice.dk/customer.asmx";
            // $soapclient_options['login'] = 'SELECTEDT';
            // $soapclient_options['password'] = '8E8R8G'; //8E8R8G //Select1234#
            $soapclient_options['trace'] = 1;
//            $soapclient_options['local_cert'] = $cert;
//            $soapclient_options['trace'] = TRUE;
            
//            $wsdl = 'https://' . $soapclient_options['login'] . ':' . $soapclient_options['password'] . '@agent.norwegian.no/api/2?wsdl' ;
            $wsdl = 'http://webservice.traveloffice.dk/customer.asmx?wsdl' ;
            
            $soap = new SoapClient($wsdl, $soapclient_options);
            // $ccon['ConnectionString']['login'] = 'SELECTEDT';
            // $ccon['ConnectionString']['password'] = 'Select1234#';

            /**
            *ConnectionString : Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234
*OrderNo: 5179
*PNRNO: MAN566
            */
            $status = $soap->GETCompleteOrder(array('ConnectionString' => 'Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234', 'OrderNo' => '5173', 'PNRNo' => '4D8Y3I'));
print_r($status);
            echo "====== REQUEST HEADERS =====" . PHP_EOL;
    var_dump($soap->__getLastRequestHeaders());
    echo "========= REQUEST ==========" . PHP_EOL;
    var_dump($soap->__getLastRequest());
    echo "========= RESPONSE =========" . PHP_EOL;
var_dump($soap->__getLastResponse());die;
//            $wsdlf = file_get_contents($wsdl);
//            echo $wsdlf;
        } catch(SoapFault $e){
            echo 'ERROR Connection: ';print_r($e->getCode()); echo '<br>';
            print_r($e->getMessage());die;
//            print_r($e->getFile()); echo '<br>';
//            print_r($e->getLine()); echo '<br>';
//            print_r($e->getTrace()); echo '<br>';
//            print_r($e->getTraceAsString());
        }

    }

    public function savesearch(){
        $traveltype = base64_decode(JRequest::getVar('traveltype', ''));
        $fromdest = base64_decode(JRequest::getVar('fromdest', ''));
        $todest = base64_decode(JRequest::getVar('todest', ''));
        $return_city = base64_decode(JRequest::getVar('return_city', ''));
        $package = base64_decode(JRequest::getVar('package', ''));
        $zooitem_id = base64_decode(JRequest::getVar('item_id', ''));
        $passangers = unserialize(base64_decode(html_entity_decode(JRequest::getVar('passangers', ''))));
        $bookingflight = unserialize(base64_decode(html_entity_decode(JRequest::getVar('bookingflight', ''))));
        $currency = base64_decode(JRequest::getVar('currency', ''));
        $rooms = unserialize(base64_decode(html_entity_decode(JRequest::getVar('rooms', ''))));
        $srcapi = unserialize(base64_decode(html_entity_decode(JRequest::getVar('srcapi', ''))));
        
        // if all data complete
        if(($traveltype>=0||$traveltype<3)&&($fromdest!='')&&($todest!='')&&($package!='')&&is_array($passangers)&&is_array($bookingflight)&&is_array($rooms)){
            $mixed = array( 
                        "traveltype"    => $traveltype,
                        "fromdest"      => $fromdest,
                        "todest"        => $todest,
                        "return_city"   => $return_city,
                        "package"       => $package,
                        "passangers"    => $passangers,
                        "bookingflight" => $bookingflight,
                        "zooitem_id"    => $zooitem_id,
                        "currency"      => $currency,
                        "rooms"         => $rooms,
                        "srcapi"        => $srcapi
                    );

            $model = $this->getModel();
            $model->saveQuerySearchTravel($mixed);
                        
            // index.php?option=com_zoo&task=item&item_id=21&category_id=7&Itemid=117
            $this->setRedirect(JRoute::_('index.php?option=com_zoo&task=item&item_id='.$zooitem_id.'&ts=1'));
        } else {
            $app = & JFactory::getApplication();
            $app->redirect(JRoute::_("index.php"), JText::_('COM_TRAVELBOOKING_INCOMPLETE_PARAMETER'));
        }
    }
    
    public function bookingFrontend(){
        $string = JRequest::getVar('string', '');
      	$total = JRequest::getVar('total', '');
        $client = JRequest::getVar('client', '');
                
        $session = JFactory::getSession();
        $pkgs = unserialize($session->get('com_travelsearch.grouptravels', ''));
        $selected = $session->get('com_travelsearch.grouptravels.selected', '');
        
        if(is_object($pkgs[$selected])){
            $model =& $this->getModel('grouptravel');
            $valid = $model->priceValidation($string, $total, $pkgs[$selected]);
            
            if($valid){
                $ordernum = $model->insertBooking($string, $pkgs[$selected], $total, $client);
                if($ordernum){
                    $message['status'] = "true";
                    $message['message'] = $ordernum;
                    die(json_encode($message));
               } else {
                    $message['status'] = "false";
                    $message['message'] = JText::_("COM_TRAVELSEARCH_FAIL_BOOK");
                    die(json_encode($message));
               }
            } else {
                $message['status'] = "false";
                $message['message'] = JText::_("COM_TRAVELSEARCH_ERROR_PARAM_1");
                die(json_encode($message));
            }
        } else {
            $message['status'] = "false";
            $message['message'] = JText::_("COM_TRAVELSEARCH_ERROR_PARAM_2");
            die(json_encode($message));
        }
    }
    
    public function noView(){
        $type = JRequest::getVar('type', 'ajax');
        $model = $this->getModel();
        if($type=='dateajax'){
            $packageid = JRequest::getVar('packageid', 0);
            $from = explode("-", JRequest::getVar('from', ''));
            $to = explode("-", JRequest::getVar('to', ''));
            
            if($to[0]!=''){
                $flightdates = $model->getFlightDates($from[0], $to[0]);
                die(json_encode($flightdates));   
            } else {
                if($packageid!=0&&$from[0]!=''){
                    $destcode = $model->getDestinationCodeByID($packageid);
                    $period = $model->getPeriodPackageByID($packageid);
                    $flightdates = $model->getFlightDatesRoundtrip($from[0], $destcode[0]->destination_city_code, $destcode[0]->return_city_code, $period[0]->period);
                    die(json_encode($flightdates));   
                }
            }
        } else if($type=='dateajaxcombi'){
            $from = explode("-", JRequest::getVar('from', ''));
            $to = explode("-", JRequest::getVar('to', ''));
            
            $flightdates = $model->getFlightDatesCombi($from[0], $to[0]);
            die(json_encode($flightdates));   
        } else if($type=='hotelajax'){
            $todest = JRequest::getVar('todest', '');
            if($todest!=''){
                $hotels['status'] = "false";
                $data = $model->getHotelByDest($todest);
                if(is_array($data)&&($data!='')){
                    $hotels['status'] = "true";
//                    $data = array(array('id' => '3', 'hotel' => $todest, 'city' => 'Jakarta'), array('id' => '4', 'hotel' => $term, 'city' => 'Bandung'), array('id' => '7', 'hotel' => $term." 1", 'city' => 'Malang'));
                    $hotels['data'] = $data;
                }
                die(json_encode($hotels));  
            }
        } else if($type=='getapisrc'){
            $from = JRequest::getVar('from', '');
            $to = explode("-", JRequest::getVar('to', ''));
            
            $apisrc['status'] = "false";
            
            $data = $model->getAPIsrc($from, $to[0]);
            
            if(is_array($data)&&($data!='')){
                $apisrc['status'] = "true";
                $apisrc['data'] = base64_encode($data[0]['api']);
            }
            die(json_encode($apisrc));  
        } else if($type=='getapisrccombi'){
            $from = JRequest::getVar('from', '');
            $to = explode("-", JRequest::getVar('to', ''));
            
            $apisrc['status'] = "false";
            
            $data = $model->getAPIsrcCombi($from, $to[0]);
            
            if(is_array($data)&&($data!='')){
                $apisrc['status'] = "true";
                $apisrc['data'] = base64_encode($data[0]['api']);
            }
            die(json_encode($apisrc));  
        } 
    }
}