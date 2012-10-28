<?php
/**
 * User authentication class
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class AuthController extends Zend_Controller_Action {

    public function init() {}

    /**
     * Forward to login action
     * 
     * @return type
     */
    public function indexAction() {
        $this->_forward('login');
        return;
    }

    /**
     * Perform the login & auth procedure
     * 
     * @return void
     */
    public function loginAction() {
        $db = Zend_Registry::get('db');
        $loginForm = new Application_Form_Login ();
        
        // Form was submitted
        if ($this->getRequest()->isPost()) {

            if ($loginForm->isValid($_POST)) {
                
                $adapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'username', 'password', 'SHA1(?)');

                $adapter->setIdentity($loginForm->getValue('username'));
                $adapter->setCredential($loginForm->getValue('password'));

                // check login credentials are correct
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    $user = $adapter->getResultRowObject();
                    $auth->getStorage()->write($user);
                    
                    $session = new Zend_Session_Namespace('zfcol');
                    $session->sesskey = sha1(uniqid(mt_rand(), true));
                    $this->_redirect('/');
                    return;
                } else {
                    $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Bad username / password'));
                    $this->view->form = $loginForm;
                }
            } else {
                // form invalid
                $this->view->assign('type', 'warning')->assign('msg', $this->view->translate('Please fill-in the form'));
                $this->view->form = $loginForm;
            }
        } else {

            $this->view->form = $loginForm;
        }
    }

    /**
     * Logout action, redirects to home page
     * 
     * @return void
     */
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
        return;
    }
}
