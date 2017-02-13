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

class Plugin {

    private static $instance = null;
    
    private function __construct() {
        
        register_activation_hook(__FILE__, array($this, 'activation_hook'));
    }
    

    public static function init($dir, $url) {
        
        if ( null == self::$instance ) {
            $class = get_called_class();
            self::$instance = new $class($dir, $url);
        }
        return self::$instance;
    }
    
    
    protected function activation_hook() {

        flush_rewrite_rules();
    }
}