<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * Controller to handle after flight search UI
 */
class ApinorwegianControllerApinorwegians extends JControllerAdmin
{
  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function getModel($name = 'Apinorwegian', $prefix = 'ApinorwegianModel')
  {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }
}
