<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

?>
<form action="<?php echo JRoute::_('index.php?option=com_travelplan&task=testGetFares&layout=fares'); ?>"
      method="post" name="loginapi" id="loginapi-form" class="form-validate">
    <fieldset class="loginapi">
        <legend>Get Fares CRS</legend>	
    </fieldset>
    <div>
		<div class="input-value">
			DepCity (CPH, RAK etc)
			<input type="text" name="DepCity">
		</div>
		<div class="input-value">
			DestCity (CPH, RAK etc)
			<input type="text" name="DestCity">
		</div>
		<div class="input-value">
			Depdate (dd-mm-yyyy)
			<input type="text" name="Depdate">
		</div>
		<div class="input-value">
			ReturnDate (dd-mm-yyyy)
			<input type="text" name="ReturnDate">
		</div>
		<div class="input-value">
			NoAdt (0, 1, 2, 3)
			<input type="text" name="NoAdt">
		</div>
		<div class="input-value">
			NoChd (0, 1, 2, 3)
			<input type="text" name="NoChd">
		</div>
		<div class="input-value">
			NoInf (0, 1, 2, 3)
			<input type="text" name="NoInf">
		</div>
		<div class="input-value"> 
			NoStop (AllStop or DirectOnly or OneStop)
			<input type="text" name="NoStop">
		</div>
		<div class="input-value">
			<input type="submit" name="submit" value="Submit">
		</div>
	</div>
</form>