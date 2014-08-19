<?php
/**
 * @version		$Id: mod_travelsearch.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage	mod_travelsearch
 * @copyright	Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

require_once(JPATH_ROOT.'/administrator/components/com_apinorwegian/helpers/apinorwegian.php');

// get session search
$session =& JFactory::getSession();
$datasession = $session->get('mod_travelsearch_data');

if(isset($datasession)){
    $datasession = unserialize($datasession);
}

// Get destination from db
$origins = ApinorwegianHelper::getDestination(array('CPH')); // 'OSL',  'ARN', //'BLL'
$destinations = ApinorwegianHelper::getDestination(array('AGA', 'RAK')); //, 'AGP'

$roundtrip = modTravelSearchHelper::getRoundtripData('R');
$combi = modTravelSearchHelper::getRoundtripData('C');
$accomodation = modTravelSearchHelper::getRoundtripData('A');

require JModuleHelper::getLayoutPath('mod_travelsearch', $params->get('layout', 'default'));
