<?php
/*
 * @file:           Taxonomy.php
 * @description:    Class to create custom taxonomies easily.
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

class Taxonomy {
    
    private $taxonomy;
    private $singular;
    private $plural;
    private $object_type;
    private $args;
    private $labels;
    
    /*
     * Creates object for custom taxonomy
     * @since: 1.0.0
     *
     * @param   $taxonomy       string          Name of the taxonomy
     * @param   $object_type    string|array    Object types which taxonomy will be registered for.
     * @param   $args           array           Optional. Additional arguments for taxonomy.
     * */
    public function __construct( $singular, $plural, $object_type, $args = array(), $labels = array() ) {
        
        if ( $singular && $plural && $object_type ) {
        
            $this->taxonomy     = Base::uglify( $plural );
            $this->singular     = $singular;
            $this->plural       = $plural;
            $this->object_type  = $object_type;
            
            $this->labels = array_merge(
                array(
                    'name'                       => _x( $this->plural, 'taxonomy general name', 'kbwp' ),
                    'singular_name'              => _x( $this->singular, 'taxonomy singular name', 'kbwp' ),
                    'search_items'               => __( 'Etsi kohteita', 'kbwp' ),
                    'popular_items'              => __( 'Käytetyimmät kohteet', 'kbwp' ),
                    'all_items'                  => __( 'Kaikki kohteet', 'kbwp' ),
                    'parent_item'                => __( 'Vanhempi:', 'kbwp'),
                    'parent_item_colon'          => __( 'Vanhempi', 'kbwp'),
                    'edit_item'                  => __( 'Muokkaa kohdetta', 'kbwp' ),
                    'update_item'                => __( 'Päivitä kohde', 'kbwp' ),
                    'add_new_item'               => __( 'Lisää uusi ' . strtolower( $this->singular ), 'kbwp' ),
                    'new_item_name'              => __( 'Uuden kohteen nimi', 'kbwp' ),
                    'separate_items_with_commas' => __( 'Erota kohteet pilkulla', 'kbwp' ),
                    'add_or_remove_items'        => __( 'Lisää tai poista kohteita', 'kbwp' ),
                    'choose_from_most_used'      => __( 'Valitse käytetyimpien joukosta', 'kbwp' ),
                    'not_found'                  => __( 'Kohteita ei löytynyt', 'kbwp' ),
                    'menu_name'                  => __( $this->plural, 'kbwp' ),
                ), $labels
            );
            
            $this->args = array_merge(
                array(
                    'labels'    => $this->labels,
                    'rewrite'   => array( 'slug' => sanitize_title( $this->singular ) ),
                ), $args
            );
            
            add_action( 'init', array( $this, 'register' ) );
        }
    }
    
    
    /*
     * Register taxonomy
     * */
    public function register() {
        
        if ( $this->taxonomy && $this->object_type && $this->args ) {
            register_taxonomy( $this->taxonomy, $this->object_type, $this->args );
        }
    }
}