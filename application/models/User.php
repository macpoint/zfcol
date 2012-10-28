<?php
/**
 * User model
 * 
 * @package zfcol
 * @category application/models
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Model_User extends Zend_Db_Table_Abstract {

    protected $_name = 'users';
    protected $_primary = 'id';

    /**
     * Check username exists
     * 
     * @param string $name user name
     * @return bool
     */
    public function userExists($name) {
        $validator = new Zend_Validate_Db_RecordExists(array(
                    'table' => $this->_name,
                    'field' => 'username')
        );
        return $validator->isValid($name) ? true : false;
    }
}