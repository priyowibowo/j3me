<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
class ApiarabiaModelSchedule extends JModelAdmin
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
	public function getTable($type = 'Schedule', $prefix = 'ApiarabiaTable', $config = array())
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
		$form = $this->loadForm('com_apiarabia.schedule', 'schedule', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
        
        public function saveSchedules($schedule){
            extract($schedule);
            
            $db = JFactory::getDBO();
            $sql = "SELECT  * FROM #__online_api_schedule
                    WHERE   `from` = '".$from."'
                    AND     `to` = '".$to."'
                    AND `depart_outbound` = '".$from_date."'
                    AND `arrival_outbound` = '".$to_date."'
                    AND api != 'norwegian'";
            
            $db->setQuery($sql);
            $result = $db->loadObjectList();
            
            if(sizeof($result)==0&&(!is_numeric($id))){
                $insert = "INSERT INTO #__online_api_schedule (`from`, `to`, `depart_outbound`, `arrival_outbound`, `api`, adt_price, chd_price, inf_price, seats, currency, time_depart, time_arrive, flight_code) 
                            VALUES (".$db->quote($from).", ".$db->quote($to).", ".$db->quote($from_date).", ".$db->quote($to_date).", ".$db->quote($api).", ".$db->quote($adt).", ".$db->quote($chd).", ".$db->quote($inf).", ".$db->quote($seats).", ".$db->quote($curr).", ".$db->quote($depart_time).", ".$db->quote($arrival_time).", ".$db->quote($code).")";
                
                $db->setQuery($insert);
                $db->Query();
            } else {
                $insert = "UPDATE #__online_api_schedule
                           SET      `from` = ".$db->quote($from).",
                                    `to` = ".$db->quote($to).", 
                                    `depart_outbound` = ".$db->quote($from_date).", 
                                    `arrival_outbound` = ".$db->quote($to_date).", 
                                    `api` = ".$db->quote($api).", 
                                    adt_price = ".$db->quote($adt).", 
                                    chd_price = ".$db->quote($chd).", 
                                    inf_price = ".$db->quote($inf).", 
                                    seats = ".$db->quote($seats).", 
                                    currency = ".$db->quote($curr).", 
                                    time_depart = ".$db->quote($depart_time).", 
                                    time_arrive = ".$db->quote($arrival_time).", 
                                    flight_code = ".$db->quote($code);
                
                if(isset($id))
                    $insert .= " WHERE   id = ".(int) $id;
                else $insert .= " WHERE   id = ".(int) $result[0]->id; 
                
                $db->setQuery($insert);
                $db->Query();
            }

            return true;
        }
}