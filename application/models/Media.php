<?php
/**
 * Media types model
 * 
 * @package zfcol
 * @category application/models
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Model_Media extends Zend_Db_Table_Abstract {

    protected $_name = 'media';
    protected $_primary = 'id';
    
}