<?php
/*
 * @file:           muPlugin.php
 * @description:    Collection of basic methods for muPlugin to use
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

abstract class Plugin {
    
    protected $dist;
    
    /* Read assets from manifest JSON created by rev-module
     * @author: kristianb
     * @since:  1.0.0
     * @param   $manifest_path  string  Absolute path to manifest.json
     * @return  $assets         array   Returns assets as key-value pairs
     * */
    protected function get_assets_from_manifest( $manifest_path = '' ) {
        
        $assets = false;
        
        if ( file_exists( $manifest_path ) ) {
            $manifest   = file_get_contents($manifest_path, true);
            $assets     = json_decode($manifest);
        } 
        
        return $assets;
    }
    
    
    /* Get asset by reading a key from the manifest file
     * @author: kristianb
     * @since:  0.0.0
     * */
    protected function get_asset( $asset, $manifest = array() ) {
        
        if ( array_key_exists( $asset, $manifest ) ) {
            return $this->_dist . '/' . $manifest[ $asset ];
        } 

        return $this->dist . '/' . $asset;
    }
}