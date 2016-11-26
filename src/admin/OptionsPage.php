<?php
/*
 * @file:           kbwp.php
 * @description:    Collection of basic methods for other KBWP classes to use
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp\admin;
use kbwp\kbwp;

class OptionsPage {
    
    public $_page_title,
    $_menu_name,
    $_page_slug,
    $_callback;
    
    
    public function __construct( string $page_title, string $menu_name, string $page_slug, $callback = array() ) {
        
        $this->_page_title = $page_title;
        $this->_menu_name = $menu_name;
        $this->_page_slug = kbwp::uglify( $page_slug );
        
        if ( $callback ) {
            
            $this->_callback = $callback;
        
        } else {
        
            $this->_callback = array(
                $this,
                'callback_render_page_content'
            );
        }
        
		add_action( 'admin_menu', array( $this, 'action_add_page_to_admin_menu' ) );
	}

    
	public function action_add_page_to_admin_menu() {
		add_options_page(
			$this->_page_title,
            $this->_menu_name,
			'manage_options',
            $this->_page_slug,
            $this->_callback
		);
	}

    
	public function  callback_render_page_content() {
		echo '<div class="wrap">';
        
        echo '</div>';
	}
    
}