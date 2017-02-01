<?php
/*
 * @file:           Plugin.php
 * @description:    Collection of basic methods for Plugin to use
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

abstract class Plugin {

    private static $instance = null;
    
    public static function init($dir, $url) {
        
        register_activation_hook(__FILE__, array($this, 'activation_hook'));
        
        if ( null == self::$instance ) {
            self::$instance = new self($dir, $url);
        }
        return self::$instance;
    }
    
    protected function activation_hook() {
        flush_rewrite_rules();
    }
}