<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * FlightManager View
 */
class ApiarabiaViewApiarabia extends JView
{
  /**
   * display method of Hello view
   * @return void
   */
  public function display($tpl = null)
  {
    // get the Data
    $form = $this->get('Form');
    $item = $this->get('Item');
    $script = $this->get('Script');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }
    // Assign the Data
    $this->form = $form;
    $this->item = $item;
    $this->script = $script;

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
    //		JRequest::setVar('hidemainmenu', true);
    JToolBarHelper::title(JText::_('COM_APIARABIA_HEAD_TITLE_FLIGHTSEARCH'));
    //		JToolBarHelper::save('flightmanager.save');
    //		JToolBarHelper::cancel('flightmanager.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
  }
  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $isNew = ($this->item->id < 1);
    $document = JFactory::getDocument();
    $document->setTitle($isNew ? JText::_('COM_FLIGHTMANAGER_FLIGHTMANAGER_CREATING') : JText::_('COM_FLIGHTMANAGER_FLIGHTMANAGER_EDITING'));
    $document->addScript(JURI::root() . $this->script);
    $document->addScript(JURI::root() . "/administrator/components/com_flightmanager/views/flightmanager/submitbutton.js");
    JText::script('COM_FLIGHTMANAGER_FLIGHTMANAGER_ERROR_UNACCEPTABLE');
  }
}
