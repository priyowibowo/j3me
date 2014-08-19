<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * FlightManager Model
 */
class ApinorwegianModelApinorwegian extends JModelAdmin
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
  public function getTable($type = 'Apinorwegian', $prefix = 'ApinorwegianTable', $config = array())
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
    $form = $this->loadForm('com_apinorwegian.apinorwegian', 'apinorwegian', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }
    return $form;
  }
  /**
   * Method to get the script that have to be included on the form
   *
   * @return string	Script files
   */
  public function getScript()
  {
    return 'administrator/components/com_apinorwegian/models/forms/apinorwegian.js';
  }
  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_apinorwegian.edit.apinorwegian.data', array());
    if (empty($data))
    {
      $data = $this->getItem();
    }
    return $data;
  }
  function import($data){
    $db = JFactory::getDBO();
    $query_in = $db->getQuery(true);
    $db->setQuery('TRUNCATE TABLE #__online_api_destination');
    $db->Query();
    foreach($data->destinations as $dest){
      $query_in->clear();
      $query_in->insert('#__online_api_destination');
      $query_in->set('airportName = "'.$dest->airportName.'"');
      $query_in->set('cityCode = "'.$dest->cityCode.'"');
      $query_in->set('cityName = "'.$dest->cityName.'"');
      $query_in->set('countryCode = "'.$dest->countryCode.'"');
      $query_in->set('countryName = "'.$dest->countryName.'"');
      $query_in->set('iataAirportCode = "'.$dest->iataAirportCode.'"');
      $query_in->set('shortAirportName = "'.$dest->shortAirportName.'"');
      $query_in->set('displayName = "'.$dest->displayName.'"');
      $db->setQuery((string)$query_in);
      $db->Query();
    }
    return true;
  }
}
