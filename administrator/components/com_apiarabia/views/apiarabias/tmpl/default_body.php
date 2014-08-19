<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php
$no = 1;
foreach($this->items as $i => $item): ?>
<tr class="row<?php echo $i % 2; ?>">
<?php
foreach($item as $key_item => $isi_item) {
  print '<td>';
  if($key_item!='id')echo $isi_item;
  else echo $no;
  print '</td>';
}
$no++;
?>
</tr>
<?php endforeach; ?>
