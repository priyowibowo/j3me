<?php
class Webservice {
  protected $travelplan;
  protected $soapCustomer;
  protected $soapOnline;
  protected $lang;
  protected $fares;
  
  private function connectTravelPlanOnlineAPI(){
    try {  
          $soapclient_options = array();
          $soapclient_options['trace'] = 1;

          $wsdl = 'http://webservice.traveloffice.dk/online.asmx?wsdl' ;

          $soap = new SoapClient($wsdl, $soapclient_options);
          return $soap;
      } catch(SoapFault $e){
        $error = JText::_('COM_TRAVELPLAN_ONLINE_ERROR_API');
          JFactory::getApplication()->redirect('index.php?option=com_travelplan', $error, $e->getMessage());
          return false;
      }
  }
  
  private function connectTravelPlanCustomerAPI(){
    try {  
          $soapclient_options = array();
          $soapclient_options['trace'] = 1;

          $wsdl = 'http://webservice.traveloffice.dk/customer.asmx?wsdl' ;

          $soap = new SoapClient($wsdl, $soapclient_options);
          return $soap;
      } catch(SoapFault $e){
        $error = JText::_('COM_TRAVELPLAN_CUSTOMER_ERROR_API');
          JFactory::getApplication()->redirect('index.php?option=com_travelplan', $error, $e->getMessage());
          return false;
      }
  }
  
  private function getCompleteOrder($soap, $name, $password){
    try {  
    /**
      *ConnectionString : Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234
  *OrderNo: 5179/5284
  *PNRNO: MAN566/MAN602
      */
      $order = $soap->GETCompleteOrder(
        array(
            'ConnectionString' => 'Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234', 
            'OrderNo' => $name, 
            'PNRNo' => $password,
        )
      );

      return $order;
  } catch (SoapFault $e){
        $error = JText::_('COM_TRAVELPLAN_ERROR_API');
          JFactory::getApplication()->redirect('index.php?option=com_travelplan', $error, $e->getMessage());
          return false;
      }
  }
  
  private function getCompleteFares($soap, $data){
    try {  
      extract($data);

      $out = date('c', strtotime($Depdate));
      $in = date('c', strtotime($ReturnDate));

      $fares = $soap->GetFaresFromCRS(
        array(
          'BookingVar' => array(
            'CRS' => 'Amadeus',
            'Status' => TRUE,
            'DisconnectAfterFares' => 1,
              'LoginInfo' => array(
                'Status' => TRUE,
                'Options' => 'R,UP',
                'MyOfficeID' => 'CPHS12318',
                'IssueOfficeID' => 'ALL:CPHS12176',
                'ConnectionString' => 'Driver={SQL Native Client};Server=195.249.147.84\SELECTSQLEXPRESS;Database=SuitCaseMain;Uid=sa;Pwd=Select1234',
                'DisconnectSabre' => TRUE,
                'DoConnect'		=> TRUE,
                'TimeOut'	=> 10,
            ),
            'DepCity' => $DepCity,
            'DestCity' => $DestCity,
            'Depdate' => $out,
            'ReturnDate' => $in,
            'NoAdt' => $NoAdt,
                'NoChd' => $NoChd,
                'NoInf' => $NoInf,
                'Lang' => 'dk',
                'ErrorNo' => 1,
                'BookingNo' => '',
                'NoStop' => $NoStop,
          ),
        )
      );

      return $fares;
    } catch (SoapFault $e){
      $error = JText::_('COM_TRAVELPLAN_CUSTOMER_ERROR_API');
        JFactory::getApplication()->redirect('index.php?option=com_travelplan&task=testGetFares', $error, $e->getMessage());
        return false;
    }
  }
  
  private function getLangCustomer($soap, $travelplan){
    try {
/**
  *'Host' => 'SELECTEDT', 
  *'Password' => 'select1234#',
  *'CustLog' => array(
  *	'CustomerNo' => '28972786',
  *	'Name'		 => 'Hassan Amjad',
  *	'Address'	=> '',
  *	'Address2' => '',
  *	'ZIP' => '2500',
  *	'City' => 'Valby',
  *	'Country' => 'DK',
  *	'Phone' => '',
  *	'Fax' => '',
  *	'email' => 'hassan1988@live.dk',
  *	'password' => 'MAN620',
  *	'Vatnumber' => '',
  *	'Creditmax' => '',
  *	'Balance' => '',
  *	'PayDays' => '',
  *),
*/
      $customNum = $travelplan->GETCompleteOrderResult->Customerno;
      $customName = $travelplan->GETCompleteOrderResult->Name->Name;
      $customZip = (isset($travelplan->GETCompleteOrderResult->Name->Postal)) ? $travelplan->GETCompleteOrderResult->Name->Postal : "";
      $customCity = (isset($travelplan->GETCompleteOrderResult->Name->City)) ? $travelplan->GETCompleteOrderResult->Name->City : "";
      $customCountry = (isset($travelplan->GETCompleteOrderResult->Name->Country)) ? $travelplan->GETCompleteOrderResult->Name->Country : "";
      $customEmail = (isset($travelplan->GETCompleteOrderResult->Name->Email)) ? $travelplan->GETCompleteOrderResult->Name->Email : "";
      $customPass = (isset($travelplan->GETCompleteOrderResult->PnrNo)) ? $travelplan->GETCompleteOrderResult->PnrNo : "";
      
      $language = $soap->GetLang(
        array(
            'Host' => 'SELECTEDT', 
            'Password' => 'select1234#',
            'CustLog' => array(
                'CustomerNo' => $customNum,
                'Name'		 => $customName,
                'Address'	=> '',
                'Address2' => '',
                'ZIP' => $customZip,
                'City' => $customCity,
                'Country' => $customCountry,
                'Phone' => '',
                'Fax' => '',
                'email' => $customEmail,
                'password' => $customPass,
                'Vatnumber' => '',
                'Creditmax' => '',
                'Balance' => '',
                'PayDays' => '',
              ),
          )
      );

      return $language;
		} catch (SoapFault $e){
      $error = JText::_('COM_TRAVELPLAN_CUSTOMER_ERROR_API');
      JFactory::getApplication()->redirect('index.php?option=com_travelplan', $error, $e->getMessage());
      return false;
    }
  }
  
  
  
  public function getFares($data){
    if (!isset($this->fares)) {
        $this->soapOnline = $this->connectTravelPlanOnlineAPI();
        $this->travelplan = $this->getCompleteFares($this->soapOnline, $data);
        return $this->travelplan;
    }

    return $this->travelplan;
  }
  
  public function getTravelPlan($name, $password){
      if (!isset($this->travelplan)) {
         $this->soapCustomer = $this->connectTravelPlanCustomerAPI();
         $this->travelplan = $this->getCompleteOrder($this->soapCustomer, $name, $password);
         return $this->travelplan;
      }

      return $this->travelplan;
  }
  
  public function getLangTravelPlan($travelplan){
		if (!isset($this->lang)) {
			$this->soapCustomer = $this->connectTravelPlanOnlineAPI();
			$this->lang = $this->getLangCustomer($this->soapCustomer, $travelplan);
			return $this->lang;
		}

		return $this->lang;
  }
  
}
?>