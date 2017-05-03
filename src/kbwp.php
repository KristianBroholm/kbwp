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

abstract class kbwp {

    public static function slugify($string) {
        $string = strtolower( $string );
        $string = str_replace( array(' ','ä','å','ö'), array( '_','a','a','o' ), $string );
        return $string;
    }

    public static function pluralize($string) {
        $string = $string . 't';
        return $string;
    }

    public static function partitize($string) {
        $string = strtolower($string);
        $last_character = substr($string, -1);

        switch($last_character) {
            case 'ä':
                $string = $string . 'ä';
            break;
            case 'ö':
                $string = $string . 'tä';
            break;
            default:
                $string = $string . 'a';
            break;
        }

        return $string;
    }

    /* Read assets from manifest JSON created by rev-module
     * @author  kristianb
     * @since   1.0.0
     * @param   $manifest_path  string  Absolute path to manifest.json
     * @return  $assets         array   Returns assets as key-value pairs
     * */
    public static function get_assets_from_manifest( $manifest_path = '' ) {

        $assets = array();

        if ( file_exists($manifest_path) ) {
            $manifest   = file_get_contents($manifest_path, true);
            $assets     = json_decode($manifest, true);
        }

        return $assets;
    }


    /* Get asset by reading a key from the manifest file
     * @author: kristianb
     * @since:  0.0.0
     * */
    public static function get_asset($asset, $manifest = array()) {

        if ( array_key_exists( $asset, $manifest ) ) {
            return $manifest[ $asset ];
        }

        return $asset;
    }
}
