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

        if ( !array_key_exists($class, self::$instances) ) {
            self::$instances[$class] = new $class($dir, $url);
        }

        register_activation_hook(__FILE__, array($class, 'activation_hook'));
        return self::$instances[$class];
    }

    public function activation_hook() {
        flush_rewrite_rules();
    }
}
