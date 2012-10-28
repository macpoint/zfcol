<?php
/**
 * View helper that outputs media type string
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Mediatype extends Zend_View_Helper_Abstract {

    /**
     * Get the media type
     * 
     * @param int $id media type id
     * @return string media type
     */
    public function mediatype($id) {

        $media = new Application_Model_Media;
        $select = $media->select()->where('id = ?', $id);
        $mediaRow = $media->fetchRow($select);

        return $mediaRow ? $mediaRow->type : 'N/A';
    }

}