<?php

/**
 * Class to get movie information from
 * themoviedb.org
 * 
 * @package zfcol
 * @category library/Zfcol/Parser
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Parser_Tmdb implements Zfcol_Parser_Parser {

    private $_parser;
    private $_movie_key;
    private $_api_key;
    private $_api;
    private $_movie_info = false;
    private $_poster_prefix;
    private $_youtube_prefix;
    private $_default_poster;
    private $_lang;

    /**
     * Default language, may be overriden in parser config
     */
    const LANG = 'en';
    
    /**
     * Search also in adults movies?
     */
    const ADULT = 0;
    
    /**
     * Parser name
     */
    const PARSER_NAME = 'themoviedb.org parser 1.0';
    
    /**
     * Parser author
     */
    const PARSER_AUTHOR = 'Kamil Kantar';

    /**
     * Set request timeout to reasonable value
     */
    const TIMEOUT = '30';

    /**
     * Assign basic variables
     * 
     * @param string|null $moviekey the movie unique key 
     */
    public function __construct($moviekey = false) {
        $this->_parser = Zend_Registry::get('movieparser');
        
        // set poster & youtube prefixes
        $this->_poster_prefix = $this->_parser->posterprefix;
        $this->_youtube_prefix = $this->_parser->youtubeprefix;
        
        // set movie info language if provided in config
        $this->_lang = empty($this->_parser->lang) ? self::LANG : $this->_parser->lang;
        
        // set api key & api from config
        $this->_api_key = $this->_parser->key;
        $this->_api = $this->_parser->url;
        
        // default poster image if not provided by the parser
        $this->_default_poster = 'images/covers/default_poster.jpg';

        // set movie key to false if we intend to search for movie
        $this->_movie_key = $moviekey ? $moviekey : false;
    }
    
    /**
     * Get parser name
     * 
     * @return string parser name
     */
    public function getParserName() {
        return self::PARSER_NAME;
    }
    
    /**
     * Get parser author
     * 
     * @return string parser author
     */
    public function getParserAuthor() {
        return self::PARSER_AUTHOR;
    }

    /**
     * Searches for movie in the DB
     * Returns array in this format:
     * themoviedb_movie_id  => movie_title
     *                  78  => Blade Runner
     * 
     * @param string $string search string
     * @return array search results
     */
    public function searchMovie($string) {
        $params = array(
            'language' => $this->_lang,
            'query' => $string,
            'page' => '1',
            'include_adult' => self::ADULT
        );

        $what = 'search/movie';
        $results = $this->_getTmdb($what, $params);
        $i = 0;
        $movie = false;
        foreach ($results['results'] as $result) {
            if ($result['release_date'] != '') {
                $date = explode('-', $result['release_date']);
                $d = ' (' . $date[0] . ')';
            } else {
                $d = '';
            }
            $movie[$i]['url'] = $result['id'];
            $movie[$i]['name'] = $result['title'] . $d;
            $i++;
        }

        return is_array($movie) ? $movie : false;
    }

    /**
     * Returns movie name
     * 
     * @return string movie name
     */
    public function getMovieName() {
        $this->_checkMovieKey();
        $params = array(
            'language' => $this->_lang
        );
        
        $what = 'movie/' . $this->_movie_key;
        $this->_movie_info = $this->_getTmdb($what, $params);
        
        return $this->_movie_info['title'];
    }

    /**
     * Returns movie overview
     * Movie overview might not be available
     * in the target language if it is not English.
     * If we get in this, we return English version 
     * instead of FALSE 
     * 
     * @param string $lang overview language
     * @return string overview
     */
    public function getMovieDescription($lang = false) {
        $this->_checkMovieKey();
        $lang = (!$lang) ? $this->_lang : $lang;
        $params = array(
            'language' => $lang
        );
    
        $what = 'movie/' . $this->_movie_key;
        $this->_movie_info = $this->_getTmdb($what, $params);
        
        // Check movie overview exist
        if (strlen($this->_movie_info['overview']) < 1) {
            
            // if not, get it in English
            $this->getMovieDescription($lang = 'en');
        }

        return $this->_movie_info['overview'];
    }

    /**
     * Returns URL of the movie poster
     * 
     * @return string url of the poster
     */
    public function getMoviePoster() {
        $this->_checkMovieKey();
        $params = array(
            'language' => 'en'
        );
        
        $what = 'movie/' . $this->_movie_key . '/images';
        $this->_movie_info = $this->_getTmdb($what, $params);
        $posters = $this->_movie_info['posters'];

        return (array_key_exists('0', $posters)) ? $this->_poster_prefix . $posters[0]['file_path'] : $this->_default_poster;
    }

    /**
     * Returns movie genre in this format:
     * genre 1, genre 2, ...
     * 
     * @return string genre(s)
     */
    public function getMovieGenre() {
        $this->_checkMovieKey();
        $params = array(
            'language' => $this->_lang
        );
        
        $what = 'movie/' . $this->_movie_key;
        $this->_movie_info = $this->_getTmdb($what, $params);
        $genres = '';
        $gcount = 1;
        foreach ($this->_movie_info['genres'] as $genre) {
            $genres .= $genre['name'] . ', ';
            $gcount++;
            
            // we limit genres for 3 items
            if ($gcount > 3) break;
        }
        
        return substr($genres, 0, -2);
    }

    /**
     * Return movie origin in this format:
     * Country 1, Country 2, ... , year, runtime min
     * ie: USA, Hong Kong, 1982, 117 min
     * 
     * @return string movie origin
     */
    public function getMovieOrigin() {
        $this->_checkMovieKey();
        $params = array(
            'language' => 'en'
        );
        
        $what = 'movie/' . $this->_movie_key;
        $this->_movie_info = $this->_getTmdb($what, $params);
        $origins = '';
        foreach ($this->_movie_info['production_countries'] as $origin) {
            $origins .= $origin['name'] . ', ';
        }

        return $origins .= $this->_movie_info['runtime'] . ' min';
    }

    /**
     * Returns movie directors(s)
     * 
     * @return string movie director(s)
     */
    public function getMovieDirector() {
        $this->_checkMovieKey();
        $params = array(
            'language' => $this->_lang
        );

        $what = 'movie/' . $this->_movie_key . '/casts';
        $this->_movie_info = $this->_getTmdb($what, $params);
        $director = '';
        foreach ($this->_movie_info['crew'] as $crew) {
            if ($crew['job'] == 'Director')
                $director .= $crew['name'] . ', ';
        }

        return substr($director, 0, -2);
    }

    /**
     * Returns movie cast (comma delimited)
     * 
     * @return string movie cast
     */
    public function getMovieStarring() {
        $this->_checkMovieKey();
        $params = array(
            'language' => $this->_lang
        );

        $what = 'movie/' . $this->_movie_key . '/casts';
        $this->_movie_info = $this->_getTmdb($what, $params);
        $starring = '';
        foreach ($this->_movie_info['cast'] as $cast) {
            $starring .= $cast['name'] . ', ';
        }

        return substr($starring, 0, -2);
    }

    /**
     * Returns movie rating (percent)
     * 
     * @return int movie rating
     */
    public function getMovieRating() {
        $this->_checkMovieKey();
        $params = array(
            'language' => $this->_lang
        );
        $what = 'movie/' . $this->_movie_key;
        $this->_movie_info = $this->_getTmdb($what, $params);

        return $this->_movie_info['vote_average'] * 10;
    }

    /**
     * Returns movie trailer as youtube id
     * 
     * @return string trailer url (embed)
     */
    public function getMovieTrailer() {
        $this->_checkMovieKey();
        $params = array(
            'language' => 'en'
        );
        $what = 'movie/' . $this->_movie_key . '/trailers';
        $this->_movie_info = $this->_getTmdb($what, $params);
        $youtube = $this->_movie_info['youtube'];
        
        return (array_key_exists('0', $youtube)) ? $this->_youtube_prefix . $youtube[0]['source'] : '';
    }

    /**
     * Check Movie key is set
     * 
     * @return bool
     */
    private function _checkMovieKey() {
        if ($this->_movie_key)
            return true;
        else
            throw new Zend_Exception('Movie key is not set!');
    }

    /**
     * Performs themoviedb.org API query
     * 
     * @param string $what what to search in the API
     * @param array $params query parameters
     * @return array query response
     * @throws Zend_Exception
     * @uses Zend_Http_Client 
     */
    private function _getTmdb($what, $params = false) {

        $client = new Zend_Http_Client();
        $uri = $this->_api . $what . '?api_key=' . $this->_api_key;

        $client->setUri($uri);
        $client->setHeaders('Accept', 'application/json');
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout' => self::TIMEOUT,
            'useragent' => Zend_Registry::get('AppInfo')->getTag(),
        ));

        if ($params)
            $client->setParameterGet($params);

        $response = $client->request();
        if ($response->isSuccessful())
            return json_decode($response->getBody(), true);
        else
            throw new Zend_Exception('Request failed');
    }

}