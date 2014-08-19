<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

/**
 * Hello Table class
 */
class ApiarabiaTableApiarabia extends JTable
{
  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db)
  {
    parent::__construct('#__online_api_destination', 'id', $db);
  }
  function store($updateNulls = false){
    if($this->id == 0) {
      unset($this->id);
    }
    unset($this->introtext);
    unset($this->fulltext);
    if(JTable::store() == TRUE)
    return true;
    else
    return false;
  }
}
