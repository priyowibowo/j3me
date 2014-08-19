<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

$curr = array(
            'NOK' => 'NOK',
            'DKK' => 'DKK',
            'SEK' => 'SEK',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
        );

$cities = array(
                '' => JText::_('COM_APIARABIA_SELECTOPTION'),
                'BLL' => 'Billund',
                'ARN' => 'Stockholm (Arlanda)',
                'CPH' => 'Copenhagen', 
                'OSL' => 'Oslo (Gardermoen)',
                'RAK' => 'Marrakech',
                'AGA' => 'Agadir',
                'AGP' => 'Malaga'
            );

$this->form;
?>

<form action="<?php echo JRoute::_('index.php?option=com_apiarabia&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="schedule-form" class="form-validate">
<div class="width-60 fltlft">
    <fieldset class="adminform">
        <legend><?php echo empty($this->item->id) ? JText::_('COM_APIARABIA_NEW_SCHEDULE'): JText::sprintf('COM_APIARABIA_EDIT_SCHEDULE', $this->item->id); ?></legend>
        <ul class="adminformlist">
            
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_FROM_LABEL');?></label>
                <select class="inputbox" name="fromdest">
                    <?php
                        foreach($cities as $k => $v){
                            if($k==$this->item->from){
                                echo '<option value='.$k.' selected>'.$v.'</option>';
                            } else
                                echo '<option value='.$k.'>'.$v.'</option>';
                        }
                    ?>
                </select>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_TO_LABEL');?></label>
                <select class="inputbox" name="todest">
                    <?php
                        foreach($cities as $k => $v){
                            if($k==$this->item->to){
                                echo '<option value='.$k.' selected>'.$v.'</option>';
                            } else
                                echo '<option value='.$k.'>'.$v.'</option>';
                        }
                    ?>
                </select>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_FROM_DATE');?></label>
                <?php 
                    echo JHTML::calendar($this->item->depart_outbound, 'from_date', 'from_date', '%Y-%m-%d', array('size'=>'12', 'maxlength'=>'10',));
                ?>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_TO_DATE');?></label>
                <?php 
                    echo JHTML::calendar($this->item->arrival_outbound, 'to_date', 'to_date', '%Y-%m-%d', array('size'=>'12', 'maxlength'=>'10',));
                ?>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_API');?> / Flight Company</label>
                <input type="text" name="api" value=<?php echo $this->item->api; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_ADT');?></label>
                <input type="text" name="adt" value=<?php echo $this->item->adt_price; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_CHD');?></label>
                <input type="text" name="chd" value=<?php echo $this->item->chd_price; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_inf');?></label>
                <input type="text" name="inf" value=<?php echo $this->item->inf_price; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_seats');?></label>
                <input type="text" name="seats" value=<?php echo $this->item->seats; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_curr');?></label>
                <select name=curr id=idcurrency>
                    <?php
                        foreach($curr as $k => $v){
                            if($k==$this->item->currency){
                                echo '<option value='.$k.' selected>'.$v.'</option>';
                            } else
                                echo '<option value='.$k.'>'.$v.'</option>';
                        }
                    ?>
                </select>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_Depart_Time');?></label>
                <input type="text" name="Depart_Time" value=<?php echo $this->item->time_depart; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_Arrival_Time');?></label>
                <input type="text" name="Arrival_Time" value=<?php echo $this->item->time_arrive; ?>>
            </li>
            <li>
                <label class="hasTip"><?php echo JText::_('COM_APIARABIA_HEADING_Code');?></label>
                <input type="text" name="Code" value=<?php echo $this->item->flight_code; ?>>
            </li>
        </ul>
        <input type="hidden" name="id" value=<?php echo $this->item->id; ?> />
        <input type="hidden" name="task" value="schedule.save" />
        <?php echo JHtml::_('form.token'); ?>
    </fieldset>
</form>