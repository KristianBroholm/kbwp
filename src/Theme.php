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
use TimberMenu;

class Theme {
    
    
    /* Enqueues scripts from Typekit and adds Typekits method into head-section
     * @author  kristianb
     * @since   1.0.0
     * @param   $id   string  Typekits ID for fontkit
    */
    public function enqueue_typekit( $id ) {
            
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
    public function enqueue_editor_style( $stylesheet_url ) {
        
        if ( is_admin() ) {
            global $editor_styles;
            $editor_styles = (array) $editor_styles;
            $stylesheet    = (array) $stylesheet;
            $editor_styles = array_merge( $editor_styles, $stylesheet );
        }
    }
    
    
    /**
     * @author  kristianb
     * @since   1.0.0
     * @param   $id
     **/
    public function add_facebook_pixel( $id ) {
        
        add_action( 'wp_head', function( $id ) {
            echo '<!-- Facebook Pixel Code -->';
            echo '<script>';
            echo '!function(f,b,e,v,n,t,s)';
            echo '{if(f.fbq)return;n=f.fbq=function(){n.callMethod?';
            echo 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};';
            echo "if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';";
            echo "n.queue=[];t=b.createElement(e);t.async=!0;";
            echo "t.src=v;s=b.getElementsByTagName(e)[0];";
            echo "s.parentNode.insertBefore(t,s)}(window,document,'script',";
            echo "'https://connect.facebook.net/en_US/fbevents.js');";
            echo "fbq('init', '" . $id . "');";
            echo "fbq('track', 'PageView');";
            echo "</script>";
            echo '<noscript><img height="1" width="1" src="https://www.facebook.com/tr?id=' . $id . '&ev=PageView&noscript=1"/></noscript>';
            echo "<!-- End Facebook Pixel Code -->";
        });
    }
    
    
    /*
     * @author  kristianb
     * @since   1.0.0
     * @param   $menus  assoc_array
     * */
    public function register_navigations($menus) {
         
        foreach($menus as $menu => $name) {
            
            register_nav_menu($menu, $name);
            
            print_r( $menu );
            
            if ( class_exists('Timber') ) {
                add_filter('timber_context', function($menu) {
                    $data['menu_' . $menu ] = new TimberMenu($menu);
                    return $data;
                });
            }
        }
        
    }
}