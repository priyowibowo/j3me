<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of FlightManager component
 */
class ApiarabiaController extends JController
{
  /**
   * display task
   *
   * @return void
   */
  function display($cachable = false, $urlparams = false)
  {
    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'Apiarabias'));
    
    // call parent behavior
    parent::display();
    require_once(JPATH_ROOT.'/administrator/components/com_apiarabia/helpers/apiarabia.php');
    if(JRequest::getVar('view')) $vw = JRequest::getVar('view');
    else $vw = 'apiarabia';
    $vw = strtolower($vw);
    
    ApiarabiaHelper::addSubmenu($vw);
  }
}
