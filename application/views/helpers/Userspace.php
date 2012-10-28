<?php

/**
 * View helper to display login form / user info
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Userspace extends Zend_View_Helper_Abstract {

    private $_translate;
    
    /**
     * Load Zend_Translate object from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * Generate login / logout output
     * 
     * @return string HTML list
     */
    public function userspace() {

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $username = $auth->getIdentity()->first_name . " " . $auth->getIdentity()->last_name;
            return '<li><strong><a href="' . $this->view->url(array(
                        'controller' => 'user',
                        'action' => 'index'), null, true)
                    . '">' . $username . '</strong></a></li>
                    <li><strong><a href="' . $this->view->url(array(
                        'controller' => 'auth',
                        'action' => 'logout'), null, true)
                    . '">' . $this->_translate->translate('Logout') . '</a></strong></li>';
        } else {
            return '<li><strong><a href="' . $this->view->url(array(
                        'controller' => 'auth',
                        'action' => 'login'), null, true)
                    . '">' . $this->_translate->translate('Login') . '</a></strong></li>';
        }
    }

}