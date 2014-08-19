<?php
/**
 * @version     $Id: mod_travelsearch.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage  mod_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// no direct access
defined('_JEXEC') or die;

$document = JFactory::getDocument();
// Add CSS
$document->addStyleSheet('modules/mod_travelsearch/tmpl/css/redmond/jquery-ui-1.8.18.custom.css');
$document->addStyleSheet('modules/mod_travelsearch/tmpl/css/validation/screen.css');
$document->addStyleSheet('modules/mod_travelsearch/tmpl/css/modalLoader.css');

// Add JS
//$document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery-1.7.1.min.js');
//$document->addScript('modules/mod_travelsearch/tmpl/js/mod_travelsearch.main.js');
//$document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery-ui-1.8.18.custom.min.js');
//$document->addScript('modules/mod_travelsearch/tmpl/js/jquery-ui/jquery.ui.datepicker-da.js');
$style = ".ui-autocomplete-loading { background: white url('media/system/images/mootree_loader.gif') right center no-repeat; }
    #search-hotel { width: 10em; }";
$document->addStyleDeclaration($style);
?>
<!-- bestil-din-rejse -->
<form action="<?php echo JRoute::_('index.php?option=com_travelsearch');?>" method="post" id="travel-search">    
<!-- Buat fieldset dan legend untuk tipe penerbangan -->
<fieldset id="typen">
    <legend>Vælg rejsetype</legend>
    <div>
        <span class="type-A"><input type="radio" value="A" name="holidayType" checked="checked"> <label><?php echo JText::_("MOD_TRAVELSEARCH_FLYHOTEL");?></label></span> <!--Fly &amp; Hotelpakker-->
    </div>
    <div>
        <span class="type-I"><input type="radio" value="I" name="holidayType"> <label><?php echo JText::_("MOD_TRAVELSEARCH_COMBI");?></label></span><!-- Kombinationsrejser -->
    </div>
    <div>
        <span class="type-D"><input type="radio" value="D" name="holidayType"> <label><?php echo JText::_("MOD_TRAVELSEARCH_RIAD");?></label></span> <!-- Riadpakker -->
    </div>
    
    
    <!--div>
        <span class="type-1"><input type="radio" value="R" name="holidayType"> <label>Rundrejser</label></span>
    </div>
    <div>
        <span class="type-2"><input type="radio" value="C" name="holidayType"> <label>Kombinationsrejser</label></span>
    </div-->
    <!-- div>
        <span class="type-3"><input type="radio" value="S" name="holidayType"> <label>Selfdrive</label></span>
    </div>
    <div>
        <span class="type-4"><input type="radio" value="T" name="holidayType"> <label>Theme Tours</label></span>
    </div--> 
    <!-- Buat fieldset dan legend untuk dari tanggal dan sampai tanggal -->    
</fieldset>
<!-- Buat fieldset dan legend untuk kota keberangkatan dan kota tujuan -->
<fieldset id="destination">
    <legend><?php echo JText::_("MOD_TRAVELSEARCH_CHOOSE");?></legend> <!-- Vælg afrejselufthavn -->
        <div>
            <label for="fromDest" class="ldes"><?php echo JText::_("MOD_TRAVELSEARCH_FROM");?></label> <!-- fra -->
            <select name="fromdest" id="fromdest">
            <?php 
                foreach ($origins as $i => $obj){
                    if($obj->iataAirportCode=='CPH'){
                        echo "<option value=".$obj->iataAirportCode." selected=selected>".$obj->cityName."</option>";    
                    } else
                        echo "<option value=".$obj->iataAirportCode.">".$obj->cityName."</option>";
                }
            ?>
            </select>
        </div>
        <div id="div-todest">
            <label for="toDest" class="ldes"><?php echo JText::_("MOD_TRAVELSEARCH_TO");?></label> <!-- til -->
            <select name="todest" id="todest">
                <option value=""><?php echo JText::_("MOD_TRAVELSEARCH_CHOOSEFROM");?></option> <!-- V&aelig;lg rejsem&aring;l -->
                <?php
    //                foreach ($destinations as $i => $obj){
    //                    echo "<option value=".$obj->iataAirportCode.">".$obj->cityName."</option>";
    //                } 
    //            hardcoded, if its billund, all dest is RAK, if its from copenhagen then agadir is aga and rest is rak
                ?>
                <option value="RAK">Marrakech</option>
                <option value="AGA">Agadir</option>
                <option value="RAK-ESS">Essaouira</option>
                <option value="RAK-CAS">Casablanca</option>
            </select>
        </div>
</fieldset>
<!-- Hotel Category remove temporary -->
<!--fieldset id="fieldset-category">
    <legend>Vælg hotelkategori</legend>
    <div>    
        <label class="lcat">Kategori</label>
        <select name="category" id="select-category">
            <option value="">Alle kategorier</option>
            <option value="2">2 Stjerner</option>
            <option value="3">3 Stjerner</option>
            <option value="4">4 Stjerner</option>
            <option value="5">5 Stjerner</option>
        </select>
    </div>
    <div id="div-todest">
        <label for="accomodation" class="lcat">Hotel</label>
        <select name="accomodation" id="accomodation">
            <option value=all>Alle hoteller</option>
        </select>
        </div>
    <!-- autocomplete not used
    div>    
        <label>Hotel</label>
        <input type="text" name="search-hotel" id="search-hotel">
        <input type="hidden" name="hotel" id="hotel" value="">
    </div>
    <div>    
        <label>Alle Hotel</label>
        <input type="checkbox" name="allhotel" id="all-hotel" value="1">
    </div-->
<!--/fieldset-->
<!-- Roundtrip -->
<fieldset id=fieldset-roundtrip style="display:none;">
    <legend>Vælg rundrejse</legend>
    <div>
        <label for="roundtrip">Valgmulighed</label>
        <select name="roundtrip" id="roundtrip">
            <option value="" selected>Alle rundrejser</option>
            <?php 
            foreach ($roundtrip as $i => $obj){
                echo "<option value=".$obj->id.">".$obj->product_name."</option>";
            }
            ?>
        </select>
    </div>
</fieldset>
<!-- Combination -->
<fieldset id=fieldset-combination style="display:none;">
    <legend>Vælg kombinationsrejse</legend>
    <div>
        <label for="combination">Valgmulighed</label>
        <select name="combination" id="combination">
            <option value="" selected>Alle kombinationsrejser</option>
            <?php 
            foreach ($combi as $i => $obj){
                echo "<option value=".$obj->id.">".$obj->product_name."</option>";
            }
            ?>
        </select>
    </div>
</fieldset>
<!-- Tanggal pergi dan pulang -->
<fieldset id="fieldset-date">
    <legend><?php echo JText::_("MOD_TRAVELSEARCH_CHOOSEDATE");?></legend> <!-- Vælg dato -->
    <div>    
        <label class="ldat"><?php echo JText::_("MOD_TRAVELSEARCH_DATEDEPART");?></label> <!-- Afrejsedato -->
        <input type="text" value="" name="fromDate" id="fromDate" size="8">
    </div>
    <div id="returnDate">
        <label class="ldat"><?php echo JText::_("MOD_TRAVELSEARCH_DATEBACK");?></label> <!-- Hjemrejsedato -->
        <input type="text" value="" name="toDate" id="toDate" size="8">
    </div>
</fieldset>
<!-- Buat fieldset dan legend untuk penumpang -->
<fieldset id="Passangers">
    <legend><?php echo JText::_("MOD_TRAVELSEARCH_CHOOSEROOM");?></legend><!-- Vælg værelser -->
        <div class="rooms" id="room-1">
        <label class="lpas"><?php echo JText::_("MOD_TRAVELSEARCH_ROOM");?> 1</label></br></br> <!-- V&aelig;relse -->
        <label class="lpas"><?php echo JText::_("MOD_TRAVELSEARCH_ADT");?></label> <!-- Voksne -->
        <select name="adultroom1" id="adult-room-1" class="adult-room">
            <option value="1">1</option>
            <option value="2" selected="selected">2</option>
            <option value="3">3</option>
        </select></br>
        <label class="lpas"><?php echo JText::_("MOD_TRAVELSEARCH_CHD");?></label> <!-- Barn (2-11 år) -->
        <select name="childroom1" id="child-room-1" class="child-room">
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select></br>
        <label class="lpas"><?php echo JText::_("MOD_TRAVELSEARCH_INF");?></label><!-- Barn (< 2 år) -->
        <select name="babyroom1" id="baby-room-1" class="baby-room">
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select>
        </div>
        <div>
            <input type="button" name="addroom" value="<?php echo JText::_("MOD_TRAVELSEARCH_ADDROOM");?>" id="add-room-hotel"> <!-- Tilføj værelse -->
        </div>
</fieldset>
<!-- Buat currency -->
<fieldset id="Currency">
    <label id="Currency"><?php echo JText::_("MOD_TRAVELSEARCH_CURR");?></label><!-- Valuta -->
        <select name=currency id=idcurrency>
            <option value="NOK">NOK</option>
            <option value="SEK">SEK</option>
            <option value="DKK" selected="selected">DKK</option>
            <option value="EUR">EUR</option>
            <option value="GBP">GBP</option>
        </select>
    
</fieldset></br>
<div id="loadingScreen"></div>
<input class="button" type="submit" value="<?php echo JText::_("MOD_TRAVELSEARCH_FIND");?>" id="search-button">  <!-- Find rejse -->
<!input type="hidden" name="option" value="com_travelsearch" /-->
<!-- input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" / -->
<input type="hidden" name="mod_search" value=<?php echo json_encode($datasession); ?> id=mod-search>
<input type="hidden" name="mod_srcapi" value="" id=mod-srcapi>
<input type="hidden" name="mod_hotels" value="" id=mod-hotels>
</form>      