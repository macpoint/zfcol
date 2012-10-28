<?php
/**
 * View helper that supports 'delete user' action
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Deleteuser extends Zend_View_Helper_Abstract {

    private $_translate;

    /**
     * Load Zend_Translate object from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * Outputs user deletion href
     * 
     * @param int $id
     * @return string href output
     */
    public function deleteuser($id) {

        if (($id != $this->_getuserid()) && ($id != 1))
            return "<a href=" . $this->view->url(array(
                'controller' => 'users', 
                'action' => 'delete', 
                'id' => $id, 
                'key' => $this->_getsesskey())) . 
                " class='ym-button ym-delete' style='font-size: 11px;'>" . $this->_translate->translate('Remove') . "</a>";
        else
            return '';
    }

    /**
     * Get current user id
     * 
     * @return int user id
     */
    private function _getuserid() {
        $auth = Zend_Auth::getInstance();
        return $auth->getIdentity()->id;
    }

    /**
     * Get session key from Zend_Session namespace
     * 
     * @return string session key
     */
    private function _getsesskey() {
        $session = new Zend_Session_Namespace('zfcol');
        return $session->sesskey;
    }
}