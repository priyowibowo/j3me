<?php
/**
 * @version     $Id: view.html.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage  com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the search component
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_search
 * @since 1.0
 */
class TravelSearchViewTravelSearch extends JView
{
    /**
     $mixed = array('departureFlight'    => $departureFlight, 
                                   'arrivalFlight'      => $arrivalFlight, 
                                   'currprice'           => $currprice, 
                                   'currency'           => $currency, 
                                   'passangers'         => $passangers, 
                                   'pkgdetails'         => $pkgdetails, 
                                   'dest'               => $dest, 
                                   'arrival'            => $arrival);
     */
    function displayAccomodation($mixed){
        extract($mixed);
        
        if($srcapi!='norwegian'){
            // for apiarabia 
            $departuredata['flightCode'] = '';
            $departuredata['departureTime'] = $departureFlight[OriginDestinationInformation][DepartureDateTime];
            $seatDepart = 20;
            $destination = array(array(
                                        'displayName' => $departureFlight[OriginDestinationInformation][OriginLocation]['!'],
                                        'iataAirportCode' => $departureFlight[OriginDestinationInformation][OriginLocation]['!LocationCode'],
                                    )
                                );

            $departuredata['booking']['class'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $departuredata['booking']['ADT'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['CHD'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['INF'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['currency'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
        } else {
            // Model load didnt work in view, so passing the model here via controller
            
            // for napi
            foreach($departureFlight as $key => $value){ 
                $dest = $value->origin;
                $departuredata['flightCode'] = $value->flightCode;
                $departuredata['departureTime'] = $value->departureTime;
                foreach($value->pricelistny as $k => $v){
                    $departuredata['booking']['class'] = $v->bookingClass;
                    $departuredata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatDepart = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatDepart = $class->seatsAvailable;
                    }
                }
            }
            
            $destination = $model->getCityByCode($dest);
        }
        
        if($srcapi!='norwegian'){
            // for apiarabia 
            $arrivaldata['flightCode'] = '';
            $arrivaldata['departureTime'] = $arrivalFlight[OriginDestinationInformation][DepartureDateTime];
            $seatReturn = 20;
            $arrival = array(array(
                                    'displayName' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!'],
                                    'iataAirportCode' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!LocationCode'],
                                )
                            );
            
            $arrivaldata['booking']['class'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $arrivaldata['booking']['ADT'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['CHD'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['INF'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['currency'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
            
        } else {
            
            foreach($arrivalFlight as $key => $value){ 
                $arrive = $value->origin;
                $arrivaldata['flightCode'] = $value->flightCode;
                $arrivaldata['departureTime'] = $value->departureTime;
                foreach($value->pricelistny as $k => $v){
                    $arrivaldata['booking']['class'] = $v->bookingClass;
                    $arrivaldata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatReturn = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatReturn = $class->seatsAvailable;
                    }
                }
            }
            
            $arrival = $model->getCityByCode($arrive);
        }
        
        // only show seats available if < 6, and show the smallest number
        $seatavailable = 6;
        if($seatReturn<=5||$seatDepart<=5){
            if($seatDepart<=$seatReturn){
                $seatavailable = $seatDepart;
            } else if($seatDepart>=$seatReturn){
                $seatavailable = $seatReturn;
            }
        }        
        
        $zoo_data = isset($pkgdetails[0]->zooitem_id) ? $model->getZooElementByItemId($pkgdetails[0]->zooitem_id) : '';
        
        // calculate price flight total per passangers
        $currprice['perpax'] = $model->calculatePriceFlightsPerson($arrivaldata, $departuredata);
        $currprice['allpax'] = $model->calculatePriceFlightsPassangers($passangers, $currprice['perpax']);
        
        // margins flight
        $flightmargin = ($currprice['perpax']['ADT']*$currprice['basicprice']['margin'])/100;
        $flightprice = $currprice['perpax']['ADT'] + $flightmargin;
        
        $flighmarginch = ($currprice['perpax']['CHD']*$currprice['basicprice']['margin'])/100;
        $flightpricech = $currprice['perpax']['CHD'] + $flighmarginch;
        
        $flightmargininf = ($currprice['perpax']['INF']*$currprice['basicprice']['margin'])/100;
        $flightpriceinf = $currprice['perpax']['INF'] + $flightmargininf;
        
        // display per room total price, round down to 5 per person
        foreach($rooms as $k => $room){
            if($room['adults']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['onead']+$flightprice)/ 5) * 5;
                $rooms[$k]['single'] = floor(($currprice['basicprice']['onead']+$flightprice)/ 5) * 5;
            } else if($room['adults']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                $rooms[$k]['double'] = floor(($currprice['basicprice']['twoad']+$flightprice)/ 5) * 5;
            } else if($room['adults']==3){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['threead']+$flightprice)/ 5) * 5) *3;
                $rooms[$k]['triple'] = floor(($currprice['basicprice']['threead']+$flightprice)/ 5) * 5;
            }
            
            if($room['child']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['oneb']+$flightpricech)/ 5) * 5;
            } else if($room['child']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['twob']+$flightpricech)/ 5) * 5) * 2;
            } 
            
