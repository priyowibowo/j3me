
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
	action="<?php echo JRoute::_('index.php?option=com_apiarabia&view=apiarabia&layout=booking'); ?>"
	method="post" name="adminForm" id="apiarabia-form"
	class="form-validate">
<fieldset><legend>Search Flight</legend>
<fieldset>
<table width="100%">
	<tr>
		<td width="10%"><b>Telephone</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="telephone" /></td>
	</tr>
	<tr>
		<td width="10%"><b>Email</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="email" /></td>
	</tr>
	<tr>
		<td width="10%"><b>creditcardExpiryDate</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="creditcardExpiryDate" /></td>
	</tr>
	<tr>
		<td width="10%"><b>creditcardHolderName</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="creditcardHolderName" /></td>
	</tr>
	<tr>
		<td width="10%"><b>creditcardNumber</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="creditcardNumber" /></td>
	</tr>
	<tr>
		<td width="10%"><b>creditcardType</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="creditcardType" /></td>
	</tr>
	<tr>
		<td width="10%"><b>cvc2Code</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="cvc2Code" /></td>
	</tr>
	<tr>
		<td width="10%"><b>departureTime</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="departureTime" /></td>
	</tr>
	<tr>
		<td width="10%"><b>flightCode</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="flightCode" /></td>
	</tr>
	<tr>
		<td width="10%"><b>destination</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="destination" /></td>
	</tr>
	<tr>
		<td width="10%"><b>origin</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="origin" /></td>
	</tr>
	<tr>
		<td width="10%"><b>flightLegId</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="flightLegId" /></td>
	</tr>
	<tr>
		<td width="10%"><b>transitFromLeg</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="transitFromLeg" /></td>
	</tr>
	<tr>
		<td width="10%"><b>transitToLeg</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="transitToLeg" /></td>
	</tr>
	<tr>
		<td width="10%"><b>paymentType</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="paymentType" /></td>
	</tr>
	<tr>
		<td width="10%"><b>soldInCurrency</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="soldInCurrency" /></td>
	</tr>
	<tr>
		<td width="10%"><b>soldInLanguage</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="soldInLanguage" /></td>
	</tr>
	<tr>
		<td width="10%"><b>bookingClass</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="bookingClass" /></td>
	</tr>
	<tr>
		<td width="10%"><b>flightLegId</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="flightLegId" /></td>
	</tr>
	<tr>
		<td width="10%"><b>luggageCount</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="luggageCount" /></td>
	</tr>
	<tr>
		<td width="10%"><b>paxRef</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="paxRef" /></td>
	</tr>
	<tr>
		<td width="10%"><b>firstname</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="firstname" /></td>
	</tr>
	<tr>
		<td width="10%"><b>lastname</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="lastname" /></td>
	</tr>
	<tr>
		<td width="10%"><b>paxRef</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="paxRef" /></td>
	</tr>
	<tr>
		<td width="10%"><b>paxType</b></td>
		<td width="1%">:</td>
		<td width="39%"><input type="text" name="paxType" /></td>
	</tr>

	<tr>
		<td width="10%"><input type="hidden" name="task"
			value="apiarabia.booking" /> <input type="submit" name="submit"
			value="Submit" /></td>
		<td width="1%"></td>
		<td width="39%"></td>
	</tr>
</table>
</fieldset>
</fieldset>
</form>
</div>
