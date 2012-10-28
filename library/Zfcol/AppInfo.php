<?php
/**
 * AppInfo support class
 * 
 * @package zfcol
 * @category library/Zfcol
 * @author kamil
 * @version 1.0
 * @license http://opensource.org/licenses/bsd-3-clause new BSD license
 * @copyright (c) 2012, Kamil Kantar
 *
 */
class Zfcol_AppInfo {

    protected $_name;
    protected $_tag;
    protected $_webpage;
    protected $_author;
    protected $_version;
    protected $_email;

    public function __construct($name, $tag, $webpage, $author, $version, $email) {
        $this->setName($name);
        $this->setTag($tag);
        $this->setWebpage($webpage);
        $this->setAuthor($author);
        $this->setVersion($version);
        $this->setEmail($email);
    }

    public function setName($name) {
        $this->_name = trim($name);
    }

    public function setTag($tag) {
        $this->_tag = trim($tag);
    }

    public function setWebpage($webpage) {
        $this->_webpage = trim($webpage);
    }

    public function setAuthor($author) {
        $this->_author = trim($author);
    }

    public function setVersion($version) {
        $this->_version = trim($version);
    }

    public function setEmail($_email) {
        $this->_email = trim($_email);
    }

    public function getName() {
        return $this->_name;
    }

    public function getTag() {
        return $this->_tag;
    }

    public function getWebpage() {
        return $this->_webpage;
    }

    public function getAuthor() {
        return $this->_author;
    }

    public function getVersion() {
        return $this->_version;
    }

    public function getEmail() {
        return $this->_email;
    }

}