<?php
/**
 * Settings model
 * 
 * @package zfcol
 * @category application/models
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Model_Settings 
{
    private $_config;
 
    /**
     * Get the config parameters from Zend_Registry
     * 
     */
    public function __construct() {
        $this->_config = Zend_Registry::get('config');
    }
    
    /**
     * Get current config settings
     * 
     * @return array current settings
     */
    public function getCurrentSettings() {
        return $this->_config->toArray();
    }
    
    /**
     * Get available translations
     * 
     * @return array available translations
     */
    public function getAvailableTranslations() {
        
        $translate = Zend_Registry::get('Zend_Translate');
        $default['en_US'] = 'en_US';
        return is_array($translate->getAdapter()->getList()) ? array_merge($default, $translate->getAdapter()->getList()) : $default;
    }
    
    /**
     * Get available parsers
     * 
     * @return array parsers
     */
    public function getAvailableParsers() {
        $parsers = new Zend_Config_Xml(APPLICATION_PATH . '/configs/parser.xml');
        foreach ($parsers as $parser) {
            $cparser = new $parser->class;
            $aparsers[$parser->identifier] = $cparser->getParserName();
        }
        
        return $aparsers;
    }
}

