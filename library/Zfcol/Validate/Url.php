<?php
/**
 * URL validate class
 * 
 * @package zfcol
 * @category library/Zfcol/Validate
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Validate_Url extends Zend_Validate_Abstract {

    const ERROR = 'url';

    // generate error message
    protected $_messageTemplates = array(
        self::ERROR => "Invalid URL format"
    );

    // check URL is valid
    public function isValid($value) {
        $this->_error(self::ERROR);
        return Zend_Uri::check($value);
    }
}