<?php
/**
 * @version	$Id: router.php 2012-02-14 15:00:59 priyowibowo $
 * @subpackage	com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

/**
 * @param	array
 * @return	array
 */
function TravelSearchBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function TravelSearchParseRoute($segments)
{
	$vars = array();

	$searchword	= array_shift($segments);
	$vars['searchword'] = $searchword;
	$vars['view'] = 'search';

	return $vars;
}
