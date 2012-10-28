<?php
/**
 * PDF creation model
 * 
 * @package zfcol
 * @category application/models
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Model_Pdf {

    private $_translate;

    /**
     * Get the translation object from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_translate = Zend_Registry::get('Zend_Translate');
    }

    /**
     * Generates table header for PDF output
     * 
     * @return string table header
     */
    public function getTableHeader() {
        $theader = '<table cellspacing="0" cellpadding="6" border="0.3" bordercolor="grey" width="100%">
            <thead>
                <tr>
                    <th width="7%" bgcolor="#dedede" align="center">' . $this->_translate->translate('ID') . '</th>
                    <th width="10%" bgcolor="#dedede" align="center">' . $this->_translate->translate('Custom ID') . '</th>
                    <th width="25%" bgcolor="#dedede" align="center">' . $this->_translate->translate('Title') . '</th>
                    <th width="38%" bgcolor="#dedede" align="center">' . $this->_translate->translate('Genre') . '</th>
                    <th width="11%" bgcolor="#dedede" align="center">' . $this->_translate->translate('Rating') . '</th>
                    <th width="9%" bgcolor="#dedede" align="center">' . $this->_translate->translate('Media') . '</th>
                </tr>
            </thead>';

        return $theader;
    }

    /**
     * Returns table footer for PDF output
     * 
     * @return string table footer
     */
    public function getTableFooter() {
        $footer = '</table>';
        return $footer;
    }

}

