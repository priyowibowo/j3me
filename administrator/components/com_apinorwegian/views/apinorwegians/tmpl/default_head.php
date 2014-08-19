<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
<?php
$headnya = array(
  'No',
  'airportName',
  'cityCode',
  'cityName',
  'countryCode',
  'countryName',
  'iataAirportCode',
  'shortAirportName',
  'displayName'
  );
  foreach($headnya as $key => $isi) {
    print '<th>';
    $key = strtoupper($isi);
    echo JText::_('COM_APINORWEGIAN_HEADING_'.$key);
    print '</th>';
  }
  ?>
</tr>
