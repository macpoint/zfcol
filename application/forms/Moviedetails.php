<?php
/**
 * Movie form
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_Moviedetails extends Zend_Form {

    private $_mediaType;

    public function init() {
        $date = new Zend_Date;
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form ym-full');
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

        $selectdecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-select',
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

        // movie name
        $this->addElement('text', 'name', array(
            'label' => _('Movie name'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie description
        $this->addElement('textarea', 'description', array(
            'label' => _('Overview'),
            'required' => false,
            'rows' => 10,
            //'filters' => array(
              //  'StringTrim',
            //)
        ));

        // movie poster
        $this->addElement('hidden', 'poster');

        // movie genre
        $this->addElement('text', 'genre', array(
            'label' => _('Genre'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie origin
        $this->addElement('text', 'origin', array(
            'label' => _('Origin'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie director
        $this->addElement('text', 'director', array(
            'label' => _('Director'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie starring
        $this->addElement('textarea', 'starring', array(
            'label' => _('Cast'),
            'required' => true,
            'rows' => 4,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie rating
        $this->addElement('text', 'rating', array(
            'label' => _('Rating'),
            'required' => true,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie trailer
        $this->addElement('text', 'trailer', array(
            'label' => _('Trailer'),
            'required' => false,
            'filters' => array(
                'StringTrim',
            )
        ));

        // movie media
        $this->addElement('select', 'media', array(
            'label' => _('Media'),
            'multiOptions' => $this->getMediaTypes()
                )
        );

        // movie favorite status
        $this->addElement('select', 'favorite', array(
            'label' => _('Add to favorities'),
            'multiOptions' => array(0 => _('No'), 1 => _('Yes'))
                )
        );

        // movie name
        $this->addElement('text', 'ownid', array(
            'label' => _('Custom ID'),
            'required' => true,
            'filters' => array(
                'StringTrim',
                'Int'
            )
        ));

        // movie url
        $this->addElement('hidden', 'url');

        // movie create date
        $this->addElement('hidden', 'createDate', array(
            'value' => $date->get('YYYY-MM-dd HH:mm:ss'),
        ));

        // movie creator
        $this->addElement('hidden', 'creator', array(
            'value' => Zend_Auth::getInstance()->getIdentity()->id
        ));

        // submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => _('Save')
        ));

        // set fields decorators
        $this->getElement('ownid')->setDecorators($textdecorator);
        $this->getElement('name')->setDecorators($textdecorator);
        $this->getElement('description')->setDecorators($textdecorator);
        $this->getElement('genre')->setDecorators($textdecorator);
        $this->getElement('origin')->setDecorators($textdecorator);
        $this->getElement('director')->setDecorators($textdecorator);
        $this->getElement('starring')->setDecorators($textdecorator);
        $this->getElement('rating')->setDecorators($textdecorator);
        $this->getElement('trailer')->setDecorators($textdecorator);
        $this->getElement('media')->setDecorators($selectdecorator);
        $this->getElement('favorite')->setDecorators($selectdecorator);
        $this->getElement('url')->setDecorators(array('ViewHelper'));
        $this->getElement('createDate')->setDecorators(array('ViewHelper'));
        $this->getElement('creator')->setDecorators(array('ViewHelper'));

        // set button decorator
        $this->getElement('submit')->setDecorators($buttondecorator);
    }

    /**
     * Retrieve media types from DB
     * 
     * @return array media types
     */
    public function getMediaTypes() {
        $media = new Application_Model_Media();
        $medialist = $media->fetchAll();

        foreach ($medialist as $type) {
            $this->_mediaType[] = $type->type;
        }

        return $this->_mediaType;
    }

}

