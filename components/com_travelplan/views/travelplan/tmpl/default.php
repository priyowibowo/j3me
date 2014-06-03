<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

?>
<form action="<?php echo JRoute::_('index.php?option=com_travelplan&task=detail'); ?>"
      method="post" name="loginapi" id="loginapi-form" class="form-validate">
    <fieldset class="loginapi">
        <legend><?php echo JText::_( 'COM_TRAVELPLAN_LOGIN' ); ?></legend>	
    </fieldset>
    <div>
		<div class="input-label">
			<?php echo JText::_( 'COM_TRAVELPLAN_ORDER_CUST' ); ?>
			<input type="text" name="Orderno">
		</div>
		<div class="input-value">
			<?php echo JText::_( 'COM_TRAVELPLAN_PASSWORD' ); ?>
			<input type="password" name="Password">
		</div>
		<div class="input-value">
			<input type="submit" name="submit" value="Submit">
		</div>
	</div>
</form>