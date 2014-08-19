<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * FlightManager Model
 */
class ApiarabiaModelRoute extends JModelAdmin
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
  public function getTable($type = 'Route', $prefix = 'ApiarabiaTable', $config = array())
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
    $form = $this->loadForm('com_apiarabia.apiarabia', 'apiarabia', array('control' => 'jform', 'load_data' => $loadData));
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
    return 'administrator/components/com_apiarabia/models/forms/apiarabia.js';
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
    $data = JFactory::getApplication()->getUserState('com_apiarabia.edit.apiarabia.data', array());
    if (empty($data))
    {
      $data = $this->getItem();
    }
    return $data;
  }
  function import($data){
    $db = JFactory::getDBO();
    $query_in = $db->getQuery(true);
    $db->setQuery('TRUNCATE TABLE #__online_api_routes');
    $db->Query();
    foreach($data->routes as $dest){
      $query_in->clear();
      $query_in->insert('#__online_api_routes');
      $query_in->set('origin = "'.$dest->origin.'"');
      $query_in->set('destination = "'.$dest->destination.'"');
      $query_in->set('transitOnly = "'.$dest->transitOnly.'"');
      $query_in->set('international = "'.$dest->international.'"');
      $query_in->set('fromDate = "'.$dest->fromDate.'"');
      $query_in->set('toDate = "'.$dest->toDate.'"');
      $db->setQuery((string)$query_in);
      $db->Query();
    }
    return true;
  }
}
