<?php
/**
 * CSFD (www.csfd.cz) movie parser
 * 
 * The CSFD movie database does not provide 
 * any API to get movie information, thus
 * we parse the search results with 
 * simple html dom
 * 
 * @package zfcol
 * @category library/Zfcol/Parser
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_Parser_Csfd implements Zfcol_Parser_Parser {

    private $_movie_key;
    private $_searchResult = false;
    public  $_searchBase;
    public  $_movieBaseUrl;
    
    const PARSER_NAME = 'CSFD Parser 1.0';
    const PARSER_AUTHOR = 'Kamil Kantar';

    public function __construct($url = false) {
        $parser = Zend_Registry::get('movieparser');
        $this->_searchBase = $parser->searchbase;
        $this->_movieBaseUrl = $parser->baseurl;
        
        // Load PHP Simple HTML DOM Parser (http://simplehtmldom.sourceforge.net)
        Zend_loader::loadFile('simple_html_dom.php', APPLICATION_PATH . "/../data/");
        $this->_movie_key = $url ? $this->setMovieSource($url) : false;
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
     * @return string parser authro
     */
    public function getParserAuthor() {
        return self::PARSER_AUTHOR;
    }

    /**
     * Loads the movie source with simple_html_dom
     * 
     * @param string $url url of the movie
     * @return string 
     */
    public function setMovieSource($url) {
        $moviekey = filter_var($url, FILTER_VALIDATE_URL) ? $url : $this->_movieBaseUrl . $url;
        return file_get_html($moviekey);
    }

    /**
     * Performs movie search
     * 
     * @param string $string search string
     * @return array|false search results
     */
    public function searchMovie($string) {
        $this->_movie_key = $this->setMovieSource($this->_searchBase . str_replace(" ", "+", $string));

        $results = 1;
        foreach ($this->_movie_key->find("div[id=search-films] h3[class=subject]") as $resultName) {
            foreach ($resultName->find('a') as $resultHref) {
                $this->_searchResult [$results] ['name'] = $resultName->plaintext;
                $movieUrl = explode('/', $resultHref->href);
                $this->_searchResult [$results] ['url'] = $movieUrl [2];
            }
            $results++;
        }
        if (count($this->_searchResult) > 0)
            return $this->_searchResult;
        else
            return false;
    }

    /**
     * Returns movie name
     * 
     * @return string|false movie name
     */
    public function getMovieName() {
        $result = $this->_movie_key ? $this->_movie_key->find("h1", 0) : false;
        return $result ? trim($result->plaintext) : false;
    }
    
    /**
     * Returns movie overview
     * 
     * @param string $lang prefered language
     * @return string|false movie overview
     */
    public function getMovieDescription($lang = true) {
        $result = $this->_movie_key ? $this->_movie_key->find("div[data-truncate=570]", 0) : false;
	return $result ? trim($result->plaintext) : false;
    }
    
    /**
     * Returns moive poster
     * 
     * @return string|false movie poster url
     */
    public function getMoviePoster() {
        $result = $this->_movie_key ? $this->_movie_key->find("img[class=film-poster]", 0) : false;
	return $result ? $result->src : false;
    }
    
    /**
     * Returns movie genre(s)
     * 
     * @return string|false movie genre(s)
     */
    public function getMovieGenre() {
        $result = $this->_movie_key ? $this->_movie_key->find("p[class=genre]", 0) : false;
	return $result ? trim($result->plaintext) : false;
    }
    
    /**
     * Returns movie origin
     * 
     * @return string|false movie origin
     */
    public function getMovieOrigin() {
        $result = $this->_movie_key ? $this->_movie_key->find("p[class=origin]", 0) : false;
	return $result ? trim($result->plaintext) : false;
    }
    /**
     * Returns movie director(s)
     * 
     * @return string|false movie director(s)
     */
    public function getMovieDirector() {
        $result = $this->_movie_key ? $this->_movie_key->find("span[data-truncate=60]", 0) : false;
	return $result ? trim($result->plaintext) : false;
    }
    
    /**
     * Returns movie cast
     * 
     * @return string|false movie cast
     */
    public function getMovieStarring() {
        $result = $this->_movie_key ? $this->_movie_key->find("span[data-truncate=340]", 0) : false;
	return $result ? trim($result->plaintext) : false;
    }
    
    /**
     * Returns movie rating
     * 
     * @return int|false movie rating (percent)
     */
    public function getMovieRating() {
        $result = $this->_movie_key ? $this->_movie_key->find("h2.average", 0) : false;
	return $result ? str_replace('%', '', trim($result->plaintext)) : false;
    }
    
    /**
     * Trailers are not supported by this parser
     * 
     * @return string empty
     */
    public function getMovieTrailer() {
        return '';
    }

}