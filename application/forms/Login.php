<?php
/**
 * Login form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_Login extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form');
        $this->setDecorators(array('FormElements', 'Form'));

        $this->addElement(
                'text', 'username', array(
            'label' => _('Username:'),
            'required' => true,
            'filters' => array('StringTrim'),
        ));

        $this->addElement('password', 'password', array(
            'label' => _('Password:'),
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Login'),
            'class' => 'ym-button'
        ));

        $textdecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-text',
                    'tag' => 'div')
            ),
            array('Label', array('tag' => 'label')));

        $buttondecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-button',
                    'tag' => 'div')
                ));


        $this->getElement('username')->setDecorators($textdecorator);
        $this->getElement('password')->setDecorators($textdecorator);
        $this->getElement('submit')->setDecorators($buttondecorator);
    }

}

