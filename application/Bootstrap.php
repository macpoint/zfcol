<?php

/**
 * Application bootstrap file
 * 
 * @package zfcol
 * @category application/
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected $_config;
    protected $_perms;

    /**
     * Initialize Zend_Controller_Front
     * to be able to use baseUrl()
     * 
     * @return Zend_Controller_Request_Http
     */
    protected function _initRequest() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
        return $request;
    }

    /**
     * Initialize PHP settings
     * 
     */
    protected function _initIni() {

        // memory limit might be increased 
        // if PDF creation fails 
        //ini_set("memory_limit", "128M");
    }

    /**
     * Initialize main config file
     * 
     */
    protected function _initConfig() {
        try {
            $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/config.xml', 'production');
        } catch (Zend_Config_Exception $e) {
            echo "Configuration file configs/config.xml is not readable!";
            exit(1);
        }
        Zend_Registry::set('config', $config);
        $this->_config = $config;
    }

    /**
     * Initialize database connection via XML config file
     * 
     */
    protected function _initDb() {
        try {
            $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/db.xml', 'production');
        } catch (Zend_Config_Exception $e) {
            echo "Configuration file configs/db.xml is not readable!";
            exit(1);
        }

        // check connection could be established
        try {
            $db = Zend_Db::factory($config->database);
            $db->getConnection();
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "Could not connect to database. Please check /application/configs/db.xml";
            exit(1);
        }

        // check table 'movies' exist (user may have not created tables)
        try {
            $db->describeTable('movies');
        } catch (Zend_Db_Exception $e) {
            echo "Table 'movies' does not exist! Please read INSTALL.txt.";
            exit(1);
        }

        // save registry key & save default DB adapter for Zend_Db_Table_Abstract
        Zend_Registry::set('db', $db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    /**
     *  Initialize Firstrun controller plugin
     * 
     */
    protected function _initFirstrun() {
        $firstrun = new Zfcol_Controller_Plugin_Firstrun();
        Zend_Controller_Front::getInstance()->registerPlugin($firstrun);
    }

    /**
     * Initialize cache for ACL
     * 
     * @throws Zend_Cache_Exception
     */
    protected function _initCache() {
        $cachedir = APPLICATION_PATH . '/../data/cache/';
        try {
            if (!is_writable($cachedir))
                throw new Zend_Cache_Exception('Cache directory ' . $cachedir . ' is not writeable!');
        } catch (Zend_Cache_Exception $e) {
            echo $e->getMessage();
            exit(1);
        }

        $appfrontend = array('lifetime' => 7200, 'automatic_serialization' => true);
        $appbackend = array('cache_dir' => APPLICATION_PATH . '/../data/cache/');
        $appcache = Zend_Cache::factory('Core', 'File', $appfrontend, $appbackend);
        Zend_Registry::set('cache_acl', $appcache);
    }

    /**
     * Initialize system ACL
     * 
     */
    protected function _initAcl() {
        try {
            $acl = new Zfcol_Controller_Plugin_Acl(APPLICATION_PATH . '/configs/permissions.xml');
        } catch (Zend_Config_Exception $e) {
            echo "Configuration file configs/permissions.xml is not readable!";
            exit(1);
        }

        Zend_Controller_Front::getInstance()->registerPlugin($acl);
        $this->_perms = $acl->getAcl();
    }

    /**
     * Initialize translations
     * 
     */
    protected function _initTranslation() {

        $locale = new Zend_Locale($this->_config->locale);
        Zend_Registry::set('Zend_Locale', $locale);

        // save the locale to session block
        $session = new Zend_Session_Namespace('zfcol');
        $langLocale = isset($session->lang) ? $session->lang : $locale;

        // set up and load the translations 
        $translate = new Zend_Translate(array(
                    'adapter' => 'gettext',
                    'content' => APPLICATION_PATH . '/languages',
                    'locale' => $langLocale,
                    'scan' => Zend_Translate::LOCALE_DIRECTORY,
                    'disableNotices' => true
                ));

        // save translations to registry
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Translate', $translate);

        // translate validators
        $v_translate = new Zend_Translate(
                        array(
                            'adapter' => 'array',
                            'content' => APPLICATION_PATH . '/languages',
                            'locale' => $langLocale,
                            'scan' => Zend_Translate::LOCALE_DIRECTORY,
                            'disableNotices' => true
                        )
        );
        Zend_Validate_Abstract::setDefaultTranslator($v_translate);
    }

    /**
     * Initialize layout headers
     * 
     */
    protected function _initHeaders() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');

        // set default head title & css
        $this->bootstrap('appinfo');
        $view->headTitle(Zend_Registry::get('AppInfo')->getTag())->setSeparator(' :: ');

        // add CSS
        $view->headLink()->appendStylesheet($view->baseUrl('css/style.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/body-' . $this->_config->font . '.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('css/datatables.css'));
        $view->headLink()->appendStylesheet($view->baseUrl('yaml/add-ons/accessible-tabs/tabs.css'));

        // add js files
        $view->headScript()->appendFile($view->baseUrl('js/jquery-1.8.2.min.js'));
        $view->headScript()->appendFile($view->baseUrl('yaml/add-ons/accessible-tabs/jquery.tabs.js'));
        $view->inlineScript()->prependFile($view->baseUrl('js/zfcol.js'));
    }

    /**
     * Initialize system navigation & acl restrictions
     * 
     */
    protected function _initNavigation() {
        try {
            $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        } catch (Zend_Config_Exception $e) {
            echo "Configuration file configs/navigation.xml is not readable!";
            exit(1);
        }

        $navigation = new Zend_Navigation($config);

        // get role
        $role = Zend_Auth::getInstance()->getIdentity() ? Zend_Auth::getInstance()->getIdentity()->role : 'guest';

        // pass navigation to view
        $view = $this->getResource('view');
        $view->navigation($navigation)->setAcl($this->_perms)->setRole($role)->setUseTranslator(Zend_Registry::get('Zend_Translate'));
    }

    /**
     * Initialize movie parser
     * 
     */
    protected function _initParser() {
        try {
            $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/parser.xml', $this->_config->parser);
        } catch (Zend_Config_Exception $e) {
            echo "Configuration file configs/parser.xml is not readable!";
            exit(1);
        }

        Zend_Registry::set('movieparser', $config);
    }

}