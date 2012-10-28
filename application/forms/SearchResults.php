<?php
/**
 * Media types form
 * This is dynamically generated form 
 * It creates one radio button for each movie
 * that is found via MoviesController::addAction
 * 
 * @package zfcol
 * @category application/forms
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Form_SearchResults extends Zend_Form {

    private $_movie;

    public function init() {
        $this->setMethod('post');
        $this->setAttrib('class', 'ym-form');
        $this->setDecorators(array('FormElements', 'Form'));
    }

    /**
     * addElement radio for each search result
     * 
     * @param array $movies Movies found by the search
     */
    public function setMovies($movies) {
        
        foreach ($movies as $movie) {
            $this->_movie[$movie['url']] = " " . $movie['name'];
        }

        $this->addElement('radio', 'movie', array(
            'label' => _('Movies found:'),
            'multiOptions' => $this->_movie,
        ));
        
        $radiodecorator = array(
            'ViewHelper',
            'Errors',
            array('HtmlTag',
                array(
                    'class' => 'ym-fbox-check',
                    'tag' => 'div')
            ),
            array('Label', array('tag' => 'label')));
        
        $this->getElement('movie')->setDecorators($radiodecorator);
    }

}