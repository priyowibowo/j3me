<?php
/**
 * @version     $Id: view.html.php 2012-01-19 16:00:59 priyowibowo $
 * @subpackage  com_travelsearch
 * @copyright   Copyright (C) Rejse-Eksperterne (C) 2012.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the search component
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_search
 * @since 1.0
 */
class TravelSearchViewGrouptravel extends JView
{
    
    function displayGrouptravel($model){
        $curr = "DKK";
        
        // load packages yang mau ditampilkan
        // why based on packages? because we need cities and periods
        $packages = $model->getPackagesGroupTravel();
        
        if(sizeof($packages)>0){
            $data = array();
            foreach($packages as $kunci => $objek){
                // ambil flight berangkat yang sesuai dengan tujuan pkg
                $outflight = $model->getFlightByID($objek->grouptravel_out_flight);
                
                if(sizeof($outflight)>0){
                    $inflight = $model->getFlightByID($objek->grouptravel_in_flight);

                    if(sizeof($inflight)>0){
                        $model->syncFlightPriceAndCurrency($outflight[0], $curr);
                        $model->syncFlightPriceAndCurrency($inflight[0], $curr);

                        // biaya package dan flights
                        $pricedata = $model->calculatePrice($outflight[0], $inflight[0], $objek, $curr);
                        if($pricedata) $data[] = $pricedata; 
                    }
                }
            }
            
            // perlu sorting berdasarkan tanggal
            $this->items = $data;
            
            //session 
            $session = JFactory::getSession();
            $session->set('com_travelsearch.grouptravels', serialize($this->items));
            
            // Display the view
            parent::display($tpl);
            $this->setDocument();
        } else JFactory::getApplication()->enqueueMessage(JText::_("COM_TRAVELSEARCH_NO_DATA"), 'error');
    }
    
    protected function setDocument(){
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_travelsearch/views/grouptravel/css/gt.css');
    }
    
}