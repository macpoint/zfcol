<?php
/**
 * Search movie form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_SearchMovie extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form');
        $this->setDecorators(array('FormElements', 'Form'));

        $this->addElement('text', 'searchstring', array(
            'label' => _('Search movie:'),
            'required' => true,
            'filters' => array(
                'StringTrim'),
            'validator' => 'NotEmpty'
        ));

        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Search')
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
        
        $this->getElement('searchstring')->setDecorators($textdecorator);
        $this->getElement('submit')->setDecorators($buttondecorator);
    }

}

