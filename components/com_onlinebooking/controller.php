<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
class OnlinebookingController extends JControllerLegacy
{
	public function testOrderPnr($cachable = false, $urlparams = false) {
		$soapclient_options = array();
		$soapclient_options['trace'] = 1;
		$wsdl = 'http://webservice.traveloffice.dk/online.asmx?wsdl' ;

		$client = new SoapClient($wsdl, $soapclient_options);

		try {
		  
		  $out = date('c', strtotime('25-05-2014'));
		  $in = date('c', strtotime('01-06-2014'));
		  
		  $fare[] = array(
		        'CUR' => 'DKK',
		        'TotalFare' => '6412',
		        'TotalFareSelling' => '6920',
		        'ValidatingCarrier' => 'DY',
		        'LastTicketingDate' => '2014-05-08T00:00:00',
		        'FarebasisGeneral' => 'TJIPPI+WJIPPI',
		        'TextInfo' => array('_' => null, 'xsi:nil' => 'TRUE'),
		        'LastTicketDate' => '2014-05-08T00:00:00',
		        'BrandID' => '',
		        'Fares' => array('_' => null, 'xsi:nil' => 'TRUE'),
		        'Travels' => array('_' => null, 'xsi:nil' => 'TRUE'),
		      );

		  $fare[] = array(
		        'CUR' => 'DKK',
		        'TotalFare' => '6412',
		        'TotalFareSelling' => '6920',
		        'ValidatingCarrier' => 'DY',
		        'LastTicketingDate' => '2014-05-08T00:00:00',
		        'FarebasisGeneral' => 'TJIPPI+WJIPPI',
		        'TextInfo' => array('_' => null, 'xsi:nil' => 'TRUE'),
		        'LastTicketDate' => '2014-05-08T00:00:00',
		        'BrandID' => '',
		        'Fares' => array('_' => null, 'xsi:nil' => 'TRUE'),
		        'Travels' => array('_' => null, 'xsi:nil' => 'TRUE'),
		      );

		  $params = array(
		    // 'request' => array(
		        'BookingVar' => array(
		          'CRS' => 'Amadeus',
		          'Status' => TRUE,
		          'DisconnectAfterFares' => FALSE,
		          'LoginInfo' => array(
		            'Status' => TRUE,
		            'Options' => 'R,UP/ady',
		            'MyOfficeID' => 'CPHS12318',
		            'IssueOfficeID' => 'ALL:CPHS12176',
		            'ConnectionString' => 'Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234',
		            'DisconnectSabre' => TRUE,
		            'DoConnect'   => TRUE,
		            'TimeOut' => 10,
		          ),
		          'DepCity' => 'CPH',
		          'DestCity' => 'RAK',
		          'Depdate' => $out,
		          'ReturnDate' => $in,
		          'NoAdt' => 2,
		          'NoChd' => 0,
		          'NoInf' => 0,
		          'Lang' => 'dk',
		          'ErrorNo' => '',
		          'BookingNo' => '',
		          'NoStop' => 'DirectOnly',
		          'Customer' => array(
		            'CustomerNo' => '',
		            'Name' => 'Priyo Wibowo',
		            'Address' => 'Taman Malaka Barat Blok E3/9',
		            'Address2' => '',
		            'ZIP' => '13460',
		            'City' => 'Jakarta',
		            'Country' => 'Indonesia',
		            'Phone' => '+6281315587695',
		            'email' => 'priyo.wbw@gmail.com',
		            'Creditmax' => '',
		            'Balance' => '',
		            'PayDays' => '',
		          ),
		          'Names' => array(
		            'Pax1' => 'WIBOWO/PRIYO MR#07/12/1983#ADT',
		            'Pax2' => 'WACHID/RIO MR#07/10/1986#ADT',
		          ),
		          'BookingNo' => '',
		          'OneFare' => array(
		            'ReadFaresClass' => $fare,
		          ),
		          'InvoiceProject' => array(
		            'InvoiceProductAIR' => 'FLYUGF',
		            'InvoiceProductTAX' => 'FLYUGF',
		            'InvoiceProductOTH' => 'RGF',
		            'minProfit' => '0',
		            'minPct' => '10',
		            'CalcProfitOnTax' => '',
		            'Rounding' => '10',
		          ),
		          'Agency' => array(
		            'CompanyName' => 'Selected Tours ApS',
		            'Address' => 'Vermundsgade 38',
		            'City' => 'Copenhagen',
		            'StateCode' => '',
		            'PostalCode' => '2100',
		            'CountryCode' => 'DK',
		          )
		        ),
		      // )
		  );

		// <Agency>
		//           <CompanyName>string</CompanyName>
		//           <Address>string</Address>
		//           <City>string</City>
		//           <StateCode>string</StateCode>
		//           <PostalCode>string</PostalCode>
		//           <CountryCode>string</CountryCode>
		//         </Agency>



		  $data = $client->BookNewPNRInCRS($params);  
		  // $data = $client->call('BookNewPNRInCRS', array('parameters' => $params));
		  // $data = $client->__soapCall('BookNewPNRInCRS', array('parameters' => $params));
		  echo "<br>========= REQUEST ==========" . PHP_EOL;
		    var_dump($client->__getLastRequest());
		    echo "<br>========= RESPONSE =========" . PHP_EOL;
		    var_dump($client->__getLastResponse());
		  // print_r($data);die;
		} catch (SoapFault $exception) {
		   echo "<br>====== REQUEST HEADERS =====" . PHP_EOL;
		    var_dump($client->__getLastRequestHeaders());
		    echo "<br>========= REQUEST ==========" . PHP_EOL;
		    var_dump($client->__getLastRequest());
		    echo "<br>========= RESPONSE =========" . PHP_EOL;
		    var_dump($client->__getLastResponse());
		}
	}