            if($room['infant']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['inf']+$flightpriceinf)/ 5) * 5;
            } else if($room['infant']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['inf']+$flightpriceinf)/ 5) * 5) * 2;
            }
        }
        
        $days = $model->getDaysInterval($departuredata['departureTime'], $arrivaldata['departureTime']);
                
        // Assign data to the view
        $this->departure = array('flight' => $departureFlight, 'city' => $destination[0]);
        $this->arrival = array('flight' => $arrivalFlight, 'city' => $arrival[0]);
        $this->passangers = $passangers;
        $this->pkgprice = $currprice;
        $this->currency = $currency;
        $this->pkgdetails = $pkgdetails;
        $this->zoo_data = $zoo_data;
        $this->booking = array('outbound' => $departuredata, 'inbound' => $arrivaldata);
        $this->rooms = $rooms;
        $this->seatavailable = $seatavailable;
        $this->days = $days;
        $this->srcapi = $srcapi;
        
        // Display the view
        parent::display($tpl);
        
        $this->setDocument();
    }
    
    function displayCombiAll($mixed){
        extract($mixed);
        
        foreach($departureFlight as $key => $value){
            $departuredata['flightCode'] = $value->flightCode;
            $departuredata['departureTime'] = $value->departureTime;
            $departuredata['booking']['currency'] = $currency;
            $departuredata['origin'] = $value->origin;
            $departuredata['destination'] = $value->destination;

            foreach($value->pricelistny as $k => $v){
                $departuredata['booking']['class'] = $v->bookingClass;
                $departuredata['booking'][$v->paxType] = $v->totalFare->amount;
            }

            if(is_object($value->bookingClasses)){
                $seatDepart = $value->bookingClasses->seatsAvailable;
            } else {
                foreach($value->bookingClasses as $num => $class){
                    $seatDepart = $class->seatsAvailable;
                }
            }
        }

        foreach($arrivalFlight as $key => $value){ 
            $arrivaldata['flightCode'] = $value->flightCode;
            $arrivaldata['departureTime'] = $value->departureTime;
            $arrivaldata['booking']['currency'] = $currency;
            $arrivaldata['origin'] = $value->origin;
            $arrivaldata['destination'] = $value->destination;
            
            foreach($value->pricelistny as $k => $v){
                $arrivaldata['booking']['class'] = $v->bookingClass;
                $arrivaldata['booking'][$v->paxType] = $v->totalFare->amount;
            }

            if(is_object($value->bookingClasses)){
                $seatReturn = $value->bookingClasses->seatsAvailable;
            } else {
                foreach($value->bookingClasses as $num => $class){
                    $seatReturn = $class->seatsAvailable;
                }
            }
        }
        
        foreach($arrivalFlight2 as $key => $value){ 
            $arrivaldata2['flightCode'] = $value->flightCode;
            $arrivaldata2['departureTime'] = $value->departureTime;
            $arrivaldata2['booking']['currency'] = $currency;
            $arrivaldata2['origin'] = $value->origin;
            $arrivaldata2['destination'] = $value->destination;
            
            foreach($value->pricelistny as $k => $v){
                $arrivaldata2['booking']['class'] = $v->bookingClass;
                $arrivaldata2['booking'][$v->paxType] = $v->totalFare->amount;
            }

            if(is_object($value->bookingClasses)){
                $seatReturn2 = $value->bookingClasses->seatsAvailable;
            } else {
                foreach($value->bookingClasses as $num => $class){
                    $seatReturn2 = $class->seatsAvailable;
                }
            }
        }
        
        foreach($packagedata as $pkg => $data){
            if($data['package'][0]->zooitem_id!=''&&$data['package'][0]->zooitem_id!=0){
                $packagedata[$pkg]['zoo_data'] = $model->getZooElementByItemId($data['package'][0]->zooitem_id);    
            }
           
            // change flight price to be the same with request currency
            if(strtoupper($departuredata['booking']['currency'])!=strtoupper($currency)){
                if($data['package'][0]->return_city_code==$arrivaldata['origin'])
                    $arrivaldata['booking'] = $model->syncFlightPriceAndCurrency($arrivaldata, $currency);
                else if ($data['package'][0]->return_city_code==$arrivaldata2['origin']){
                    $arrivaldata2['booking'] = $model->syncFlightPriceAndCurrency($arrivaldata2, $currency);
                }
                
                $departuredata['booking'] = $model->syncFlightPriceAndCurrency($departuredata, $currency);
            }
            
            // calculate price flight total per passangers
            if($data['package'][0]->return_city_code==$arrivaldata['origin']){
                $packagedata[$pkg]['perpax'] = $model->calculatePriceFlightsPerson($arrivaldata, $departuredata);
            } else if($data['package'][0]->return_city_code==$arrivaldata2['origin']){
                $packagedata[$pkg]['perpax'] = $model->calculatePriceFlightsPerson($arrivaldata2, $departuredata);
            }
            
                                  
            // use transport add 100 eur
            if($transport){
                if(strtoupper($currency)!='EUR'){
                    $cur = $model->getCurrencyByID($currency);
                    $addontransport = 100*$cur[0]->currency_value;
                    $packagedata[$pkg]['perpax']['ADT'] += $addontransport;
                    $packagedata[$pkg]['perpax']['CHD'] += $addontransport;
                    $packagedata[$pkg]['perpax']['INF'] += $addontransport;
                } else {
                    $packagedata[$pkg]['perpax']['ADT'] += 100;
                    $packagedata[$pkg]['perpax']['CHD'] += 100;
                    $packagedata[$pkg]['perpax']['INF'] += 100;
                }
            }
            
            // total all price
            $packagedata[$pkg]['allpax'] = $model->calculatePriceFlightsPassangers($passangers, $packagedata[$pkg]['perpax']);
                       
            // margins flight
            $flightmargin = ($packagedata[$pkg]['perpax']['ADT']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            
            $flightprice = $packagedata[$pkg]['perpax']['ADT'] + $flightmargin;

            $flighmarginch = ($packagedata[$pkg]['perpax']['CHD']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            $flightpricech = $packagedata[$pkg]['perpax']['CHD'] + $flighmarginch;

            $flightmargininf = ($packagedata[$pkg]['perpax']['INF']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            $flightpriceinf = $packagedata[$pkg]['perpax']['INF'] + $flightmargininf;

            if(is_array($packagedata[$pkg]['nurooms'])){
                $nurooms = $packagedata[$pkg]['nurooms'];
            } else {
                $nurooms = $rooms;
            }
            
            // display per room total price, round down to 5 per person
            foreach($nurooms as $k => $room){
                $nurooms[$k]['total'] = 0;
                if($room['adults']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5;
                    $nurooms[$k]['single'] = floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5;
                } else if($room['adults']==2){
                    $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                    $nurooms[$k]['double'] = floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5;
                } else if($room['adults']==3){
                    if($packagedata[$pkg]['price']['basicprice']['threead']=='n/a'){
                        // suggest new single room and double room
                        $double = (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                        $triple = (floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5);
                        $nurooms[$k]['total'] += $double;
                        $nurooms[$k]['total'] += $triple;
                        $nurooms[$k]['triple'] = ($double + $triple)/3;
                    } else {
                        $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['threead']+$flightprice)/ 5) * 5) *3;
                        $nurooms[$k]['triple'] = floor(($packagedata[$pkg]['price']['basicprice']['threead']+$flightprice)/ 5) * 5;
                    }
                }

                if($room['child']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['oneb']+$flightpricech)/ 5) * 5;
                } else if($room['child']==2){
                    if(strtolower($packagedata[$pkg]['price']['basicprice']['twob'])=='n/a'){
                        // if child price for 2 is not available, then suggest price for adt (price taken will be varied depending on actual adt)
                        if($room['adults']==1){
                            // if only one adt, then suggest price for two adt for one of the child and one chd price for one child
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightpricech)/ 5) * 5);
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['oneb']+$flightpricech)/ 5) * 5);
                        } else if($room['adults']==2){
                            // suggest new double room
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightpricech)/ 5) * 5) * 2;
                        }
                    }
                    else
                        $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twob']+$flightpricech)/ 5) * 5) * 2;
                } 

                if($room['infants']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['inf']+$flightpriceinf)/ 5) * 5;
                } else if($room['infants']==2){
                    
                    $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['inf']+$flightpriceinf)/ 5) * 5) * 2;
                }
            }

            $packagedata[$pkg]['rooms'] = $nurooms;

        }
        
        // only show seats available if < 6, and show the smallest number
        $seatavailable = 6;
        if($seatReturn<=5||$seatDepart<=5){
            if($seatDepart<=$seatReturn){
                $seatavailable = $seatDepart;
            } else if($seatDepart>=$seatReturn){
                $seatavailable = $seatReturn;
            }
        }
        
        $days = $model->getDaysInterval($departuredata['departureTime'], $arrivaldata['departureTime']);
                
        // Assign data to the view
