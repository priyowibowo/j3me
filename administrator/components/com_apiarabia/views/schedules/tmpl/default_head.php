<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
<?php
$headnya = array(
    'No',  
    'From',
    'To',
    'Departure_date',
    'Arrival_date',
    'Api',
    'adt',
    'chd',
    'inf',
    'seats',
    'curr',
    'Depart_Time',
    'Arrival_Time',
    'Code',
  );
     

  foreach($headnya as $key => $isi) {
      if($key==0){
          print '<th width="20">
                <input type="checkbox" name="toggle" value="" onclick="checkAll('.count($this->items).');" />
            </th>';
      } else {
          print '<th>';
          $key = strtoupper($isi);
          echo JText::_('COM_APIARABIA_HEADING_'.$key);
          print '</th>';
      }
  }
  ?>
</tr>