<?php
/**
 * @package     Travelplan
 * @subpackage  com_travelplan
 *
 * @copyright   Copyright (C) 2014 Selected Tour Aps
 */
// No direct access to this file
defined('_JEXEC') or die;

// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Travelplan');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();