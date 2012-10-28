<?php

/**
 * App settings form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_Settings extends Zend_Form {

    public function init() {

        $settings = new Application_Model_Settings();

        // get array of all available translation in /languages
        $translations = $settings->getAvailableTranslations();

        // get available movie parsers from configs/parser.xml
        $parsers = $settings->getAvailableParsers();

        $this->addElementPrefixPath(
                'Zfcol_Validate', 'Zfcol/Validate', 'validate');

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
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-button',
                    'tag' => 'div')
                ));

        // multiselect decorator
        $selectdecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-select',
                    'tag' => 'div')
            ),
            array('Label', array('tag' => 'label')));

        // add element 'locale'
        $this->addElement('select', 'locale', array(
            'label' => _('App language: '),
            'multiOptions' => $translations
                )
        );

        // add element 'parser'
        $this->addElement('select', 'parser', array(
            'label' => _('Movie information source: '),
            'multiOptions' => $parsers
                )
        );

        $homepage = array(
            'feeds' => 'Feeds',
            'movies' => 'Movies',
        );

        // add element 'homepage'
        $this->addElement('select', 'homepage', array(
            'label' => _('Homepage: '),
            'multiOptions' => $homepage
                )
        );

        $this->addElement('text', 'feed1', array(
            'label' => _('Feed 1'),
            'required' => false,
            'filters' => array(
                'StringTrim',
            )
        ));

        $this->addElement('text', 'feed2', array(
            'label' => _('Feed 2'),
            'required' => false,
            'filters' => array(
                'StringTrim',
            )
        ));

        $font = array(
            'play' => 'Play',
            'ptsans' => 'PT Sans',
            'ropasans' => 'Ropa Sans'
        );

        // add element 'font'
        $this->addElement('select', 'font', array(
            'label' => _('App font: '),
            'multiOptions' => $font
                )
        );
        
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Save'),
            'class' => 'ym-button'
        ));

        $this->getElement('locale')->setDecorators($selectdecorator);
        $this->getElement('parser')->setDecorators($selectdecorator);
        $this->getElement('homepage')->setDecorators($selectdecorator);
        $this->getElement('font')->setDecorators($selectdecorator);
        $this->getElement('submit')->setDecorators($buttondecorator);
        $this->getElement('feed1')->setDecorators($textdecorator)->addValidator('Url');
        $this->getElement('feed2')->setDecorators($textdecorator)->addValidator('Url');
    }

}

