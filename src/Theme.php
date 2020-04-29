<?php

namespace kbwp;
use Timber\Timber as Timber, \TimberMenu;

class Theme extends Extension
{
    private $_menus = [];
    public $_url;
    public $_directory;
    public $_locale;
    public $_languageFolder;

    protected function __construct()
    {
        $this->_url             = get_stylesheet_directory_uri();
        $this->_directory       = get_template_directory();
        $this->_languageFolder  = '';
    }


    public function loadTextDomain( $locale = '', $path = 'lang' )
    {
        $this->_locale = $locale;
        $this->_languageFolder = $path;
        load_theme_textdomain( $this->_locale, $this->_languageFolder );
    }


    public function addNavigationMenu( $handle = '', $menu = '' )
    {
        if ( is_array( $handle ))
        {
            foreach( $handle as $handle => $menu )
            {
                $this->_menus[$handle] = [$handle => $menu];
            }
        }
        elseif ( !empty( $handle ))
        {
            $this->_menus[$handle] = [$handle => $menu];
        }
    }


    /*
    public function addNavigationsToTimberContext()
    {
        add_filter('timber_context', array( $this, 'filter_add_navigations_to_timber_context' ));
    }
    */


    public static function load()
    {
        $class = get_called_class();
        $theme = new $class();
        $theme->loadNavigationMenus();
        parent::load();
    }


    private function loadNavigationMenus()
    {
        $class = get_called_class();
        $theme = new $class();
        add_action('after_setup_theme', array( $theme, 'registerNavigationMenus' ));
    }


    public function registerNavigationMenus()
    {
        foreach( $this->_menus as $menu )
        {
            foreach( $menu as $handle => $name )
            {
                register_nav_menu( $handle, $name );
            }
        }
    }
}
