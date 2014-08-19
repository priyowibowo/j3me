<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHTML::_('stylesheet','system/jquery.ui.all.css',false,true);
JHTML::_('script','system/multiselect.js',false,true);
JHTML::_('script','system/jquery-1.6.2.js',false,true);
JHTML::_('script','system/jquery.ui.core.js',false,true);
JHTML::_('script','system/jquery.ui.widget.js',false,true);
JHTML::_('script','system/jquery.ui.datepicker.js',false,true);

$cities = array("" => JText::_('COM_APIARABIA_SELECTOPTION'), 
                "ARN" => "Stockholm (Arlanda)", 
                "CPH" => "Copenhagen (Copenhagen)", 
                "OSL" => "Oslo (Gardermoen)",
                "RAK" => "Marrakech (Marrakech)",
                "AGA" => "Agadir (Agadir)");
?>

<form action="<?php echo JRoute::_('index.php?option=com_apiarabia&view=schedules'); ?>" method="post" name="adminForm" id="schedule-form">
<fieldset id="filter-bar">
    <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="schedules_from"><?php echo JText::_('COM_APIARABIA_FROM_LABEL'); ?></label>
            <select class="inputbox" name="schedules_from" id="schedules_from" title="<?php echo JText::_('COM_APIARABIA_FROM_LABEL'); ?>">
                <?php 
                    foreach($cities as $k => $v){
                        echo "<option value=".$k; ?>
                        <?php if(($k==$this->escape($this->state->get('schedules.from')))&&($k!='')) echo "selected"; ?>                            
                        <?php echo ">".$v."</option>";
                    }
                ?>        
            </select>
            <label class="filter-search-lbl" for="schedules_to"><?php echo JText::_('COM_APIARABIA_TO_LABEL'); ?></label>
            <select class="inputbox" name="schedules_to" id="schedules_to" title="<?php echo JText::_('COM_APIARABIA_TO_LABEL'); ?>">
                <?php 
                    foreach($cities as $k => $v){
                        echo "<option value=".$k; ?>
                        <?php if(($k==$this->escape($this->state->get('schedules.to')))&&($k!='')) echo "selected"; ?>                            
                        <?php echo ">".$v."</option>";
                    }
                ?>        
            </select>
            <label class="filter-search-lbl" for="schedules_date"><?php echo JText::_('COM_APIARABIA_DATE_LABEL'); ?></label>
            <input class="inputbox" name="schedules_date" id="schedules_date" value="
                <?php if(trim($this->escape($this->state->get('schedules.date')))!='YYYY-MM-DD') 
                        echo trim($this->escape($this->state->get('schedules.date')));
                      else echo trim('YYYY-MM-DD'); ?>" 
                      title="<?php echo JText::_('COM_APIARABIA_DATE_LABEL'); ?>">
            <button type="submit"><?php echo JText::_('COM_APIARABIA_SEARCH'); ?></button>
            <button type="button" onclick="document.id('schedules_date').value='YYYY-MM-DD';document.id('schedules_from').selectedIndex=0;document.id('schedules_to').selectedIndex=0;this.form.submit();"><?php echo JText::_('COM_APIARABIA_CLEAR'); ?></button>
    </div>
</fieldset>
<table class="adminlist">
	<thead>
	<?php echo $this->loadTemplate('head');?>
	</thead>
	<tfoot>
	<?php echo $this->loadTemplate('foot');?>
	</tfoot>
	<tbody>
	<?php echo $this->loadTemplate('body');?>
	</tbody>
</table>
<div>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="" /> <input type="hidden"
	name="boxchecked" value="0" /> <?php echo JHtml::_('form.token'); ?></div>
</form>
