<?php
/**
 * Media types form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_Media extends Zend_Form {

    public function init() {
        
        // set form default settings
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form');
        $this->setDecorators(array('FormElements', 'Form'));
        
        // textdecorator
        $textdecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-text',
                    'tag' => 'div')
            ),
            array('Label', array('tag' => 'label')));
        
        // button decorator
        $buttondecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-button',
                    'tag' => 'div')
            ));
        
       
        
         $this->addElement('text', 'type', array(
            'label' => _('Media type'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));
         
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Save'),
            'class' => 'ym-button'
        ));

        $this->getElement('type')->setDecorators($textdecorator);
        $this->getElement('submit')->setDecorators($buttondecorator);
    }

}

