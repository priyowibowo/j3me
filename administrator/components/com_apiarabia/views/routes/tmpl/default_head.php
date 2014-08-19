<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
<?php
$headnya = array(
  'No',
  'origin',
  'destination',
  'transitOnly',
  'international',
  'fromDate',
  'toDate'
  );
  foreach($headnya as $key => $isi) {
    print '<th>';
    $key = strtoupper($isi);
    echo JText::_('COM_APIARABIA_ROUTES_HEADING_'.$key);
    print '</th>';
  }
  ?>
</tr>
