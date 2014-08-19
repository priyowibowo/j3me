<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
?>

<form action="<?php echo JRoute::_('index.php?option=com_apinorwegian&view=schedules'); ?>" method="post" name="adminForm" id="schedule-form" class="form-validate">
<div class="width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_APINORWEGIAN_NEW_SCHEDULE');?></legend>
        <ul class="adminformlist">
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_FROM_LABEL');?></label>
                <select class="inputbox" name="fromdest">
                    <option value="" selected><?php echo JText::_('COM_APINORWEGIAN_SELECTOPTION');?></option>
                    <option value="ARN">Stockholm (Arlanda)</option>
                    <option value="CPH">Copenhagen (Copenhagen)</option>
                    <option value="OSL">Oslo (Gardermoen)</option>
                    <option value="RAK">Marrakech (Marrakech)</option>
                    <option value="AGA">Agadir (Agadir)</option>
                    <option value="AGP">Malaga (Malaga)</option>
                </select>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_TO_LABEL');?></label>
                <select class="inputbox" name="todest">
                    <option value="" selected><?php echo JText::_('COM_APINORWEGIAN_SELECTOPTION');?></option>
                    <option value="ARN">Stockholm (Arlanda)</option>
                    <option value="CPH">Copenhagen (Copenhagen)</option>
                    <option value="OSL">Oslo (Gardermoen)</option>
                    <option value="RAK">Marrakech (Marrakech)</option>
                    <option value="AGA">Agadir (Agadir)</option>
                    <option value="AGP">Malaga (Malaga)</option>
                </select>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_FROM_DATE');?></label>
                <?php 
                    echo JHTML::calendar(date("Y-m-d"), 'from_date', 'from_date', '%Y-%m-%d', array('size'=>'12', 'maxlength'=>'10',));
                ?>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_TO_DATE');?></label>
                <?php 
                    echo JHTML::calendar(date("Y-m-d"), 'to_date', 'to_date', '%Y-%m-%d', array('size'=>'12', 'maxlength'=>'10',));
                ?>
            </li>
            <!--li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_ROUNTRIP');?></label>
                <input type="checkbox" name="roundtrip" value="1">
            </li-->
            <!--li>
                <label class="hasTip"><?php echo JText::_('COM_APINORWEGIAN_DELETE_EXISTING');?></label>
                <input type="checkbox" name="rewrite" value="1">
            </li-->
        </ul>
        <input type="hidden" name="task" value="schedule.save" />
        <?php echo JHtml::_('form.token'); ?>
    </fieldset>
</form>