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

abstract class Theme {
    
    
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
}