<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modelitem');
 
/**
 * HelloWorld Model
 */
class TravelPlanModelTravelPlan extends JModelItem
{
	/**
     * @var string msg
     */
    protected $travelplan;
    protected $soapCustomer;
    protected $soapOnline;
    protected $lang;
    protected $fares;

    public function getFares($data){
    	if (!isset($this->fares)) {
           $this->soapOnline = $this->connectTravelPlanOnlineAPI();
           $this->travelplan = $this->getCompleteFares($this->soapOnline, $data);
           return $this->travelplan;
        }

        return $this->travelplan;
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
		        		'NoStop' => 'AllStop',
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

    public function printDetailPdf(){
    	require_once( JPATH_SITE . '/components/com_travelplan/lib/mypdf.php');
		$session =& JFactory::getSession();
		$travelplan = $session->get('com_travelplan.travelplandata');
		
		if(is_object($travelplan)){
			$order = $travelplan->GETCompleteOrderResult;

			$pdf = new MyPDF();
			$pdf->AddPage();
			
			$pdf->Image(JPATH_SITE . '/images/Logowebnew.jpg',105,6,100.1,29.9);
			$pdf->ln(25);

			// HEADER
			$pdf->SetFont('Arial', null, 10);
			$pdf->Cell(140, 10, $order->Name->Name, 0, 0);
			$pdf->SetFont('Arial', 'b', 12);
			$pdf->Cell(50, 5, $order->AgentName,0,1,'R');
			$pdf->SetFont('Arial', null, 10);
			
			$postal = isset($order->Name->Postal) ? $order->Name->Postal : "";
			$pdf->Cell(12, 10, $postal,0,0);
			
			$city = isset($order->Name->City) ? $order->Name->City : "";
			$pdf->Cell(128, 10, $city,0,0);

			$pdf->Cell(50, 5, $order->AgentAddress,0,2,'R');
			$pdf->Cell(50, 5, iconv('UTF-8', 'windows-1252', $order->AgentCity),0,2,'R');

			$pdf->Cell(50, 5, 'Telefon : ' . $order->UserPhone,0,2,'R');			
			$pdf->Cell(50, 5, 'Bank : Nordea - Reg : 2105',0,2,'R');			
			$pdf->Cell(50, 5, 'Konto : 0745445551',0,1,'R');			
			$pdf->ln(10);

			// BODY
			$pdf->SetFont('Arial', 'b', 16);
			$pdf->Cell(80, 10, '',0,0);
			$pdf->Cell(40, 10, '',0,0);
			$pdf->Cell(70, 10, $order->TravelStatus,0,1,'C');

		    $pdf->SetFont('Arial', 'b', 10);
		    $pdf->SetFillColor(0,0,0);
		    $pdf->SetTextColor(255,255,255);
		    
			$pdf->Cell(80, 5, 'Deltagere',0,0,null,1);			
			$pdf->Cell(40, 5, '',0,0,null,0);			
			$pdf->Cell(70, 5, '',0,1,null,1);			

			$pdf->SetFont('Arial', 'b', 10);
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(80, 5, '',0,0);			
			$pdf->Cell(40, 5, '',0,0);			
			$pdf->Cell(70, 5, 'Order nr.: '.$order->OrderNo,0,1,'C');			

			$pdf->SetFont('Arial', null, 10);
			$pdf->Cell(80, 5, '',0,0);			
			$pdf->Cell(40, 5, '',0,0);			
			$pdf->Cell(70, 5, 'PNR nr.: '.$order->PnrNo,0,1,'C');	

			$pdf->Cell(80, 1, '',0,0);			
			$pdf->Cell(40, 1, '',0,0);			
			$pdf->Cell(70, 0.3, '',0,1,'C',1);	
			$pdf->ln();

			$pdf->SetFont('Arial', null, 8);
			$pdf->Cell(80, 4, '',0,0);			
			$pdf->Cell(40, 4, '',0,0);			
			$pdf->Cell(70, 4, 'Dato: '.date("d-m-Y"),0,1,'C');	

			$pdf->SetFont('Arial', null, 8);
			$pdf->Cell(80, 4, '',0,0);			
			$pdf->Cell(40, 4, '',0,0);			
			$pdf->Cell(70, 4, 'Side: 1',0,1,'C');	
			$pdf->ln(10);

			foreach($order->PNRlist->PNRlistD->segmentList->SegmentlistD as $travel_detail){
				$pdf->SetFont('Arial', 'b', 10);
				$pdf->SetTextColor(255,255,255);

				if($travel_detail->Type == 'AIR'){
					$fratil = 'Fra/Til';
					$dato = 'Dato';
					$flvnum = 'Flv nr.';
					$tider = 'Tider:';
					$terminal = 'Terminal';
				} elseif($travel_detail->Type == 'HHL'){
					$fratil = 'Hotel';
					$dato = 'Fra/Til';
					$flvnum = 'Bekræftelses nr.';
					$tider = '';
					$terminal = 'Antal';
				} elseif($travel_detail->Type == 'TRF'){
					$fratil = 'Transport';
					$dato = 'Fra/Til';
					$flvnum = 'Bekræftelses nr.';
					$tider = '';
					$terminal = 'Antal';
				}

				$pdf->Cell(40, 6, $fratil . ':',0,0,null,1);			
				$pdf->Cell(15, 6, $dato . ':',0,0,null,1);			
				$pdf->Cell(85, 6, iconv('UTF-8', 'windows-1252', $flvnum) . ':',0,0,null,1);			 
				$pdf->Cell(15, 6, $tider,0,0,null,1);			
				$pdf->Cell(20, 6, $terminal . ':',0,0,null,1);			
				$pdf->Cell(15, 6, 'Status:',0,1,'C',1);	

				$pdf->SetTextColor(0,0,0);
				if($travel_detail->Type == 'AIR'){
					$pdf->SetFont('Arial', 'b', 10);
					$pdf->Cell(40, 6, strtoupper($travel_detail->DepCityName),0,0);			
					$pdf->SetFont('Arial', null, 10);
					$pdf->Cell(15, 6, $travel_detail->Depdate,0,0);
					$pdf->SetFont('Arial', 'b', 10);
					$pdf->Cell(85, 6, $travel_detail->Carrier." ".$travel_detail->Flightno,0,0);
					$pdf->SetFont('Arial', null, 10);
					$pdf->Cell(15, 6, $travel_detail->Deptime,0,0);
					$deptermid = (isset($travel_detail->Deptermid)) ? $travel_detail->Deptermid : "";
					$pdf->Cell(20, 6, $deptermid,0,0);
					$pdf->Cell(15, 6, $travel_detail->Status,0,1);

					$pdf->SetFont('Arial', 'b', 10);
					$pdf->Cell(40, 6, strtoupper($travel_detail->ArrCityName),0,0);			
					$pdf->SetFont('Arial', null, 10);
					$pdf->Cell(15, 6, '',0,0);
					$pdf->Cell(85, 6, $travel_detail->CarrierName,0,0);
					$pdf->Cell(15, 6, $travel_detail->Arrtime,0,0);
					$pdf->Cell(20, 6, '',0,0);
					$pdf->Cell(15, 6, '',0,1);					
				} elseif($travel_detail->Type == 'HHL' || $travel_detail->Type == 'TRF'){
					$pdf->SetFont('Arial', 'b', 10);
					$pdf->Cell(40, 6, $travel_detail->DepCityName,0,0);			
					$pdf->SetFont('Arial', null, 10);
					$pdf->Cell(15, 6, $travel_detail->Depdate,0,0);
					
					$pdf->SetFont('Arial', 'b', 10);
					$detail = ($travel_detail->Type == 'HHL') ? $travel_detail->Confirm : 'TRANSFER';
					$pdf->Cell(85, 6, iconv('UTF-8', 'windows-1252', $detail),0,0);
					
					$pdf->SetFont('Arial', null, 10);
					$pdf->Cell(15, 6, '',0,0);
					$pdf->Cell(20, 6, '',0,0);

					$pdf->Cell(15, 6, $travel_detail->Status,0,1);

					// $pdf->Cell(40, 5, iconv('UTF-8', 'windows-1252', $travel_detail->SegName),0,0);			
					$pdf->MultiCell(40,5,iconv('UTF-8', 'windows-1252', $travel_detail->SegName),0, 'l');
					$pdf->SetY($pdf->GetY()-5);
					$pdf->SetX(50);

					$pdf->Cell(15, 6, strtoupper(date("dM", strtotime($travel_detail->OutDate))),0,0);

					$pdf->SetFont('Arial', 'i', 10);
					$varText = str_replace("<br>", "\n", $travel_detail->VarText);
					$pdf->MultiCell(85,4,iconv('UTF-8', 'windows-1252', $varText),0, 'l');	
					
					$pdf->Cell(15, 6, '',0,0);
					$pdf->Cell(20, 6, '',0,0);
					$pdf->Cell(15, 6, '',0,1);
				}
			}

			$pdf->ln(10);
			$pdf->Cell(190, 0.5, '',0,1,'C',1);	
			$pdf->ln(5);

			$pdf->SetFont('Arial', 'b', 8);
			$pdf->Cell(63, 4, iconv('UTF-8', 'windows-1252', 'MØDETID I LUFTHAVNE'),0,0);			
			$pdf->Cell(64, 4, iconv('UTF-8', 'windows-1252', 'ÆNDRING OG REFUNDERING'),0,0);			
			$pdf->Cell(63, 4, 'PAS OG VISUM:',0,1);				
			$pdf->ln(3);

			$pdf->SetFont('Arial', null, 7);
			$left = "Europæiske lufthavne: Min. 1 time og 15 minutter før afgang.\n
Oversøiske lufthavne: Generelt min. 2 timer før
afgang, men dette kan dog variere.\n
UNDERSØG ALTID DEN EKSAKTE MØDETID, NÅR
RESERVATIONEN GENBEKRÆFTES.\n
GENBEKRÆFTELSE:
Alle hjem- eller vidererejser uden for Europa skal
genbekræftes hos luftfartsselskabet 3 - 7 dage før
rejsen. Undersøg samtidigt om der skal betales lokale
skatter, som ikke allerede er inkluderet i billetten. ";

			$pdf->MultiCell(60,3,iconv('UTF-8', 'windows-1252', $left),0,'c');		
			
			$center = "Langt de fleste billetter kan ikke ændres eller
refunderes efter bestillingen. Kun hvis SELECTEDT har
oplyst, at der gælder særlige regler for ovenstående
billet, kan denne ændres eller delvist refunderes.\n
Hvis returrejsen kan ændres undervejs på rejsen, skal
dette ske ved hevendelse til luftfartsselskabet lokalt.\n
SELECTEDT optræder som agent for de i transporten
deltagende luftfartsselskaber, og det er alene disse,
der har ansvaret for transportens korrekte
gennemførelse.";

			$right= "Husk altid gyldigt pas. Det er vigtigt at pas er gyldigt i
minimum 6 måneder efter hjemrejsen.\n
Undersøg om det er nødvendigt med særligt visum på
rejsen. Dette kan gøres på de enkelte landes
ambassader og konsulater eller på
www.visumformular.dk. Husk også eventuelle
vaccinationer. Ved rejser uden for Europa, skal pas
være af nyeste type.\n
Vi ønsker jer en god rejse!";

			$pdf->SetY($pdf->GetY()-48);
			$pdf->SetX(73);
			$pdf->MultiCell(60,3,iconv('UTF-8', 'windows-1252', $center),0,'c');
			$pdf->SetY($pdf->GetY()-45);
			$pdf->SetX(137);
			$pdf->MultiCell(60,3,iconv('UTF-8', 'windows-1252', $right),0,'c');

			$pdf->Output();
			die;
		}
    }

    public function printFakturaPdf(){
    	require_once( JPATH_SITE . '/components/com_travelplan/lib/mypdf.php');
		$session =& JFactory::getSession();
		$travelplan = $session->get('com_travelplan.travelplandata');

		if(is_object($travelplan)){
			$order = $travelplan->GETCompleteOrderResult;
			if(is_object($travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD)){
				$invoice = $travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD;
			} elseif(is_array($travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD)){
				$invoice = $travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD[0];

				$payment = array();
				foreach ($travelplan->GETCompleteOrderResult->InvoiceList->InvoicelistD as $key => $value) {
					if($value->InvoiceType == 'PAYMENT'){
						$payment[] = $value;
					}
				}
			}

			$pdf = new MyPDF();
			$pdf->AddPage();
			$pdf->Image(JPATH_SITE . '/images/Logowebnew.jpg',105,6,100.1,29.9);
			$pdf->ln(25);

			// HEADER
			$pdf->SetFont('Arial', null, 10);
			$pdf->Cell(140, 10, $order->Name->Name, 0, 0);
			$pdf->SetFont('Arial', 'b', 12);
			$pdf->Cell(50, 5, $order->AgentName,0,1,'R');
			$pdf->SetFont('Arial', null, 10);
			
			$postal = isset($order->Name->Postal) ? $order->Name->Postal : "";
			$pdf->Cell(12, 10, $postal,0,0);
			
			$city = isset($order->Name->City) ? $order->Name->City : "";
			$pdf->Cell(128, 10, $city,0,0);

			$pdf->Cell(50, 5, $order->AgentAddress,0,2,'R');
			$pdf->Cell(50, 5, iconv('UTF-8', 'windows-1252', $order->AgentCity),0,2,'R');

			$pdf->Cell(50, 5, 'Telefon : ' . $order->UserPhone,0,2,'R');			
			$pdf->Cell(50, 5, 'Bank : Nordea - Reg : 2105',0,2,'R');			
			$pdf->Cell(50, 5, 'Konto : 0745445551',0,1,'R');			
			$pdf->ln(10);

			// BODY
			$pdf->SetFont('Arial', 'b', 12);
			$pdf->Cell(95, 5, 'FAKTURA ' . $invoice->InvoiceNo,0,0,'l');			
			$pdf->Cell(95, 5, 'Dato: ' . date("d-m-Y", strtotime($invoice->Issuedate)),0,1,'R');	
			$pdf->ln();

			$pdf->SetFont('Arial', 'b', 10);
			$pdf->SetTextColor(255,255,255);

			$pdf->Cell(15, 6, 'Side',0,0,null,1);			
			$pdf->Cell(25, 6, 'Order nr.',0,0,null,1);			
			$pdf->Cell(25, 6, 'Kunde nr.',0,0,null,1);			 
			$pdf->Cell(30, 6, 'CVR nr.',0,0,null,1);			
			$pdf->Cell(25, 6, 'EAN nr.',0,0,null,1);			
			$pdf->Cell(25, 6, 'Afrejse',0,0,null,1);			
			$pdf->Cell(45, 6, 'Kunde ref.',0,1,null,1);			

			foreach($order->PNRlist->PNRlistD->segmentList->SegmentlistD as $travel_detail){
				$depart = date("d-m-Y", strtotime($travel_detail->WinDate));
				break;
			}	

			$pdf->SetFont('Arial', null, 10);
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(15, 6, '1',0,0,null);			
			$pdf->SetFont('Arial', 'b', 10);
			$pdf->Cell(25, 6, $order->OrderNo,0,0,null);			
			$pdf->Cell(25, 6, $invoice->CustomerNo,0,0,null);			 
			$pdf->Cell(30, 6, '',0,0,null);			
			$pdf->Cell(25, 6, '',0,0,null);			
			$pdf->Cell(25, 6, $depart,0,0,null);			
			$pdf->Cell(45, 6, '',0,1,null);

			$pdf->SetTextColor(255,255,255);
			$pdf->Cell(15, 6, 'Antal',0,0,null,1);			
			$pdf->Cell(105, 6, 'Tekst',0,0,null,1);					
			$pdf->Cell(25, 6, 'Pris pr. stk.',0,0,null,1);			
			$pdf->Cell(45, 6, 'Total DKK',0,1,null,1);		

			$pdf->SetTextColor(0,0,0);
			
			if(is_object($invoice->InvoiceLinelist->InvoiceLinelistD)){
				$pdf->SetFont('Arial', null, 10);
				$pdf->Cell(15, 6, $invoice->InvoiceLinelist->InvoiceLinelistD->Units,0,0,null);			
				
				$ypris = $pdf->GetY();
				$varText = str_replace("<br>", "\n", $invoice->InvoiceLinelist->InvoiceLinelistD->Text);
				$pdf->MultiCell(105,5,iconv('UTF-8', 'windows-1252', $varText),0, 'l');
				$ystart = $pdf->GetY();
				$pdf->SetY($ypris);
				$pdf->SetX(130);
				
				$pdf->Cell(25, 6, number_format($invoice->InvoiceLinelist->InvoiceLinelistD->Unitprice, 2, ',', '.'),0,0,null);			
				$pdf->SetFont('Arial', 'b', 10);
				$pdf->Cell(45, 6, number_format($invoice->InvoiceLinelist->InvoiceLinelistD->Total, 2, ',', '.'),0,1,null);		
			}elseif(is_array($invoice->InvoiceLinelist->InvoiceLinelistD)){
				foreach($invoice->InvoiceLinelist->InvoiceLinelistD as $key => $value){
					$pdf->SetFont('Arial', null, 10);

					if(isset($ystart)) 
						$pdf->SetY($ystart);
					
					$pdf->Cell(15, 6, $value->Units,0,0,null);			

					$varText = str_replace("<br>", "\n", $value->Text);
					$ypris = $pdf->GetY();
					$pdf->MultiCell(105,6,iconv('UTF-8', 'windows-1252', $varText),0, 'l');	
					$ystart = $pdf->GetY();
					$pdf->SetY($ypris);
					$pdf->SetX(130);
					$pdf->Cell(25, 6, number_format($value->Unitprice, 2, ',', '.'),0,0,null);			
					$pdf->SetFont('Arial', 'b', 10);
					$pdf->Cell(45, 6, number_format($value->Total, 2, ',', '.'),0,1,null);		
				}
			}

			$pdf->ln(10);
			if($invoice->IsPaid){
				$paid = "Betalt\nSelected Tour Aps";
				$pdf->SetFillColor(255,255,255);
				$pdf->RoundedRect(50, $ystart+10, 40, 20, 8, '1234', 'DF');
				
				$pdf->SetY($ystart+10);
				$pdf->Cell(190, 6, '',0,1,null);
				$pdf->Cell(40, 6, '',0,0);
				$pdf->SetFont('Arial', 'b', 12);
				$pdf->Cell(40, 6, 'Betalt',0,1,'C');
				$pdf->Cell(40, 6, '',0,0);
				$pdf->SetFont('Arial', null, 8);
				$pdf->Cell(40, 6, 'Selected Tour Aps',0,1,'C');
				$pdf->ln(10);
			}

			// total
			$pdf->Cell(145, 6, '',0,0,null);
			$pdf->SetFillColor(0,0,0);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetFont('Arial', 'b', 10);
			$pdf->Cell(45, 6, 'Total',0,1,null,1);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial', 'b', 10);
			$pdf->Cell(145, 6, '',0,0,null);
			$pdf->Cell(45, 10, number_format($invoice->Total, 2, '.', ','),0,1);

			$pdf->SetFillColor(0,0,0);
			$pdf->SetTextColor(255,255,255);
			$pdf->Cell(55, 6, iconv('UTF-8', 'windows-1252', 'Total før moms'),0,0,null,1);			

			$textdeposit = ($invoice->deposit) ? 'Depositum' : '';
			$deposittotal = ($invoice->deposit) ? $invoice->deposit : 0;

			$pdf->Cell(65, 6, $textdeposit,0,0,null,1);					
			$pdf->Cell(25, 6, 'Betalingsfrist',0,0,null,1);			
			$pdf->Cell(45, 6, 'Resterende betaling',0,1,null,1);		

			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(55, 6, number_format($invoice->Total, 2, '.', ','),0,0,null,1);			
			$pdf->SetTextColor(204,0,0);
			$pdf->Cell(65, 6, number_format($invoice->deposit, 2, '.', ','),0,0,null,1);					
			$pdf->Cell(25, 6, date("d-m-Y", strtotime($invoice->paydate)),0,0,null,1);			
			$pdf->SetTextColor(0,0,0);
			$pdf->Cell(45, 6, number_format($invoice->Total - $invoice->deposit, 2, '.', ','),0,1,null,1);		

			if(is_object($payment[0])){
				$pdf->SetFillColor(0,0,0);
				$pdf->SetTextColor(255,255,255);
				$pdf->Cell(55, 6, 'Depositum (1) / Betalingsfrist :',0,0,null,1);			
				$pdf->Cell(65, 6, '',0,0,null,1);					
				$pdf->Cell(70, 6, 'Passagerer',0,1,null,1);	

				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(255,255,255);
				$pdf->Cell(55, 6, number_format($invoice->deposit, 2, '.', ',') . ' / ' . date("d-m-Y", strtotime($invoice->depsitPayDate)),0,0,null,1);			
				$pdf->Cell(65, 6, '',0,0,null,1);					
				$pdf->SetFont('Arial', null, 8);

				$order->PnrPaxList = (isset($order->PnrPaxList)) ? $order->PnrPaxList : "" ;
				$pax = str_replace(";", "\n", $order->PnrPaxList);
				$pdf->MultiCell(70,6,iconv('UTF-8', 'windows-1252', $pax),0, 'l');
			}else {

			}
			
			$pdf->Output();
			die;
		}
    }
}