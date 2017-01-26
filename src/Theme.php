<?php
/*
 * @file:           Theme.php
 * @description:    Collection of basic methods for themes to use
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;
use \TimberMenu, \TimberImage;

Abstract class Theme {
    
    private $menus;
    public  $url;
    
    protected static function __construct() {
        
        $this->url = stylesheet_directory_uri();
        add_filter( 'timber_context', array( $this, 'filter_add_custom_header_to_timber_context' ) );
    }
    
    
    /*
     * @author  kristianb
     * @since   1.0.0
     */
    public function filter_add_custom_header_to_timber_context( $timber_context ) {
        
        $header_image = get_header_image();
        $timber_context['header_image'] = new TimberImage( $header_image );  
        
        return $timber_context;
    }
    
    
    /*
     * @author: kristianb
     * @since:  1.0.0
     */
    public function filter_add_navigations_to_timber_context( $timber_context ) {
        
        if ( class_exists( 'TimberMenu' ) && isset( $this->_menus ) ) {
            
            foreach( $this->_menus as $menu_slug => $menu_name ) {
                
                $timber_context[ $menu_slug ] = new TimberMenu( $menu_name );
            }
            
            return $timber_context;
        }
    }
    
    
    /* Enqueues scripts from Typekit and adds Typekits method into head-section
     * @author  kristianb
     * @since   1.0.0
     * @param   $id   string  Typekits ID for fontkit
    */
    public function wp_enqueue_typekit( $id ) {
            
        wp_enqueue_script( 'typekit-' . $id, 'https://use.typekit.net/' . $id . '.js' );
        
        if ( !has_action( 'wp_head', 'typekit_init' ) ) {
            add_action( 'wp_head', array( $this, 'typekit_init' ) );
        }
    }
    
    
    /* Inits Typekit
     * @author  kristianb
     * @since   1.0.0
     * */
    public function typekit_init() {

        echo "<script>\n//Init typekit\ntry{Typekit.load({ async: true });}catch(e){}\n</script>";
    }
    

    /**
     * @author: kristianb
     * @since:  1.0.0
     * @param   $stylesheet_url string  Stylesheets URL
     */
    public function add_editor_style( $stylesheet_url ) {
        
        if ( is_admin() ) {
            global $editor_styles;
            $editor_styles = (array) $editor_styles;
            $stylesheet    = (array) $stylesheet;
            $editor_styles = array_merge( $editor_styles, $stylesheet );
        }
    }
    
    
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
    
    
    /**
     * @author: kristianb
     * @since:  0.0.0
     * */
    protected function get_asset( $asset, $manifest = array() ) {
        
        if ( !$manifest) {
            $manifest = $this->_assets;
        }
        
        if ( array_key_exists( $asset, $manifest ) ) {
            return $this->_dist . '/' . $manifest[ $asset ];
        } 

        return $this->_dist . '/' . $asset;
    }
}