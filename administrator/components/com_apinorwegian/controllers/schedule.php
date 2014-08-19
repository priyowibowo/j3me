<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ApinorwegianControllerSchedule extends JControllerForm
{
    
    public function save($key = null, $urlVar = null) {
        $from = JRequest::getVar('fromdest');
        $to = JRequest::getVar('todest');
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        $roundtrip = JRequest::getVar('roundtrip');
        $rewrite = JRequest::getVar('rewrite');
        
        if($from!=$to){
            require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');
            JModel::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_travelsearch'.DS.'models');
            $modelTravel =& JModel::getInstance('TravelSearch', 'TravelSearchModel');
            
            $check_date = $from_date;
            
            while ($check_date <= $to_date) {
//                $departure = $modelTravel->formatDate($check_date);
                $tanggal = $check_date.'T00:00:00';
                $hasil = ApinorwegianHelper::APIflightsearchprivateclass(0, 24,'RejseEksperterneImportSchedule', 'NOK', $from, $to, $tanggal, NULL);
                
                if(is_array($hasil)&&sizeof($hasil)>0){
                    $model = $this->getModel();
                    if($rewrite) $model->rewriteSchedule($hasil);
                    $model->updateSchedules($hasil);
                }
                
                $check_date = date("Y-m-d", strtotime($check_date." +1 day"));
            }
            
            $this->setRedirect('index.php?option=com_apinorwegian&view=schedules', 'Imported From '.$from_date.' to '.$to_date);
        } else
            $this->setRedirect('index.php?option=com_apinorwegian&view=schedule&layout=edit', 'Cities from and to can not be the same', 'warning');
    }
}