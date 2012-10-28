<?php
/**
 * Movies controller
 * 
 * @package zfcol
 * @category application/controllers
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class MoviesController extends Zend_Controller_Action {

    private $_sesskey = null;
    private $_parser = null;

    /**
     * Get the key from session namespace
     * Get the movie parser properties
     */
    public function init() {
        $session = new Zend_Session_Namespace('zfcol');
        $this->_sesskey = $session->sesskey;

        $this->_parser = Zend_Registry::get('movieparser')->class;
    }

    /**
     * Forward to the list action
     * 
     * @return void
     */
    public function indexAction() {
        $this->_forward('list');
        return;
    }

    /**
     * List all movies
     * 
     */
    public function listAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl('js/jquery.dataTables.min.js'));
        $movies = new Application_Model_Movies();
        $list = $movies->fetchAll();
        $this->view->movies = $list;
    }

    /**
     * Show movie details
     * 
     */
    public function showAction() {
        $this->view->inlineScript()->prependFile($this->view->baseUrl('js/showmovie.js'));
        $id = $this->getRequest()->getParam('id');
        $movie = new Application_Model_Movies();
        $moviedata = $movie->getMovieInfo($id);

        // pass the session key to the view
        $this->view->key = $this->_sesskey;

        // pass movie details to the view
        $this->view->movie = $moviedata;
    }

    /**
     * Add new movie
     *
     */
    public function addAction() {
        $this->view->inlineScript()->prependFile($this->view->baseUrl('js/addmovie.js'));
        $searchform = new Application_Form_SearchMovie();
        $manualform = new Application_Form_Moviedetails();

        // start the parser
        $movie = new $this->_parser();
        $this->view->parsername = $movie->getParserName();

        // search was invoked
        if ($this->getRequest()->isPost()) {
            if ($searchform->isValid($_POST)) {

                /*
                 * Get the results & generate form & pass it to view 
                 * Now we need to pass results to a form that creates one radio button for
                 * each result. We use semi-magic method set* in the Application_Form_Moviedetails
                 * 
                 * This is how we can pass all the results to the view: 
                 * $this->view->results = $movie->searchMovie($searchform->getValue('searchstring'));
                 */

                $searchresults = $movie->searchMovie($searchform->getValue('searchstring'));

                // movies found, display radio boxes
                if ($searchresults) {
                    $results = new Application_Form_SearchResults(array('movies' => $searchresults));

                    // no movies found       
                } else {
                    $results = new Application_Form_SearchMovie;
                    $this->view->assign('type', 'warning')->assign('msg', $this->view->translate('No results found'));
                }
                $this->view->searchform = $results;
                $this->_helper->viewRenderer('searchresults');
            } else {
                $this->view->assign('type', 'warning')->assign('msg', $this->view->translate('Please enter movie name'));
                $this->view->searchform = $searchform;
                $this->view->manualform = $manualform;
            }
        } else {
            $this->view->searchform = $searchform;
            $this->view->manualform = $manualform;
        }
    }

    /**
     * Preview parsed movie details
     *
     */
    public function previewAction() {
        
        // reset default layout (we are calling this via jQuery)
        Zend_Layout::resetMvcInstance();

        // get movie URL (incomplete one)
        $url = $this->getRequest()->getParam('url');

        // initialize parser object & date object
        $movie = new $this->_parser($url);
        $date = new Zend_Date;
        $parser = Zend_Registry::get('movieparser');

        // check default poster has correct path
        $serverurl = $this->view->serverUrl();
        $poster = $movie->getMoviePoster();
        $posterurl = Zend_Uri::check($poster) ? $poster : $serverurl . $this->view->baseUrl($poster);

        // create movie details array to populate the form
        $data = array(
            'name' => $movie->getMovieName(),
            'description' => $movie->getMovieDescription(),
            'poster' => $posterurl,
            'genre' => $movie->getMovieGenre(),
            'origin' => $movie->getMovieOrigin(),
            'director' => $movie->getMovieDirector(),
            'starring' => $movie->getMovieStarring(),
            'rating' => $movie->getMovieRating(),
            'trailer' => $movie->getMovieTrailer(),
            'ownid' => '0',
            'url' => $parser->baseurl . $url,
            'createDate' => $date->get('YYYY-MM-dd HH:mm:ss'),
            'creator' => Zend_Auth::getInstance()->getIdentity()->id
        );

        $form = new Application_Form_Moviedetails();
        $form->getElement('ownid')->setLabel($this->view->translate('Custom ID (Leave zero if there is none)'));
        $form->populate($data);

        $this->view->form = $form;
        $this->view->poster = $posterurl;
    }

    /**
     * Save the movie
     *
     * @throws Zend_Exception
     */
    public function saveAction() {
        // save the movie
        if ($this->getRequest()->isPost()) {
            
            $saveform = new Application_Form_Moviedetails;
            if ($saveform->isValid($_POST)) {
                $movie = new Application_Model_Movies;

                // get form data
                $formdata = $saveform->getValues();

                // save movie poster
                $formdata['poster'] = $movie->saveMoviePoster($formdata['poster'], $formdata['name']);

                $exists = $movie->movieExists($this->getRequest()->getPost('name')) ? true : false;

                // save the movie
                if (!$newid = $movie->insert($formdata)) {
                    throw new Zend_Exception('Could not save movie to DB');
                } else {

                    // movie saved, update own id if not entered by user
                    if ($formdata['ownid'] == 0) {
                        $where = $movie->getAdapter()->quoteInto('id = ?', $newid);
                        $update = array('ownid' => $newid);
                        $movie->update(($update), $where);
                    }

                    if (!$exists)
                        $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Movie was saved'));
                    else
                        $this->view->assign('type', 'warning')->assign('msg', $this->view->translate('Movie was saved. Movie with this name already exists'));

                    $this->_forward('show', 'movies', 'default', array('id' => $newid));
                }
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Please fill-in the form'));
                $this->view->saveform = $saveform;
            }
        } else {
            $this->_forward('list');
        }
    }

    /**
     * Edit movie
     * 
     * @throws Zend_Db_Exception
     */
    public function editAction() {
        // get id of the movie
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        $movies = new Application_Model_Movies();
        $movie = $movies->find($id)->current();

        if (empty($movie))
            throw new Zend_Db_Exception('ID is invalid');

        $form = new Application_Form_Moviedetails();

        // edit form was submitted
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $movie->setFromArray($form->getValues());
                $movie->save();

                $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Movie was edited'));
                $this->_forward('show', 'movies', 'default', array('id' => $movie->id));
            } else {
                $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Form is not complete'));
                $form->populate($movie->toArray());
                $this->view->form = $form;
            }
        } else {
            $form->populate($movie->toArray());
            $this->view->form = $form;
        }
    }

    /**
     * Delete movie
     *
     * @throws Zend_Exception
     * @throws Zend_Db_Exception
     */
    public function deleteAction() {
        // check received key is correct
        if ($this->_sesskey != $this->getRequest()->getParam('key'))
            throw new Zend_Exception('Control key is invalid');

        // get id of the movie
        $id = $this->getRequest()->getParam('id');

        // check id is numeric
        $id = Zend_Filter::filterStatic($id, 'Int');

        // check movie with this id exists
        $movie = new Application_Model_Movies();

        $data = $movie->find($id)->current();
        if (empty($data))
            throw new Zend_Db_Exception('ID is invalid');

        // prepare the query
        $where = $movie->getAdapter()->quoteInto('id = ?', $id);

        // get the movie poster name
        $poster = $data->poster;

        // delete the movie & notify the user
        if ($movie->delete($where)) {

            // deletion ok, remove the poster
            unlink(APPLICATION_PATH . '/../public/images/covers/' . $poster);
            $this->view->assign('type', 'success')->assign('msg', $this->view->translate('Movie was removed'));
            $this->view->movies = $movie->fetchAll();
            $this->_forward('list');
            return;
        } else {
            $this->view->assign('type', 'error')->assign('msg', $this->view->translate('Cannot remove movie!'));
            $this->view->movies = $movie->fetchAll();
            $this->_forward('list');
            return;
        }
        
    }

    /**
     * Display movie trailer
     * 
     */
    public function trailerAction() {
        $id = $this->getRequest()->getParam('id');
        $movie = new Application_Model_Movies();
        $this->view->movie = $movie->getMovieInfo($id);
    }

}