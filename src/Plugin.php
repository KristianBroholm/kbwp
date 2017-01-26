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
    
    protected function __construct() {
        flush_rewrite_rules();
    }
    
}