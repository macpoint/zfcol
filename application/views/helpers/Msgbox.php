<?php
/**
 * View helper that displays notification messages
 * 
 * @package zfcol
 * @category application/views/helpers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zend_View_Helper_Msgbox extends Zend_View_Helper_Abstract {

    /**
     * Output popup message
     * 
     * @param string $type info|error|warn
     * @param string $msg output message
     * @return string HTML paragraph 
     */
    public function msgbox($type = false, $msg = false) {

        if ($type && $msg) {
            return "<p class='box " . $type . "'>" . $msg . "</p>";
        }
    }
}
