<?php
/*
 * @file:           PostType.php
 * @description:    Class to create custom post types easily.
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

class PostType {
    
    private $type;
    private $singular;
    private $plural;
    private $args;
    private $labels;
    
    /*
     * Creates object for new post type
     * @since:  1.0.0
     *
     * @param   string  $type           Name of the post type
     * @param   string  $singular       Singular name for post type
     * @param   string  $plural         Plural name for post type
     * @param   array   $args       
     * */
    public function __construct( $singular, $plural, $type = '', $args = array(), $labels = array() ) {
        
        if ( !$type ) {
            $this->type = Base::uglify( $singular );
        } else {
            $this->type = $type;
        }
        
        $this->singular = $singular;
        $this->plural = $plural;
        
        $this->labels = array_merge(
            array(
                'name'               => _x( $this->plural, 'post type general name', 'kbwp' ),
                'singular_name'      => _x( $this->singular, 'post type singular name', 'kbwp' ),
                'menu_name'          => _x( $this->plural, 'admin menu', 'kbwp' ),
                'name_admin_bar'     => _x( $this->singular, 'add new on admin bar', 'kbwp' ),
                'add_new'            => _x( 'Lisää uusi', $this->type, 'kbwp' ),
                'add_new_item'       => __( 'Lisää uusi '. strtolower( $this->singular ), 'kbwp' ),
                'new_item'           => __( 'Uusi ' . $this->singular, 'kbwp' ),
                'edit_item'          => __( 'Muokkaa kohdetta', 'kbwp' ),
                'view_item'          => __( 'Näytä kohde', 'kbwp' ),
                'all_items'          => __( 'Kaikki ' . strtolower( $this->plural ), 'kbwp' ),
                'search_items'       => __( 'Etsi kohteista "' . strtolower( $this->plural ) . '"', 'kbwp' ),
                'parent_item_colon'  => __( 'Vanhempi:', 'kbwp' ),
                'not_found'          => __( 'Kohteita ei löytynyt', 'kbwp' ),
                'not_found_in_trash' => __( 'Roskakori on tyhjä.', 'kbwp' )
            ), $labels
        );

        $this->args = array_merge(
            array(
                'labels'             => $this->labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => sanitize_title( $this->plural ) ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' )
            ), $args
        );
        
        add_action( 'init', array( $this, 'register' ) );
    }
    
    
    /*
     * Add taxonomy for post type
     * */
    public function add_taxonomy( $singular, $plural, $object_type = '', $args = array(), $labels = array() ) {
        
        if (!$object_type) {
            $object_type = $this->type;
        }
        
        $taxonomy = new Taxonomy( $singular, $plural, $object_type, $args, $labels );
    }
    
    
    /*
     * Register post type
     * */
    public function register() {
        register_post_type( $this->type, $this->args );
    }
}