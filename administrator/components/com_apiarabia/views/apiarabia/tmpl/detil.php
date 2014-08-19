
<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');
JHTML::_('behavior.calendar');
?>
<style>
.saya img {
	width: 20px;
	float: right;
	margin: 0 0 13px 0;
}
</style>
<div class="width-100 fltlft">
<form
	action="<?php echo JRoute::_('index.php?option=com_apiarabia&view=apiarabia&layout=detil'); ?>"
	method="post" name="adminForm" id="apiarabia-form"
	class="form-validate">
<fieldset><legend>Search Flight</legend>
<fieldset>
<table width="100%">
	<tr>
		<td width="10%"><b>Currency</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="currency" /></td>
	</tr>
	<tr>
		<td width="10%"><b>Origin</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="origin" /></td>
	</tr>
	<tr>
		<td width="10%"><b>Destination</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="destination" /></td>
	</tr>
	<tr>
		<td width="10%"><b>Date</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="date" /></td>
	</tr>
	<tr>
		<td width="10%"><b>Class Flight</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="classf" /></td>
	</tr>
	<tr>
		<td width="10%"><input type="hidden" name="task"
			value="apiarabia.flighsearch" /> <input type="submit"
			name="submit" value="Submit" /></td>
		<td width="1%"></td>
		<td width="39%"></td>
	</tr>
</table>
</fieldset>
</fieldset>
</form>
</div>