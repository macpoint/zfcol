<?php

/**
 * Movie model
 * 
 * @package zfcol
 * @category application/models
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Application_Model_Movies extends Zend_Db_Table_Abstract {

    protected $_name = 'movies';
    protected $_primary = 'id';
    protected $_movie_id;

    /**
     * Get all movie information
     * 
     * @param int $id movie id
     * @return type array movie info
     * @throws Zend_Db_Exception
     */
    public function getMovieInfo($id) {
        $this->_movie_id = Zend_Filter::filterStatic($id, 'Int');
        $data = $this->find($id)->current();
        if (empty($data))
            throw new Zend_Db_Exception('ID is invalid');
        $data = $data->toArray();

        return $data;
    }

    /**
     * Get favorite movies
     * 
     * @return object movie / false if movie not fount
     */
    public function getFavoriteMovies() {
        $select = $this->select()->where('favorite = 1');
        $movie = $this->fetchAll($select);

        return $movie ? $movie : false;
    }

    /**
     * Check movie name exists
     * 
     * @param string $name movie name
     * @return bool
     */
    public function movieExists($name) {
        $validator = new Zend_Validate_Db_RecordExists(array(
                    'table' => $this->_name,
                    'field' => 'name')
        );
        return $validator->isValid($name) ? true : false;
    }

    /**
     * Count the movies
     * 
     * @return int
     */
    public function getMovieCount() {
        $select = $this->select();
        $select->from($this, array('count(*) as count'));
        $rows = $this->fetchAll($select);

        return($rows[0]->count);
    }

    /**
     * Get last inserted movie
     * 
     * @return object|false
     */
    public function getLastMovie() {
        $select = $this->select()->order('createDate desc')->limit(1, 0);
        $movie = $this->fetchRow($select);

        return $movie ? $movie : false;
    }

    /**
     * Get best movie (by rating)
     * 
     * @return object|false
     */
    public function getBestMovie() {
        $select = $this->select()->order('rating desc')->limit(1, 0);
        $movie = $this->fetchRow($select);

        return $movie ? $movie : false;
    }

    /**
     * Save the movie poster and return its filename
     * 
     * @param type $image image URL
     * @param type $moviename name of the movie
     * @return string|boolean
     */
    public function saveMoviePoster($image, $moviename) {

        // start Zend_Http_Client and set image URI
        $client = new Zend_Http_Client();
        $client->setUri($image);

        // send request & receive response
        $result = $client->request('GET');

        // create image from the response body
        $img = imagecreatefromstring($result->getBody());

        // generate image name, strip white spaces, special chars
        $moviename = preg_replace('/[^\00-\255]+/u', '', $moviename);
        $moviename = preg_replace('/\//', '_', $moviename);
        $imagename = preg_replace('/\s+/', '_', $moviename) . rand() . '.jpg';

        // path to save the file
        $filepath = APPLICATION_PATH . '/../public/images/covers/' . $imagename;

        // save the image & destroy the image object
        if (imagejpeg($img, $filepath)) {
            imagedestroy($img);
            return $imagename;
        } else {
            imagedestroy($img);
            return false;
        }
    }

}