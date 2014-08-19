<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ApinorwegianControllerRoute extends JControllerForm
{

  public function import(){
    require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');
    $data = ApinorwegianHelper::APIqueryDestinations('BUDHIREQDEST', 'EN');
    $model = $this->getModel();
    $model->import($data);
    $this->setRedirect('index.php?option=com_apinorwegian&view=routes');
  }
}
