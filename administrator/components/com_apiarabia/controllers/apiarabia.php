<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ApiarabiaControllerApiarabia extends JControllerForm
{

  public function import(){
    require_once(JPATH_ROOT.'/administrator/components/com_apiarabia/helpers/apiarabia.php');
    $data = ApiarabiaHelper::APIqueryDestinations('RejseEksperterneDest', 'EN');
    $model = $this->getModel();
    $model->import($data);
    $this->setRedirect('index.php?option=com_apiarabia&view=apiarabias');
  }
  
  public function flighsearch(){
    $data = JRequest::get( 'post' );
    require_once(JPATH_ROOT.'/administrator/components/com_apiarabia/helpers/apiarabia.php');
    $tanggal = $data['date'].'T00:00:00';
    $hasil = ApiarabiaHelper::APIflightsearchprivateclass(0, 24,'RejseEksperterne', $data['currency'], $data['origin'], $data['destination'], $tanggal, $data['classf']);
    
    $tampil_tmp = $hasil;
    $html = '';
    $html .= '
		<fieldset>
			<legend>'.$tampilkan[0]->cityPair->origin.' - '.$tampilkan[0]->cityPair->destination.'</legend>
			<table class="adminlist">
            <thead>
                    <tr>
                        <th>No</th>
                        <th>flightCode</th>
                        <th>departureTime</th>
                        <th>operatingCarrierCode</th>
                        <th>operatingCarrierName</th>
                        <th>fareInfoRef</th>
                        <th>transitLegId</th>
                        <th>transitToLeg</th>
                        <th>transitFromLeg</th>
                        <th>preseatingAvailable</th>
                        <th>leaseType</th>
                        <th>numberOfStops</th>
                        <th>terminal</th>
                    </tr>
            </thead>
			<tbody>';		
    $no = 1;
    //  print '<pre>';
    //  print_r($tampil_tmp);die;
    //  print '</pre>';
    foreach($tampil_tmp	as $key=>$tt){
      $html .= '<tr>';
      $html .= '<td>'.$no.'</td>
			 <td>'.$tt->flightCode.'</td>
			<td>'.$tt->departureTime.'</td>
			<td>'.$tt->operatingCarrierCode.'</td>
			<td>'.$tt->operatingCarrierName.'</td>
			<td>'.$tt->transitLegId.'</td>
			<td>'.$tt->fareInfoRef.'</td>
			<td>'.$tt->transitToLeg.'</td>
			<td>'.$tt->transitFromLeg.'</td>
			<td>'.$tt->preseatingAvailable.'</td>
			<td>'.$tt->leaseType.'</td>
			<td>'.$tt->numberOfStops.'</td>
			<td>'.$tt->terminal.'</td>
    <tr>
    <td></td>
    <td colspan="2"></td>
    <td colspan="10">
    <table>
    <tr>';
      foreach($tt->bookingClasses as $bc){
        $html .= '<td>'.$bc->code.'</td>';
      }
      $html .= '</tr><tr>
        ';
      foreach($tt->bookingClasses as $bc){
        $html .= '<td>'.$bc->seatsAvailable.'</td>';
      }
      $html .= '</tr>
    </table>
    </td>';

      $html .= '</tr></tr>';
      $no++;
    }
    $html .=	'</tbody>
			</table>';
   
    print $html;
  }
}
