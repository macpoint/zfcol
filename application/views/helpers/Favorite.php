<?php
/**
 * View helper that formats favorite movies output
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Favorite extends Zend_View_Helper_Abstract {

    private $_translate;
  
    /**
     * Load Zend_Translate object from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * See if the movie is favorite
     * 
     * @param int $id movie id
     * @return string Yes|No
     */
    public function favorite($id) {

        $movies = new Application_Model_Movies;
        $select = $movies->select()->where('id = ?', $id);
        $movieRow = $movies->fetchRow($select);

        return ($movieRow->favorite == '1') ? $this->_translate->translate('Yes') : $this->_translate->translate('No');
    }

}
