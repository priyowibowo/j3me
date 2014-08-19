<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modellist');

class ApinorwegianModelSchedules extends JModelList
{
    protected function getListQuery(){
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        // Select some fields
        $query->select('`id`, `from`, `to`, `depart_outbound`, `arrival_outbound`, `api`');
        // From the hello table
        $query->from('#__online_api_schedule');

        $from = $this->getState('schedules.from');
        $to = $this->getState('schedules.to');
        $date = $this->getState('schedules.date');

        if (!empty($from))
        $query->where('`from` = '."'".$from."'");

        if(!empty($to))
        $query->where('`to` = '."'".$to."'");  

        if(!empty($date)&&(trim($date)!='YYYY-MM-DD'))
        $query->where('depart_outbound >= '."'".$date."'"); 
        else
        $query->where('depart_outbound >= '."'".date('Y-m-d')."'");

        $query->order("`from`, `to`, depart_outbound");

        return $query;    
    }
  
    protected function populateState($ordering = null, $direction = null){
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $from = $this->getUserStateFromRequest($this->context.'.schedules.from', 'schedules_from');
        $this->setState('schedules.from', $from);

        $to = $this->getUserStateFromRequest($this->context.'.schedules.to', 'schedules_to');
        $this->setState('schedules.to', $to);

        $date = $this->getUserStateFromRequest($this->context.'.schedules.date', 'schedules_date');
        $this->setState('schedules.date', trim($date));

        // Load the parameters.
        $params = JComponentHelper::getParams('com_apinorwegian');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('name', 'asc');
    }
}