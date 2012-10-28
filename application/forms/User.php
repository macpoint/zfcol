<?php
/**
 * User form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_User extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form columnar');
        $this->setDecorators(array('FormElements', 'Form'));
        
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
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-button',
                    'tag' => 'div')
            ));
        
        // multiselect decorator
        $selectdecorator = array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-select',
                    'tag' => 'div')
            ),
            array('Label', array('tag' => 'label')));
        
        $this->addElement(
                'text', 'first_name', array(
            'label' => _('Name:'),
            'required' => true,
            'filters' => array('StringTrim'),
        ));
        
        $this->addElement(
                'text', 'last_name', array(
            'label' => _('Surname:'),
            'required' => true,
            'filters' => array('StringTrim'),
        ));
        
        $this->addElement(
                'text', 'username', array(
            'label' => _('Username:'),
            'required' => true,
            'filters' => array('StringTrim'),
        ));
        
        $this->addElement(
                'password', 'password', array(
            'label' => _('Password:'),
            'required' => false,
            'validators' => array(
                array('identical', false, array('token' => 'verifypassword'))
            )
        ));
        
        $this->addElement('password', 'verifypassword', array(
            'label'      => _('Verify password:'),
            'required'   => false,
            'validators' => array(
                array('identical', false, array('token' => 'password'))
            )
        ));
        
        $roles = array(
            'editor' => 'editor',
            'administrator' => 'administrator'
        );
        
        // add element 'role'
        $this->addElement('select', 'role', array(
            'label' => _('Role: '),
            'multiOptions' => $roles
                )
        );
        
         $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Save'),
            'class' => 'ym-button'
        ));
         
         
         $this->getElement('role')->setDecorators($selectdecorator);
         $this->getElement('first_name')->setDecorators($textdecorator);
         $this->getElement('last_name')->setDecorators($textdecorator);
         $this->getElement('username')->setDecorators($textdecorator);
         $this->getElement('password')->setDecorators($textdecorator);
         $this->getElement('verifypassword')->setDecorators($textdecorator);
         $this->getElement('submit')->setDecorators($buttondecorator); 
    }
}

