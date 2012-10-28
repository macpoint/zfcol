<?php
/**
 * View helper that outputs correct movie URL
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Movieurl extends Zend_View_Helper_Abstract {

    /**
     * Get movie URL
     * 
     * @param int $id movie id
     * @return string movie url
     */
    public function movieurl($id) {

        $movies = new Application_Model_Movies;
        $select = $movies->select()->where('id = ?', $id);
        $movieRow = $movies->fetchRow($select);
        $parser = Zend_Registry::get('movieparser');
        $baseurl = $parser->baseurl;

        return filter_var($movieRow->url, FILTER_VALIDATE_URL) ? $movieRow->url : $baseurl . $movieRow->url;
    }
}