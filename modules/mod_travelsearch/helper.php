<?php
/**
 * @version      $Id: mod_travelsearch.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage  mod_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// no direct access
defined('_JEXEC') or die;

class modTravelSearchHelper
{
//	public static function getSearchImage($button_text)
//	{
//		$img = JHtml::_('image','searchButton.gif', $button_text, NULL, true, true);
//		return $img;
//	}
    public static function getRoundtripData($type = 'A'){
        $db = JFactory::getDBO();
        $query_in = $db->getQuery(true);
        
        if($type=='A'){
            $sql = "SELECT  pkg.*, 
                            hotel.name, hotel.category, hotel.location_code 
                    FROM #__online_pkg_desc_test as pkg
                    INNER JOIN #__online_hotels_test as hotel
                        ON hotel.id = pkg.hotel_id";
        } else {
            $sql = "SELECT *
                    FROM #__online_pkg_desc_test as pkg";
        }
        
        if(isset($type)){
            $sql .= " WHERE travel_type = '".$type."'";
        }
        
        if($type=='A')
            $sql .= " ORDER BY hotel.category";

        $db->setQuery($sql);
        $db->Query();
        return $db->loadObjectList();
    }
}