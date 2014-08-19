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
?>
<script> 
	$(function() {
		$( "#filter_tgl" ).datepicker({ dateFormat: 'yy-mm-dd' });
	});
	</script>
<form
	action="<?php echo JRoute::_('index.php?option=com_apinorwegian&view=routes'); ?>"
	method="post" name="adminForm">
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
<div><input type="hidden" name="task" value="" /> <input type="hidden"
	name="boxchecked" value="0" /> <?php echo JHtml::_('form.token'); ?></div>
</form>
