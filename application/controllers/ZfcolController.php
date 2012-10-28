<?php
/**
 * Application support controller
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class ZfcolController extends Zend_Controller_Action {

    private $_yaml_version = '4.0.1';
    private $_tcpdf_version = '5.9';
    private $_simple_html_dom_version = '1.5';

    public function init() {}

    /**
     * Forward to 'about' action
     * 
     * @return void
     */
    public function indexAction() {
        $this->_forward('about');
        return;
    }

    /**
     * Generate 'about' content links & versions
     * 
     */
    public function aboutAction() {
        $version = array();
        $version['zfcol'] = Zend_Registry::get('AppInfo')->getVersion();
        $version['zf'] = Zend_Version::VERSION;
        $version['php'] = phpversion();
        $version['yaml'] = $this->_yaml_version;
        $version['tcpdf'] = $this->_tcpdf_version;
        $version['simple_html_dom'] = $this->_simple_html_dom_version;

        $www = array();
        $www['zfcol'] = Zend_Registry::get('AppInfo')->getWebpage();
        $www['zf'] = 'http://framewerk.zend.com';
        $www['php'] = 'http://www.php.net';
        $www['yaml'] = 'http://www.yaml.de';
        $www['tcpdf'] = 'http://www.tcpdf.org';
        $www['simple_html_dom'] = 'http://simplehtmldom.sourceforge.net';

        $this->view->version = $version;
        $this->view->www = $www;
    }
}