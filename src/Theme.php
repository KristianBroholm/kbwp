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
    
    private $_menus;
    
    protected function __construct() {
        
        add_filter( 'timber_context', array( $this, 'filter_add_custom_header_to_timber_context' ) );
    }
    
    
    /* Adds given menus to Timber Context. Can be done only once.
     * @author kristianb
     * @since   1.0.0
     * @param   $menus  array   Array must be associative like 'timber_handle' => 'menu_name'
     * */
    public function add_navigations_to_timber_context( $menus = array( 'main_navigation' => 'primary' ) ) {
        
        $this->_menus = $menus;
        
        if ( !has_filter( 'timber_context', 'filter_add_navitagions_to_timber_context' ) ) {
        
            add_filter( 'timber_context', array( $this, 'filter_add_navigations_to_timber_context' ) );
        }
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
     * @param   $stylesheet string  path to stylesheet
     */
    public function add_stylesheet_to_editor( $stylesheet ) {
        
        if ( is_admin() ) {
        
            global $editor_styles;
            $editor_styles = (array) $editor_styles;
            $stylesheet    = (array) $stylesheet;
            $editor_styles = array_merge( $editor_styles, $stylesheet );
        }
    }
    
    
    /* Returns content of requested metafield
     * @author: kristianb
     * @since:  1.0.0
     * */
    public function get_field( $field ) {
        
        global $wp_query;
        global $post;
        
            
        if ( is_category() || is_tag() || is_tax() ) {
            
            $queried_taxonomy = $wp_query->tax_query->queries[0]['taxonomy'];
            $queried_term = $wp_query->tax_query->queries[0]['terms'][0];
            $term = get_term_by( 'slug', $queried_term, $queried_taxonomy );
            $term_meta = get_option( 'term_meta_' . $term->taxonomy . '_' . $term->term_id );
            
            if ( isset( $term_meta[ '_'. $field ] ) ) {
                $result = $term_meta[ '_'. $field ];
            } else {
                $result = null;
            }
            
        } else {
            
            if ( isset( $post->custom['_'. $field ] ) ) {
                $result = $post->custom['_' . $field ];
            } else {
                $result = null;
            }
        }
        
        return $result;
    }

}