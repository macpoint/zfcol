<?php

/*
 * Interface for the movie parser
 * 
 * @package zfcol
 * @category library/Zfcol/Parser
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
interface Zfcol_Parser_Parser {
    
    public function getParserName();
    
    public function getParserAuthor();

    public function searchMovie($string);

    public function getMovieName();

    public function getMovieDescription($lang = true);

    public function getMoviePoster();

    public function getMovieGenre();

    public function getMovieOrigin();

    public function getMovieDirector();

    public function getMovieStarring();

    public function getMovieRating();
    
    public function getMovieTrailer();
}