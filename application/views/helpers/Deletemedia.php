<?php

/**
 * View helper that supports 'delete media' action
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Deletemedia extends Zend_View_Helper_Abstract {

    private $_translate;

    /**
     * Load Zend_Translate object from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * Outputs media deletion href
     * 
     * @param int $id media type id
     * @return string href output
     */
    public function deletemedia($id) {

        // do not remove media type with id = 1
        if ($id != 1)
            return "<a href=" . $this->view->url(array(
                'controller' => 'settings', 
                'action' => 'deletemedia', 
                'id' => $id, 
                'key' => $this->_getsesskey())) . 
                " class='ym-button ym-delete' style='font-size: 11px;'>" . $this->_translate->translate('Remove') . "</a>";
        else
            return '';
    }

    /**
     * Retrieves session key from Zend_Session namespace
     * 
     * @return string session key
     */
    private function _getsesskey() {
        $session = new Zend_Session_Namespace('zfcol');
        return $session->sesskey;
    }
}