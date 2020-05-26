<?php

namespace kbwp;
use Timber\Timber as Timber, \TimberMenu;

class Theme extends Extension
{
    protected $_directory;
    protected $_features = [];
    protected $_locale;
    protected $_languageFolder;
    protected $_menus = [];
    protected $_url;

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


    public function addSupport( $feature = '', array $args = [], bool $return_obj = true )
    {
        $return = false;
        $errors = [];

        $addSupport = function( $feature = '', array $args = [] )
        {
            if ( !$this->hasSupport( $feature ) )
            {
                $this->_features[$feature]['feature'] = $feature;
                if ( !empty($args) )
                {
                    $this->_features[$feature]['args'] = $args;
                }
                return true;
            }
            return false;
        };

        if ( !is_array( $feature ))
        {
            $return = $addSupport( $feature, $args );
        }
        else
        {
            foreach( $feature as $feature )
            {
                $errors[] = $addSupport( $feature );
            }
            $return = ( !$this->hasErrors( $errors ) ? true : false );
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function hasSupport( $feature = '' )
    {
        $return = false;

        if ( !is_array( $feature ))
        {
            $return = array_key_exists( $feature, $this->_features );
        }
        return $return;
    }


    public static function load()
    {
        $class = get_called_class();
        $theme = $class::init();
        $theme->loadNavigationMenus();
        $theme->loadSupportedFeatures();
        parent::load();
    }


    private function loadNavigationMenus()
    {
        $class = get_called_class();
        $theme = $class::init();
        $theme->addAction('after_setup_theme', [$theme, 'registerNavigationMenus']);
    }


    private function loadSupportedFeatures()
    {
        foreach( $this->_features as $feature )
        {
            if ( array_key_exists( 'arg', $feature ))
            {
                add_theme_support( $feature['feature'], $feature['args']);
            } 
            else
            {
                add_theme_support( $feature['feature']);
            }
        }
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


    public function url()
    {
        return $this->_url;
    }
}
