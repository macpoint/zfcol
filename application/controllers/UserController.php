<?php
/**
 * Display & edit user's own settings
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class UserController extends Zend_Controller_Action {

    public function init() {}

    /**
     * Display user's settings.
     * Edit user's settings if request is POST
     * 
     */
    public function indexAction() {
        $form = new Application_Form_User();
        $form->removeElement('role');

        $users = new Application_Model_User();
        $id = Zend_Auth::getInstance()->getIdentity()->id;
        $user = $users->find($id)->current();

        // save the user
        if ($this->getRequest()->isPost()) {
            $formdata = $this->getRequest()->getPost();
            if ($form->isValid($formdata)) {

                // hash the password if provided
                $formdata['password'] = (empty($formdata['password'])) ? $user->password : sha1($formdata['password']);
                $user->setFromArray($formdata);
                $user->save();
                $this->view->assign('type', 'success')->assign('msg', $this->view->translate('User saved'));
                $form->populate($user->toArray());
                $this->view->assign('form', $form);
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot save user!'));
                $form->populate($user->toArray());
                $this->view->assign('form', $form);
            }
        } else {
            $form->populate($user->toArray());
            $this->view->form = $form;
        }
    }
}