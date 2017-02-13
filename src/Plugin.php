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

    private static $instances = array();
    
    public static function init($dir, $url) {
        
        $class = get_called_class();
        
        if ( !self::$instances[$class] ) {
            self::$instances[$class] = new $class($dir, $url);
        }
        
        register_activation_hook(__FILE__, array($this, 'activation_hook'));
        
        return self::$instances[$class];
    }
    
    protected function activation_hook() {
        flush_rewrite_rules();
    }
}