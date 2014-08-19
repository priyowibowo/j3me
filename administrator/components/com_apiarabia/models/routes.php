<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * FlightManagerList Model
 */
class ApiarabiaModelRoutes extends JModelList
{
  /**
   * Method to build an SQL query to load the list data.
   *
   * @return	string	An SQL query
   */
  protected function getListQuery()
  {
    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    // Select some fields
    $query->select('*');
    // From the hello table
    $query->from('#__online_api_routes');
    //                $search = $this->getState('filter.search');
    if (!empty($search)) {
      if (stripos($search, 'id:') === 0) {
        $query->where('a.id = '.(int) substr($search, 3));
      } else {
        $search = $db->Quote('%'.$db->getEscaped($search, true).'%');
        $query->where('(name LIKE '.$search.' OR flight_number LIKE '.$search.')');
      }
    }
    return $query;
  }
  protected function populateState($ordering = null, $direction = null)
  {
    // Initialise variables.
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    $this->setState('filter.search', $search);
    $tgl = $this->getUserStateFromRequest($this->context.'.filter.tgl', 'filter_tgl');
    $this->setState('filter.tgl', $tgl);

    // Load the parameters.
    $params = JComponentHelper::getParams('com_banners');
    $this->setState('params', $params);

    // List state information.
    parent::populateState('name', 'asc');
  }
}
