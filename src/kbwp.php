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

    public static function has_scands($string) {
        $matches = preg_match('/^[a-zA-ZäöåÄÖÅ]+$/', $string);

        if ($matches) {
            return true;
        }
        return false;
    }

    public static function slugify($string) {
        $string = mb_strtolower( $string );
        $string = str_replace( array(' ','ä','å','ö'), array( '_','a','a','o' ), $string );
        return $string;
    }

    public static function pluralize($string) {
        $string     = mb_strtolower($string);

        if (mb_substr($string, -2) == 'te') {
            $string = mb_substr($string, 0 , -2) . 'tteet';
        } elseif (mb_substr($string, -2) == 'de') {
            $string = mb_substr($string, 0, -2) . 'teet';
        } elseif (mb_substr($string, -2) == 'ke') {
            $string = mb_substr($string, 0, -2) . 'kkeet';
        } elseif (mb_substr($string, -3) == 'nen') {
            $string = mb_substr($string, 0, -3) . 'set';
        } elseif (mb_substr($string, -2) == 'en') {
            $string = mb_substr($string, 0, -2) . 'et';
        } elseif (mb_substr($string, -1) == 's' ) {
            if (mb_substr($string, -4) == 'rves') {
                $string = mb_substr($string, 0 , -1) . 'et';
            } else {
                $string = mb_substr($string, 0, -1) . 'kset';
            }
        } elseif (mb_substr($string, -3) == 'kko') {
            $string = mb_substr($string, 0, -3) . 'kot';
        } elseif (mb_substr($string, -3) == 'kkö') {
            $string = mb_substr($string, 0, -3) . 'köt';
        } elseif (mb_substr($string, -3) == 'kkä') {
            $string = mb_substr($string, 0, -3) . 'kät';
        } else {
            $string = $string . 't';
        }

        return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
    }

    public static function partitize($string) {
        $string = mb_strtolower($string);

        if (mb_substr($string, -1) == 'ä') {
            $string = $string . 'ä';
        } elseif (mb_substr($string, -1) == 'ö') {
            $string = $string . 'tä';
        } elseif (mb_substr($string, -1) == 'e') {
            if (self::has_scands($string)) {
                $string = $string . 'ttä';
            } else {
                $string = $string . 'tta';
            }
        } elseif (mb_substr($string, -1) == 's') {
            if (mb_substr($string, -3) == 'ves') {
                $string = $string . 'tä';
            } else {
                $string = $string . 'ta';
            }
        } elseif (mb_substr($string, -3) == 'nen') {
            $string = mb_substr($string, 0, -3) . 'sta';
        } else {
            $string = $string . 'a';
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
