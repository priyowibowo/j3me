<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ApiarabiaControllerSchedule extends JControllerForm
{  
    
    public function save($key = null, $urlVar = null) {
        $id = JRequest::getVar('id');
        $from = JRequest::getVar('fromdest');
        $to = JRequest::getVar('todest');
        $from_date = JRequest::getVar('from_date');
        $to_date = JRequest::getVar('to_date');
        $api = JRequest::getVar('api');
        $adt = JRequest::getVar('adt');
        $chd = JRequest::getVar('chd');
        $inf = JRequest::getVar('inf');
        $seats = JRequest::getVar('seats');
        $curr = JRequest::getVar('curr');
        $depart_time = JRequest::getVar('Depart_Time');
        $arrival_time = JRequest::getVar('Arrival_Time');
        $code = JRequest::getVar('Code');
        
        if($from!=$to){
            $model = $this->getModel();
            
            $saved = $model->saveSchedules(
                    array(
                        'id'        => $id,
                        'from'      => $from,
                        'to'        => $to,
                        'from_date' => $from_date,
                        'to_date'   => $to_date,
                        'api'       => $api,
                        'adt'       => $adt,
                        'chd'       => $chd,
                        'inf'       => $inf,
                        'seats'     => $seats,
                        'curr'      => $curr,
                        'depart_time'   => $depart_time,
                        'arrival_time'  => $arrival_time,
                        'code'      => $code
                    )
                );
            if($saved) 
                $this->setRedirect('index.php?option=com_apiarabia&view=schedules', 'Data Saved');
            else $this->setRedirect('index.php?option=com_apiarabia&view=schedules', 'Not Saved', 'warning');
        } else
            $this->setRedirect('index.php?option=com_apiarabia&view=schedule&layout=edit', 'Cities from and to can not be the same', 'warning');
    }
}