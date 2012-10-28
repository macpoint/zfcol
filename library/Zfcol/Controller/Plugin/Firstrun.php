<?php
/**
 * Controller plugin to determine app first run
 *  
 * @package zfcol
 * @category library/Zfcol/Controller/Plugin
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Controller_Plugin_Firstrun extends Zend_Controller_Plugin_Abstract {

    /**
     * Determine app first run
     * before Zend_Controller_Front enters its dispatch loop.
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        parent::dispatchLoopStartup($request);

        if ($this->_isFirstrun()) {
            
            if (Zend_Auth::getInstance()->hasIdentity()) {
            
                // set the settings controller
                $request->setControllerName('settings');
                $request->setActionName('index');

                // get the view & assign values to it
                $view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
                $view->assign('type', 'warning')
                    ->assign('msg', $view->translate('This is the app first run. Please adjust the below settings and click "Save". You can later edit these settings in "App settings".'));
            } else {
                // set the settings controller
                $request->setControllerName('auth');
                $request->setActionName('login');
            }
        } else {
            return;
        }
    }

    /**
     * Get the firstrun param from main config
     * 
     * @return bool
     */
    private function _isFirstrun() {
        $registry = Zend_Registry::get('config');
        return $registry->firstrun == 'true' ? true : false;
    }

}