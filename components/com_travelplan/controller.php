<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
class TravelplanController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function detail($cachable = false, $urlparams = false){
		$name = JRequest::getVar('Orderno', NULL);
		$password = JRequest::getVar('Password', NULL);
		$language = JRequest::getVar('Lang', NULL);

		if(trim($name) == '' || trim($password) == ''){
			$error = JText::_('COM_TRAVELPLAN_MISSING_PARAM');
            JFactory::getApplication()->redirect('index.php?option=com_travelplan', $error, 'error' );
            return false;
		}

		// Get the document object.
		$document	= JFactory::getDocument();
		$document->addStyleSheet(JURI::base() . 'components/com_travelplan/views/css/style.css');
		$document->addScript(JURI::base() . 'components/com_travelplan/views/js/scripts.js');

		// Set the default view name and format from the Request.
		$vName   = $this->input->getCmd('view', 'detail');
		$vFormat = $document->getType();
		$lName   = $this->input->getCmd('layout', 'detail');

		$view = $this->getView($vName, $vFormat);
		$view->setLayout($lName);

		// Assign data to the view
		$model = $this->getModel();
        $view->travelplan = $model->getTravelPlan($name, $password);
        $view->langLabels = $model->getLangTravelPlan($view->travelplan);

        $view->name = $name;
        $view->password = $password;

        $session =& JFactory::getSession();
		$session->set('com_travelplan.travelplandata', $view->travelplan);
        
		$view->display();
	}	

	public function printFaktura($cachable = false, $urlparams = false){
		$model = $this->getModel();
        $model->printFakturaPdf();
	}

	public function printDetail($cachable = false, $urlparams = false){
		$model = $this->getModel();
        $model->printDetailPdf();
	}	

	public function testGetFares($cachable = false, $urlparams = false){
		$DepCity = JRequest::getVar('DepCity', NULL);
		$DestCity = JRequest::getVar('DestCity', NULL);
		$Depdate = JRequest::getVar('Depdate', NULL);
		$ReturnDate = JRequest::getVar('ReturnDate', NULL);
		$NoAdt = JRequest::getVar('NoAdt', NULL);
		$NoChd = JRequest::getVar('NoChd', NULL);
		$NoInf = JRequest::getVar('NoInf', NULL);
		$NoStop = JRequest::getVar('NoStop', NULL);

		$document	= JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->getCmd('view', 'fares');
		$vFormat = $document->getType();

		if((trim($DepCity) <> '') && (trim($DestCity) <> '') && (trim($Depdate) <> '') && (trim($ReturnDate) <> '')){
			$lName = $this->input->getCmd('layout', 'fares');	
			$model = $this->getModel();
			$data = array(
				'DepCity' => $DepCity,
				'DestCity' => $DestCity,
				'Depdate' => $Depdate,
				'ReturnDate' => $ReturnDate,
				'NoAdt' => $NoAdt,
				'NoChd' => $NoChd,
				'NoInf' => $NoInf,
				'NoStop' => $NoStop,
			);
	        $view->fares = $model->getFares($data);
	        print_r($view->fares);die;
		} else {
			$lName   = $this->input->getCmd('layout', 'default');	
		}	

		$view = $this->getView($vName, $vFormat);
		$view->setLayout($lName);

		$view->display();		
	}
}