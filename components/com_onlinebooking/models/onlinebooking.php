<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modelitem');
 
/**
 * HelloWorld Model
 */
class OnlinebookingModelOnlinebooking extends JModelItem
{
	/**
     * @var string msg
     */
    protected $travelplan;
    protected $soapCustomer;
    protected $soapOnline;
    protected $lang;
    protected $fares;

    private function obConnectTravelPlanOnlineAPI(){
    	try {  
            $soapclient_options = array();
            $soapclient_options['trace'] = 1;
           
            $wsdl = 'http://webservice.traveloffice.dk/online.asmx?wsdl' ;
            
            $soap = new SoapClient($wsdl, $soapclient_options);
            return $soap;
        } catch(SoapFault $e){
        	$error = JText::_('COM_TRAVELPLAN_ONLINE_ERROR_API');
            JFactory::getApplication()->redirect('index.php?option=com_onlinebooking', $error, $e->getMessage());
            return false;
        }
    }

    public function getFares($data){
    	if (!isset($this->fares)) {
           $this->soapOnline = $this->obConnectTravelPlanOnlineAPI();
           $this->travelplan = $this->getCompleteFares($this->soapOnline, $data);
           return $this->travelplan;
        }

        return $this->travelplan;
    }

    private function getCompleteFares($soap, $data){
    	try {  
	    	extract($data);

	  //   	$out = date('c', strtotime($Depdate));
			// $in = date('c', strtotime($ReturnDate));

			$out = '2014-05-24T00:00:00';
  			$in = '2014-05-31T00:00:00';
echo $Depdate."<br>";
die(date('Y-m-d', strtotime($Depdate)) . 'T00:00:00');
			$fares = $soap->GetFaresFromCRS(
				array(
					'BookingVar' => array(
						'CRS' => 'Amadeus',
						'Status' => FALSE,
						'DisconnectAfterFares' => FALSE,
		    			'LoginInfo' => array(
		    				'Status' => FALSE,
			                'Options' => 'r,up/ady',
			                'MyOfficeID' => 'CPHS12318',
			                'IssueOfficeID' => 'ALL:CPHS12176',
			                'ConnectionString' => 'Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234',
			                'DisconnectSabre' => FALSE,
			                'DoConnect'   => TRUE,
			                'TimeOut' => 10,
						),
						'DepCity' => $DepCity,
						'DestCity' => $DestCity,
						'Depdate' => $out,
						'ReturnDate' => $in,
						'NoAdt' => $NoAdt,
		        		'NoChd' => $NoChd,
		        		'NoInf' => $NoInf,
		        		'Lang' => 'da',
		        		'ErrorNo' => '',
		        		'BookingNo' => '',
		        		'NoStop' => 'DirectOnly',
		        		// 'AirlineVendorID' => $AirlineVendorID,
		        		'Agency' => array(
			              'CompanyName' => 'Selected Tours ApS',
			              'Address' => 'Vermundsgade 38',
			              'City' => 'Copenhagen',
			              'StateCode' => '',
			              'PostalCode' => '2100',
			              'CountryCode' => 'DK',
			            )
					),
				)
		    );

		    return $fares;
	    } catch (SoapFault $e){
        	$error = JText::_('COM_TRAVELPLAN_CUSTOMER_ERROR_API');
            JFactory::getApplication()->redirect('index.php?option=com_onlinebooking&task=testGetFares', $error, $e->getMessage());
            return false;
        }
    }
}