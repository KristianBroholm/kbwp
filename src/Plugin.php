<?php

namespace kbwp;
use kbwp\kbwp as kbwp;

abstract class Plugin extends Extension
{
    public $_path;
    protected $prefix;

    protected function __construct()
    {
        register_activation_hook( __FILE__, array( $this, 'activation_hook' ));
    }

    public function activation_hook()
    {
        flush_rewrite_rules();
    }

    public function prefix(string $handle = '', string $separator = '_')
    {
        $string = $this->prefix . $separator . $handle;
        return kbwp::slugify($string);
    }
}
