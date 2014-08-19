<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class ApiarabiaControllerRoute extends JControllerForm
{

  public function import(){
    require_once(JPATH_ROOT.'/administrator/components/com_apiarabia/helpers/apiarabia.php');
    $data = ApiarabiaHelper::APIqueryDestinations('BUDHIREQDEST', 'EN');
    $model = $this->getModel();
    $model->import($data);
    $this->setRedirect('index.php?option=com_apiarabia&view=routes');
  }
}
