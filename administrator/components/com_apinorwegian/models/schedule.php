<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
class ApinorwegianModelSchedule extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Schedule', $prefix = 'ApinorwegianTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
        
        /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_apinorwegian.schedule', 'schedule', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
        
        public function rewriteSchedule($schedule){
            foreach($schedule as $code => $data){
                $from = $data->origin;
                $to = $data->destination;
            }
            
            $db = JFactory::getDBO();
            $sql = "DELETE  FROM #__online_api_schedule
                    WHERE   `from` = '".$from."'
                    AND     `to` = '".$to."'
                    AND     `api` = 'norwegian'";

            $db->setQuery($sql);
            $db->Query();
            return true;
        }
        
        public function updateSchedules($schedule){
            foreach($schedule as $code => $data){
                $from = $data->origin;
                $to = $data->destination;
                $dep = $data->departureTime;
                $expdep = explode("T", $dep);
                $arr = $data->arrivalTime;
                $arrdep = explode("T", $arr);                
            }
            
            $db = JFactory::getDBO();
            $sql = "SELECT  * FROM #__online_api_schedule
                    WHERE   `from` = '".$from."'
                    AND     `to` = '".$to."'
                    AND `depart_outbound` = '".$expdep[0]."'
                    AND `arrival_outbound` = '".$arrdep[0]."'
                    AND `api` = 'norwegian'";
            
            $db->setQuery($sql);
            $result = $db->loadObjectList();
            
            if(sizeof($result)==0){
                $insert = "INSERT INTO #__online_api_schedule(`from`, `to`, `depart_outbound`, `arrival_outbound`, `api`) VALUES ('".$from."', '".$to."', '".$expdep[0]."', '".$arrdep[0]."', 'norwegian')";
                $db->setQuery($insert);
                $db->Query();
            } 
            
            return true;
        }
}