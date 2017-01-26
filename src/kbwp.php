<?php
/*
 * @file:           kbwp.php
 * @description:    Collection of basic methods for other KBWP classes to use
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

class kbwp {
    
    private function __construct() {}
    
    public static function uglify( $string ) {
        $string = strtolower( $string );
        $string = str_replace( array(' ','ä','å','ö'), array( '_','a','a','o' ), $string );
        return $string;
    }
}