//        $this->departure = array('flight' => $departureFlight, 'city' => $destination[0]);
//        $this->arrival = array('flight' => $arrivalFlight, 'city' => $arrival[0]);
        $this->passangers = $passangers;
        $this->currency = $currency;
        $this->packagedata = $packagedata;
        $this->booking = array('outbound' => $departuredata, 'inbound' => $arrivaldata);
        $this->rooms = $rooms;
        $this->seatavailable = $seatavailable;
        $this->days = $days;
        $this->srcapi = $srcapi;
        $this->copydest = $copydest;
        $this->defaultlabels = $model->getDefaultLabel();
        
        // Display the view
        parent::display($tpl);
        
        $this->setDocument();
    }
    
    /*$mixed = array('departureFlight'    => $departureFlight, 
                                   'arrivalFlight'      => $arrivalFlight, 
                                   'currency'           => $currency, 
                                   'passangers'         => $passangers, 
                                   'packagedata'        => $packagedata,
                                   'model'              => $model);*/
    function displayAccomodationAll($mixed){
        extract($mixed);
        
        if($srcapi!='norwegian'){
            // for apiarabia 
            $departuredata['flightCode'] = '';
            $departuredata['departureTime'] = $departureFlight[OriginDestinationInformation][DepartureDateTime];
            $seatDepart = 20;
            $destination = array(array(
                                        'displayName' => $departureFlight[OriginDestinationInformation][OriginLocation]['!'],
                                        'iataAirportCode' => $departureFlight[OriginDestinationInformation][OriginLocation]['!LocationCode']
                                    )
                                );

            $departuredata['booking']['class'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $departuredata['booking']['ADT'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['CHD'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['INF'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['currency'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
            
        } else {
            // Model load didnt work in view, so passing the model here via controller
            
            // for napi
            foreach($departureFlight as $key => $value){ 
                $dest = $value->origin;
                $departuredata['flightCode'] = $value->flightCode;
                $departuredata['departureTime'] = $value->departureTime;
                $departuredata['booking']['currency'] = $currency;
                foreach($value->pricelistny as $k => $v){
                    $departuredata['booking']['class'] = $v->bookingClass;
                    $departuredata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatDepart = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatDepart = $class->seatsAvailable;
                    }
                }
            }
            
            $destination = $model->getCityByCode($dest);
        }
        
        
        
        if($srcapi!='norwegian'){
            // for apiarabia 
            $arrivaldata['flightCode'] = '';
            $arrivaldata['departureTime'] = $arrivalFlight[OriginDestinationInformation][DepartureDateTime];
            $seatReturn = 20;
            $arrival = array(array(
                                    'displayName' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!'],
                                    'iataAirportCode' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!LocationCode']
                                )
                        );
            
            $arrivaldata['booking']['class'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $arrivaldata['booking']['ADT'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['CHD'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['INF'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['currency'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
            
        } else {
                        
            foreach($arrivalFlight as $key => $value){ 
                $arrive = $value->origin;
                $arrivaldata['flightCode'] = $value->flightCode;
                $arrivaldata['departureTime'] = $value->departureTime;
                $arrivaldata['booking']['currency'] = $currency;
                foreach($value->pricelistny as $k => $v){
                    $arrivaldata['booking']['class'] = $v->bookingClass;
                    $arrivaldata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatReturn = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatReturn = $class->seatsAvailable;
                    }
                }
            }
            
            $arrival = $model->getCityByCode($arrive);
        }
        
        foreach($packagedata as $pkg => $data){
            if($data['package'][0]->zooitem_id!=''){
                $packagedata[$pkg]['zoo_data'] = $model->getZooElementByItemId($data['package'][0]->zooitem_id);    
            }
           
            // change flight price to be the same with request currency
            if(strtoupper($departuredata['booking']['currency'])!=strtoupper($currency)){
                $arrivaldata['booking'] = $model->syncFlightPriceAndCurrency($arrivaldata, $currency);
                $departuredata['booking'] = $model->syncFlightPriceAndCurrency($departuredata, $currency);
            }

            // calculate price flight total per passangers
            $packagedata[$pkg]['perpax'] = $model->calculatePriceFlightsPerson($arrivaldata, $departuredata);
                                  
            // use transport add 100 eur
            if($transport){
                if(strtoupper($currency)!='EUR'){
                    $cur = $model->getCurrencyByID($currency);
                    $addontransport = 100*$cur[0]->currency_value;
                    $packagedata[$pkg]['perpax']['ADT'] += $addontransport;
                    $packagedata[$pkg]['perpax']['CHD'] += $addontransport;
                    $packagedata[$pkg]['perpax']['INF'] += $addontransport;
                } else {
                    $packagedata[$pkg]['perpax']['ADT'] += 100;
                    $packagedata[$pkg]['perpax']['CHD'] += 100;
                    $packagedata[$pkg]['perpax']['INF'] += 100;
                }
            }
            
            // total all price
            $packagedata[$pkg]['allpax'] = $model->calculatePriceFlightsPassangers($passangers, $packagedata[$pkg]['perpax']);
                       
            // margins flight
            $flightmargin = ($packagedata[$pkg]['perpax']['ADT']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            
            $flightprice = $packagedata[$pkg]['perpax']['ADT'] + $flightmargin;

            $flighmarginch = ($packagedata[$pkg]['perpax']['CHD']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            $flightpricech = $packagedata[$pkg]['perpax']['CHD'] + $flighmarginch;

            $flightmargininf = ($packagedata[$pkg]['perpax']['INF']*$packagedata[$pkg]['price']['basicprice']['margin'])/100;
            $flightpriceinf = $packagedata[$pkg]['perpax']['INF'] + $flightmargininf;

            if(is_array($packagedata[$pkg]['nurooms'])){
                $nurooms = $packagedata[$pkg]['nurooms'];
            } else {
                $nurooms = $rooms;
            }
            
            // display per room total price, round down to 5 per person
            foreach($nurooms as $k => $room){
                $nurooms[$k]['total'] = 0;
                if($room['adults']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5;
                    $nurooms[$k]['single'] = floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5;
                } else if($room['adults']==2){
                    $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                    $nurooms[$k]['double'] = floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5;
                } else if($room['adults']==3){
                    if($packagedata[$pkg]['price']['basicprice']['threead']=='n/a'){
                        // suggest new single room and double room
                        $double = (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                        $triple = (floor(($packagedata[$pkg]['price']['basicprice']['onead']+$flightprice)/ 5) * 5);
                        $nurooms[$k]['total'] += $double;
                        $nurooms[$k]['total'] += $triple;
                        $nurooms[$k]['triple'] = ($double + $triple)/3;
                    } else {
                        $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['threead']+$flightprice)/ 5) * 5) *3;
                        $nurooms[$k]['triple'] = floor(($packagedata[$pkg]['price']['basicprice']['threead']+$flightprice)/ 5) * 5;
                    }
                }

                if($room['child']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['oneb']+$flightpricech)/ 5) * 5;
                } else if($room['child']==2){
                    if(strtolower($packagedata[$pkg]['price']['basicprice']['twob'])=='n/a'){
                        // if child price for 2 is not available, then suggest price for adt (price taken will be varied depending on actual adt)
                        if($room['adults']==1){
                            // if only one adt, then suggest price for two adt for one of the child and one chd price for one child
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightpricech)/ 5) * 5);
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['oneb']+$flightpricech)/ 5) * 5);
                        } else if($room['adults']==2){
                            // suggest new double room
                            $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twoad']+$flightpricech)/ 5) * 5) * 2;
                        }
                    }
                    else
                        $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['twob']+$flightpricech)/ 5) * 5) * 2;
                } 

                if($room['infants']==1){
                    $nurooms[$k]['total'] += floor(($packagedata[$pkg]['price']['basicprice']['inf']+$flightpriceinf)/ 5) * 5;
                } else if($room['infants']==2){
                    
                    $nurooms[$k]['total'] += (floor(($packagedata[$pkg]['price']['basicprice']['inf']+$flightpriceinf)/ 5) * 5) * 2;
                }
            }

            $packagedata[$pkg]['rooms'] = $nurooms;

        }
        
        // only show seats available if < 6, and show the smallest number
        $seatavailable = 6;
        if($seatReturn<=5||$seatDepart<=5){
            if($seatDepart<=$seatReturn){
                $seatavailable = $seatDepart;
            } else if($seatDepart>=$seatReturn){
                $seatavailable = $seatReturn;
            }
        }
        
        $days = $model->getDaysInterval($departuredata['departureTime'], $arrivaldata['departureTime']);
                
        // Assign data to the view
        $this->departure = array('flight' => $departureFlight, 'city' => $destination[0]);
        $this->arrival = array('flight' => $arrivalFlight, 'city' => $arrival[0]);
        $this->passangers = $passangers;
        $this->currency = $currency;
        $this->packagedata = $packagedata;
        $this->booking = array('outbound' => $departuredata, 'inbound' => $arrivaldata);
        $this->rooms = $rooms;
        $this->seatavailable = $seatavailable;
        $this->days = $days;
        $this->srcapi = $srcapi;
        $this->copydest = $copydest;
        $this->defaultlabels = $model->getDefaultLabel();
        
        // Display the view
        parent::display($tpl);
        
        $this->setDocument();
    }
    
    protected function setDocument(){
        $document = JFactory::getDocument();
        
        $script = 'if(typeof jQuery != "undefined"){'.$document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery-1.7.1.min.js').'}';
        $document->addScriptDeclaration($script);
        $document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery-ui-1.8.18.custom.min.js');
        $document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery.ui.datepicker-da.js');
        $document->addScript("components/com_travelsearch/views/travelsearch/js/com_travelsearch.main.js");
    }

    /*$mixed = array('departureFlight'    => $departureFlight, 
                                   'arrivalFlight'      => $arrivalFlight, 
                                   'currency'           => $currency, 
                                   'passangers'         => $passangers, 
                                   'packagedata'        => $packagedata,
                                   'model'              => $model);*/
    function displayTravelSearch($mixed){
        extract($mixed);
        
        if($srcapi!='norwegian'){
            // for apiarabia 
            $departuredata['flightCode'] = '';
            $departuredata['departureTime'] = $departureFlight[OriginDestinationInformation][DepartureDateTime];
            $seatDepart = 20;
            $outorigin = array(array(
                                        'displayName' => $departureFlight[OriginDestinationInformation][OriginLocation]['!'], 
                                        'iataAirportCode' => $departureFlight[OriginDestinationInformation][OriginLocation]['!LocationCode']
                                    )
                            );
            $outdestination = array(array(
                                            'displayName' => $departureFlight[OriginDestinationInformation][DestinationLocation]['!'],
                                            'iataAirportCode' => $departureFlight[OriginDestinationInformation][DestinationLocation]['!LocationCode']
                                        )
                                    );

            $departuredata['booking']['class'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $departuredata['booking']['ADT'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['CHD'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['INF'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $departuredata['booking']['currency'] = $departureFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
        } else {
            // Model load didnt work in view, so passing the model here via controller
            
            // for napi
            foreach($departureFlight as $key => $value){ 
                $out_origin = $value->origin;
                $out_destination = $value->destination;
                $departuredata['flightCode'] = $value->flightCode;
                $departuredata['departureTime'] = $value->departureTime;
                foreach($value->pricelistny as $k => $v){
                    $departuredata['booking']['class'] = $v->bookingClass;
                    $departuredata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatDepart = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatDepart = $class->seatsAvailable;
                    }
                }
            }
            
            $outorigin = $model->getCityByCode($out_origin);
            $outdestination = $model->getCityByCode($out_destination);
        }        

        if($srcapi!='norwegian'){
            // for apiarabia 
            $arrivaldata['flightCode'] = '';
            $arrivaldata['departureTime'] = $arrivalFlight[OriginDestinationInformation][DepartureDateTime];
            $seatReturn = 20;
            $inorigin = array(array('displayName' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!'],
                'iataAirportCode' => $arrivalFlight[OriginDestinationInformation][OriginLocation]['!LocationCode']));
            $indestination = array(array('displayName' => $arrivalFlight[OriginDestinationInformation][DestinationLocation]['!'],
                'iataAirportCode' => $arrivalFlight[OriginDestinationInformation][DestinationLocation]['!LocationCode']));
            
            $arrivaldata['booking']['class'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][FareBasisCodes][FareBasisCode];
            $arrivaldata['booking']['ADT'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['CHD'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][1][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['INF'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][2][PassengerFare][TotalFare]['!Amount'];
            $arrivaldata['booking']['currency'] = $arrivalFlight[AAAirAvailRSExt][PricedItineraries][PricedItinerary][AirItineraryPricingInfo][PTC_FareBreakdowns][PTC_FareBreakdown][0][PassengerFare][TotalFare]['!CurrencyCode'];
            
        } else {
            
            foreach($arrivalFlight as $key => $value){ 
                $in_origin = $value->origin;
                $in_destination = $value->destination;
                $arrivaldata['flightCode'] = $value->flightCode;
                $arrivaldata['departureTime'] = $value->departureTime;
                foreach($value->pricelistny as $k => $v){
                    $arrivaldata['booking']['class'] = $v->bookingClass;
                    $arrivaldata['booking'][$v->paxType] = $v->totalFare->amount;
                }
                
                if(is_object($value->bookingClasses)){
                    $seatReturn = $value->bookingClasses->seatsAvailable;
                } else {
                    foreach($value->bookingClasses as $num => $class){
                        $seatReturn = $class->seatsAvailable;
                    }
                }
            }
            
            $inorigin = $model->getCityByCode($in_origin);
            $indestination = $model->getCityByCode($in_destination);
        }
        
        // only show seats available if < 6, and show the smallest number
        $seatavailable = 6;
        if($seatReturn<=5||$seatDepart<=5){
            if($seatDepart<=$seatReturn){
                $seatavailable = $seatDepart;
            } else if($seatDepart>=$seatReturn){
                $seatavailable = $seatReturn;
            }
        }
        
        $zoo_data = isset($pkgdetails[0]->zooitem_id) ? $model->getZooElementByItemIdRt($pkgdetails[0]->zooitem_id) : '';
        
        // calculate price flight total per passangers
        $currprice['perpax'] = $model->calculatePriceFlightsPerson($arrivaldata, $departuredata);
        $currprice['allpax'] = $model->calculatePriceFlightsPassangers($passangers, $currprice['perpax']);
        
        // margins flight
        $flightmargin = ($currprice['perpax']['ADT']*$currprice['basicprice']['margin'])/100;
        
        $flightprice = $currprice['perpax']['ADT'] + $flightmargin;
        
        $flighmarginch = ($currprice['perpax']['CHD']*$currprice['basicprice']['margin'])/100;
        $flightpricech = $currprice['perpax']['CHD'] + $flighmarginch;
        
        $flightmargininf = ($currprice['perpax']['INF']*$currprice['basicprice']['margin'])/100;
        $flightpriceinf = $currprice['perpax']['INF'] + $flightmargininf;
        
        // display per room total price, round down to 5 per person
        foreach($rooms as $k => $room){
            if($room['adults']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['onead']+$flightprice)/ 5) * 5;
                $rooms[$k]['single'] = floor(($currprice['basicprice']['onead']+$flightprice)/ 5) * 5;
            } else if($room['adults']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['twoad']+$flightprice)/ 5) * 5) * 2;
                $rooms[$k]['double'] = floor(($currprice['basicprice']['twoad']+$flightprice)/ 5) * 5;
            } else if($room['adults']==3){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['threead']+$flightprice)/ 5) * 5) *3;
                $rooms[$k]['triple'] = floor(($currprice['basicprice']['threead']+$flightprice)/ 5) * 5;
            }
            
            if($room['child']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['oneb']+$flightpricech)/ 5) * 5;
            } else if($room['child']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['twob']+$flightpricech)/ 5) * 5) * 2;
            } 
            
            if($room['infant']==1){
                $rooms[$k]['total'] += floor(($currprice['basicprice']['inf']+$flightpriceinf)/ 5) * 5;
            } else if($room['infant']==2){
                $rooms[$k]['total'] += (floor(($currprice['basicprice']['inf']+$flightpriceinf)/ 5) * 5) * 2;
            }
        }
        
        $days = $model->getDaysInterval($departuredata['departureTime'], $arrivaldata['departureTime']);
                
        // Assign data to the view
        $this->departure = array('flight' => $departureFlight, 'city' => $outorigin[0], 'city_dest' => $outdestination[0]);
        $this->arrival = array('flight' => $arrivalFlight, 'city' => $inorigin[0], 'city_dest' => $indestination[0]);
        $this->passangers = $passangers;
        $this->pkgprice = $currprice;
        $this->currency = $currency;
        $this->pkgdetails = $pkgdetails;
        $this->zoo_data = $zoo_data;
        $this->booking = array('outbound' => $departuredata, 'inbound' => $arrivaldata);
        $this->rooms = $rooms;
        $this->seatavailable = $seatavailable;
        $this->days = $days;
        $this->srcapi = $srcapi;
        
        // Display the view
        parent::display($tpl);
        $this->setDocument();
    }
    
    function displayNotif(){
        // Assign data to the view
        
        // Display the view
        parent::display($tpl);
    }
}