<?php
/**
 * View helper that displays user name & surname
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Userinfo extends Zend_View_Helper_Abstract {

    /**
     * Get user firstname & lastname
     * 
     * @param int $id user id
     * @return string firstname & lastname
     */
    public function userinfo($id) {
        $users = new Application_Model_User();
        $select = $users->select()->where('id = ?', $id);
        $userRow = $users->fetchRow($select);

        return ($userRow) ? $userRow->first_name . " " . $userRow->last_name : 'admin';
    }
}