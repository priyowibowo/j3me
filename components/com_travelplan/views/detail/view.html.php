<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class TravelPlanViewDetail extends JViewLegacy
{
	public $travelplan;
    public $langLabels;

    // Overwriting JView display method
    function display($tpl = null) 
    {
    	$travelplan = $this->travelplan;
        $langLabels = $this->langLabels;
        
    	$name = $this->name;
    	$password = $this->password;
    	
        // Display the view
        parent::display($tpl);
    }
}