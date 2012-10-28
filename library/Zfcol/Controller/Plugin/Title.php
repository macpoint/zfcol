<?php
/**
 * Controller plugin to set the view script titles
 * based on the navigation lables
 *  
 * @package zfcol
 * @category library/Zfcol/Controller/Plugin
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Controller_Plugin_Title extends Zend_Controller_Plugin_Abstract {

    public static function getTitle($override = false) {
        // get the view
        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
        
        if (!$override) {

            // get active page and its label
            $activePage = $view->navigation()->findOneBy('active', true);
            $label = $activePage->get('label');

            // set page label as html title (translated if possible)
            $translation = $view->translate($label);
            return $view->headTitle($translation);
        } else {
            
            // return original string
            return $view->headTitle($override);
        }
    }
}
