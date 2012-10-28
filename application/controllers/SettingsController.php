<?php
/**
 * Settings controller
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class SettingsController extends Zend_Controller_Action {

    private $_sesskey = null;

    /**
     * Get the key from session namespace
     * 
     */
    public function init() {
        $session = new Zend_Session_Namespace('zfcol');
        $this->_sesskey = $session->sesskey;
    }

    /**
     * Read & write main configuration parameters
     * 
     */
    public function indexAction() {
        // get the settings model
        $settings = new Application_Model_Settings();

        // get the settings form
        $form = new Application_Form_Settings();

        // settings form was submitted
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                // get new values & disable firstrun
                $values = $form->getValues();
                $values['firstrun'] = 'false';

                // prepare the config to write
                $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/config.xml', 'production',
                                array(
                                    'skipExtends' => true,
                                    'allowModifications' => true
                        ));

                // populate the new values
                foreach ($values as $key => $value) {
                    $config->$key = $value;
                }

                // save the file
                $writer = new Zend_Config_Writer_Xml(array(
                            'config' => $config,
                            'filename' => APPLICATION_PATH . '/configs/config.xml'
                        ));
                $writer->write();
                $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Settings saved. Please reload the page'));
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Form is not complete'));
                $this->view->form = $form;
            }
        } else {

            // form was not submitted
            // populete it with current values
            $form->populate($settings->getCurrentSettings());
            $this->view->form = $form;
        }
    }

    /**
     * Display movie media types
     * 
     */
    public function mediaAction() {
        $media = new Application_Model_Media;
        $list = $media->fetchAll();
        $this->view->media = $list;
    }

    /**
     * Action to edit movie media types
     * 
     * @throws Zend_Db_Exception
     */
    public function editmediaAction() {
        // get id of the media
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        // load model, get the media type
        $media = new Application_Model_Media();
        $type = $media->find($id)->current();

        if (empty($type))

        // media type does not exist
            throw new Zend_Db_Exception('ID is invalid');

        // load the media form
        $form = new Application_Form_Media;

        // settings form was submitted
        if ($this->getRequest()->isPost()) {

            $formdata = $this->getRequest()->getPost();
            if ($form->isValid($formdata)) {

                $type->setFromArray($formdata);
                $type->save();

                // fetch updated list
                $list = $media->fetchAll();
                $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Settings saved'));
                $this->view->assign('media', $list);
                $this->_helper->viewRenderer('media');
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot save settings!'));
                $this->view->assign('media', $list);
                $this->_helper->viewRenderer('media');
            }

            // form was not submitted, display current data 
        } else {
            $form->populate($type->toArray());
            $this->view->form = $form;
        }
    }

    /**
     * Action to edit movie media type
     * 
     * @return void
     * @throws Zend_Exception
     * @throws Zend_Db_Exception
     */
    public function deletemediaAction() {
        // check received key is correct
        if ($this->_sesskey != $this->getRequest()->getParam('key'))
            throw new Zend_Exception('Control key is invalid');

        // get id of the user
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        // check we are not deleteing current user
        if ($id == 0)
            throw new Zend_Exception('This media type cannot be deleted');

        // check user with this id exists
        $media = new Application_Model_Media();

        $type = $media->find($id)->current();
        if (empty($type))
            throw new Zend_Db_Exception('ID is invalid');

        // prepare the query
        $where = $media->getAdapter()->quoteInto('id = ?', $id);

        // delete the user & notify the user
        if ($media->delete($where)) {
            $list = $media->fetchAll();
            $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Media was removed'));
            $this->view->assign('media', $list);
            $this->_helper->viewRenderer('media');
            return;
        } else {
            $list = $media->fetchAll();
            $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot remove media!'));
            $this->view->assign('media', $list);
            $this->_helper->viewRenderer('media');
            return;
        }
    }

    /**
     * Action to add a movie media type
     * 
     * @throws Zend_Exception
     */
    public function addmediaAction() {
        $form = new Application_Form_Media();

        // save the media type
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $media = new Application_Model_Media();

                if (!$newid = $media->insert($form->getValues())) {
                    throw new Zend_Exception('Cannot save media');
                } else {
                    $list = $media->fetchAll();
                    $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Added new media.'));
                    $this->view->assign('media', $list);
                    $this->_helper->viewRenderer('media');
                }
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Please fill-in the form.'));
                $this->view->form = $form;
            }
        } else {
            // form not submitted, display it
            $this->view->form = $form;
        }
    }
}