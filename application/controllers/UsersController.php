<?php
/**
 * Users Controller
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class UsersController extends Zend_Controller_Action {

    private $_sesskey = null;
    private $_userid = null;

    /**
     * Initialize Session namespace & user id
     * 
     */
    public function init() {
        // get session key from session
        $session = new Zend_Session_Namespace('zfcol');
        $this->_sesskey = $session->sesskey;

        // get current user id
        $auth = Zend_Auth::getInstance();
        $this->_userid = $auth->getIdentity()->id;
    }

    /**
     * Redirect to 'show' action
     * 
     * @return void
     */
    public function indexAction() {
        $this->_forward('show');
        return;
    }

    /**
     * Fetch all users using the model
     *
     */
    public function showAction() {
        $users = new Application_Model_User();
        $list = $users->fetchAll();
        $this->view->list = $list;
    }

    /**
     * Edit the user (if POST was triggered)
     * Display the edit user form
     * 
     * @see Zend_Form_User
     * @throws Zend_Db_Exception
     */
    public function editAction() {
        // get id of the user
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        // load model, get the user
        $users = new Application_Model_User;
        $user = $users->find($id)->current();

        if (empty($user))

        // user does not exist
            throw new Zend_Db_Exception('ID is invalid');

        // load the user form
        $form = new Application_Form_User();

        // settings form was submitted
        if ($this->getRequest()->isPost()) {

            $formdata = $this->getRequest()->getPost();
            if ($form->isValid($formdata)) {

                $formdata['password'] = (empty($formdata['password'])) ? $user->password : sha1($formdata['password']);
                $user->setFromArray($formdata);
                $user->save();
                $this->view->assign('type', 'success')->assign('msg', $this->view->translate('User saved'));
                $list = $users->fetchAll();
                $this->view->assign('list', $list);
                $this->_helper->viewRenderer('show');
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot save user!'));
                $list = $users->fetchAll();
                $this->view->assign('list', $list);
                $this->_helper->viewRenderer('show');
            }

            // form was not submitted, display current data 
        } else {
            $form->populate($user->toArray());

            // disable role change to user with id = 1
            if ($id == 1)
                $form->getElement('role')->setAttrib('disabled', 'disabled');
            $this->view->form = $form;
        }
    }

    /**
     * Delete the user
     * 
     * @return void
     * @throws Zend_Exception
     * @throws Zend_Db_Exception
     */
    public function deleteAction() {
        // check received key is correct
        if ($this->_sesskey != $this->getRequest()->getParam('key'))
            throw new Zend_Exception('Control key is invalid');

        // get id of the user
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        // check we are not deleteing current user
        if ($id == $this->_userid)
            throw new Zend_Exception('This user cannot be deleted');

        if ($id == 1)
            throw new Zend_Exception('This user cannot be deleted');

        // check user with this id exists
        $user = new Application_Model_User();

        $data = $user->find($id)->current();
        if (empty($data))
            throw new Zend_Db_Exception('ID is invalid');

        // prepare the query
        $where = $user->getAdapter()->quoteInto('id = ?', $id);

        // delete the user & notify the user
        if ($user->delete($where)) {
            $this->view->assign('type', 'success')->assign('msg', $this->view->translate('User removed'));
            $this->_forward('show', 'users', 'default');
            return;
        } else {
            $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot remove user!'));
            $this->_forward('show', 'users', 'default');
            return;
        }
    }

    /**
     * Add new user
     * 
     * @return void
     * @throws Zend_Exception
     */
    public function addAction() {
        $form = new Application_Form_User();

        $form->getElement('password')->setRequired(true);
        $form->getElement('verifypassword')->setRequired(true);

        // save the user
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $user = new Application_Model_User();

                // check user name does not exist
                if (!$user->userExists($this->getRequest()->getPost('username'))) {

                    $newuser = $form->getValues();

                    // hash the password
                    $newuser['password'] = sha1($newuser['password']);

                    // remove the verify password field
                    unset($newuser['verifypassword']);

                    if (!$newid = $user->insert($newuser)) {
                        throw new Zend_Exception('User cannot be saved');
                    } else {
                        $this->view->assign('type', 'success')->assign('msg', $this->view->translate('User was added'));
                        $this->_forward('show', 'users', 'default');
                        return;
                    }
                } else {
                    // user exists
                    $this->view->assign('type', 'error')->assign('msg', $this->view->translate('This username already exists'));
                    $this->view->form = $form;
                }
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Please fill-in the form'));
                $this->view->form = $form;
            }
        } else {

            // form not submitted, display it
            $this->view->form = $form;
        }
    }

}