	public function testGetFare($cachable = false, $urlparams = false){
		$document	= JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->getCmd('view', 'fares');
		$vFormat = $document->getType();

		$lName = $this->input->getCmd('layout', 'fares');	
		$model = $this->getModel();
		$data = array(
			'DepCity' => 'CPH',
			'DestCity' => 'RAK',
			'Depdate' => '',
			'ReturnDate' => '',
			'NoAdt' => 1,
			'NoChd' => 0,
			'NoInf' => 0,
			'NoStop' => 'DirectOnly',
			'AirlineVendorID' => NULL,
		);
        $view->fares = $model->getFares($data);
        print_r($view->fares);die;
			

		$view = $this->getView($vName, $vFormat);
		$view->setLayout($lName);

		$view->display();		
	}

	public function testGetFares($cachable = false, $urlparams = false){
		$DepCity = JRequest::getVar('DepCity', NULL);
		$DestCity = JRequest::getVar('DestCity', NULL);
		$Depdate = JRequest::getVar('Depdate', NULL);
		$ReturnDate = JRequest::getVar('ReturnDate', NULL);
		$NoAdt = JRequest::getVar('NoAdt', NULL);
		$NoChd = JRequest::getVar('NoChd', NULL);
		$NoInf = JRequest::getVar('NoInf', NULL);
		$NoStop = JRequest::getVar('NoStop', NULL);
		$AirlineVendorID = JRequest::getVar('AirlineVendorID', NULL);

		$document	= JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->getCmd('view', 'fares');
		$vFormat = $document->getType();

		if((trim($DepCity) <> '') && (trim($DestCity) <> '') && (trim($Depdate) <> '') && (trim($ReturnDate) <> '')){
			$lName = $this->input->getCmd('layout', 'fares');	
			$model = $this->getModel();
			$data = array(
				'DepCity' => $DepCity,
				'DestCity' => $DestCity,
				'Depdate' => $Depdate,
				'ReturnDate' => $ReturnDate,
				'NoAdt' => $NoAdt,
				'NoChd' => $NoChd,
				'NoInf' => $NoInf,
				'NoStop' => $NoStop,
				'AirlineVendorID' => $AirlineVendorID,
			);
	        $view->fares = $model->getFares($data);
	        print_r($view->fares);die;
		} else {
			$lName   = $this->input->getCmd('layout', 'default');	
		}	

		$view = $this->getView($vName, $vFormat);
		$view->setLayout($lName);

		$view->display();		
	}
}