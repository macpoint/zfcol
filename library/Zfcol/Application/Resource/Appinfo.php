<?php
/**
 * AppInfo resource plugin
 * 
 * @package zfcol
 * @category library/Zfcol/Application/Resource
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Application_Resource_Appinfo extends Zend_Application_Resource_ResourceAbstract {

    const REGISTRY_KEY = 'AppInfo';

    protected $_appinfo;

    public function init() {
        return $this->getAppInfo();
    }

    public function getAppInfo() {
        if ($this->_appinfo == null) {
            $options = $this->getOptions();
            $name = $options['name'];
            $tag = $options['tag'];
            $webpage = $options['webpage'];
            $author = $options['author'];
            $version = $options['version'];
            $email = $options['email'];

            $this->_appinfo = new Zfcol_AppInfo(
                            $name,
                            $tag,
                            $webpage,
                            $author,
                            $version,
                            $email);

            Zend_Registry::set(self::REGISTRY_KEY, $this->_appinfo);
        }
        return $this->_appinfo;
    }

}