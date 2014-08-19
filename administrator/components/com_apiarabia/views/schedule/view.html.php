<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class ApiarabiaViewSchedule extends JView
{
  /**
   * FlightManagers view display method
   * @return void
   */
  function display($tpl = null)
  {
    // Get data from the model
    $item = $this->get('Item');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }
    
    // Assign data to the view
    $this->item = $item;

    // Set the toolbar
    $this->addToolBar();

    // Display the template
    parent::display($tpl);

    // Set the document
    $this->setDocument();
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {
    JToolBarHelper::title(JText::_('COM_APIARABIA_SCHEDULE'), 'con_mobile.png');
    JRequest::setVar('hidemainmenu', true);
    JToolBarHelper::save('schedule.save');
    JToolBarHelper::cancel('schedule.cancel');
  }
  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_APIARABIA_TITLE'));
    $document->addScript(JURI::root() . "administrator/components/com_apiarabia/views/schedule/submitbutton.js");
//    $script = 'if(jQuery !== undefined){'.$document->addScript(JURI::root()."administrator/components/com_apiarabia/js/jquery-1.7.1.min.js").'}';
    $document->addScript(JURI::root()."administrator/components/com_apiarabia/js/jquery-1.7.1.min.js");
    $document->addScriptDeclaration($script);
    $document->addScript(JURI::root() . "administrator/components/com_apiarabia/views/schedule/selectoption.js");
  }
}
