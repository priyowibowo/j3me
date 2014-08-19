<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of FlightManager component
 */
class ApinorwegianController extends JController
{
  /**
   * display task
   *
   * @return void
   */
  function display($cachable = false, $urlparams = false)
  {
    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'Apinorwegians'));
    
    // call parent behavior
    parent::display();
    require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');
    if(JRequest::getVar('view')) $vw = JRequest::getVar('view');
    else $vw = 'apinorwegians';
    $vw = strtolower($vw);
    
    ApinorwegianHelper::addSubmenu($vw);
  }
}
