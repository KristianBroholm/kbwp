<?php

namespace kbwp;
use kbwp\kbwp as kbwp;

abstract class Plugin extends Extension
{
    protected $_path;
    protected $_url;
    protected $_prefix;

    protected function __construct()
    {
        register_activation_hook( __FILE__, array( $this, 'activation_hook' ));
    }


    public function activation_hook()
    {
        flush_rewrite_rules();
    }


    public function path(string $string = '')
    {
        if (!empty($string)) {
            return $this->_path . '/' . $string;
        }
        return $this->_path;
    }


    public function prefix(string $handle = '', string $separator = '_')
    {
        $string = $this->_prefix . $separator . $handle;
        return kbwp::slugify($string);
    }


    public function url(string $string = '')
    {
        if (!empty($string)) {
            return $this->_url . '/' . $string;
        }
        return $this->_url;
    }
}
