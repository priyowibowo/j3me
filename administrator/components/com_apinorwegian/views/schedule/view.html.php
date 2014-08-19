<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class ApinorwegianViewSchedule extends JView
{
  /**
   * FlightManagers view display method
   * @return void
   */
  function display($tpl = null)
  {
    // Get data from the model
//    $items = $this->get('Items');
//    $pagination = $this->get('Pagination');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }
    
    // Assign data to the view
//    $this->items = $items;
//    $this->pagination = $pagination;
//    $this->state = $this->get('State');

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
    JToolBarHelper::title(JText::_('COM_FLIGHT_ADDSCHEDULE'), 'con_mobile.png');
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
    $document->setTitle(JText::_('COM_APINORWEGIAN_TITLE'));
    $document->addScript(JURI::root() . "administrator/components/com_apinorwegian/views/schedule/submitbutton.js");
    $script = 'if(jQuery !== undefined){'.$document->addScript(JURI::root()."administrator/components/com_apinorwegian/js/jquery-1.7.1.min.js").'}';
    $document->addScriptDeclaration($script);
    $document->addScript(JURI::root() . "administrator/components/com_apinorwegian/views/schedule/selectoption.js");
  }
}
