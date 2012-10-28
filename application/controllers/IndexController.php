<?php
/**
 * Initial controller class
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class IndexController extends Zend_Controller_Action {

    private $_config = null;

    /**
     * Get the configuration parameters
     * 
     */
    public function init() {
        $this->_config = Zend_Registry::get('config');
    }

    /**
     * Determine what to display on the homepage
     * based on the configuration
     * 
     * @return void
     */
    public function indexAction() {
        if ($this->_config->homepage == 'feeds') {
            $this->_forward ('feeds');
            return;
        } else { 
            $this->_forward ('list', 'movies');
            return;
        }
    }

    /**
     * Prepare the sidebar
     * 
     */
    public function sidebarAction() {
        $movies = new Application_Model_Movies();
        $this->view->moviecount = $movies->getMovieCount();
        $this->view->lastmovie = $movies->getLastMovie() ? $movies->getLastMovie() : false;
        $this->view->bestmovie = $movies->getBestMovie() ? $movies->getBestMovie() : false;
    }

    /**
     * Import feed from congigured locations
     * 
     */
    public function feedsAction() {
        if (!empty($this->_config->feed1)) {
            $feed1 = Zend_Feed_Reader::import($this->_config->feed1);
            $this->view->feed1 = $feed1;
        } else {
            $this->view->feed1 = false;
        }

        if (!empty($this->_config->feed2)) {
            $feed2 = Zend_Feed_Reader::import($this->_config->feed2);
            $this->view->feed2 = $feed2;
        } else {
            $this->view->feed2 = false;
        }
    }
}

