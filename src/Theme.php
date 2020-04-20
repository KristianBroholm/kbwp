<?php

namespace kbwp;
use Timber\Timber as Timber, \TimberMenu;

class Theme extends Extension
{
  public $_locale;
  public $_menus;
  public $_url;
  public $_directory;
  public $_languageFolder;

  private function actionAfterSetup($function)
  {
    add_action('after_setup_theme', function() use ($function) {
      $function();
    });
    return $this;
  }


  private function loadTextDomain ()
  {
    $this->actionAfterSetup( function() {
      load_theme_textdomain( $this->_locale, $this->_directory . $this->_directory . '/' . $this->_languageFolder );
    });
  }


  public function __construct($locale, $languageFolder = 'lang')
  {
    // Define properties
    $this->_locale          = $locale;
    $this->_menus           = array();
    $this->_url             = get_stylesheet_directory_uri();
    $this->_directory       = get_template_directory();
    $this->_languageFolder  = $languageFolder;

    // Define supported features
    $this->addSupport('title-tag')
         ->addSupport('custom-logo')
         ->addSupport('post-thumbnails')
         ->addSupport('customize-selective-refresh-widgets')
         ->addSupport('html5', [
             'search-form',
             'comment-form',
             'comment-list',
             'gallery',
             'caption'
         ]);

    $this->loadTextDomain();
  }


  public function addImageSize($name, $width = 0, $height = 0, $crop = false)
  {
    $this->actionAfterSetup(function() use ($name, $width, $height, $crop){
      add_image_size($name, $width, $height, $crop);
    });
    return $this;
  }


  public function addNavigation($location, $name)
  {
    $this->actionAfterSetup(function() use ($location, $name) {
      register_nav_menu($location, $name);
    });
    $this->_menus[$location] = $name;
  }


  public function addNavigations($locations = array())
  {
    $this->actionAfterSetup(function() use ($locations)
    {
      register_nav_menus($locations);
    });

    foreach($locations as $location => $name)
    {
      $this->_menus[$location] = $name;
    }

    return $this;
  }


  public function addNavigationsToTimberContext()
  {
    add_filter('timber_context', array($this, 'filter_add_navigations_to_timber_context'));
  }


  public function addSupport($feature, $options = array())
  {
    $this->actionAfterSetup(function() use ($feature, $options)
    {
      if ($options) {
        add_theme_support($feature, $options);
      } else {
        add_theme_support($feature);
      }
    });
    return $this;
  }


  public function filter_add_navigations_to_timber_context($timber_context)
  {
    if (class_exists('\Timber\Menu') && isset($this->_menus))
    {
      foreach ($this->_menus as $location => $name)
      {
          $timber_context['menu'][$location] = new \Timber\Menu( $name );
      }
    }
    return $timber_context;
  }


  public function removeImageSize($name)
  {
    $this->actionAfterSetup(function() use ($name){
      remove_image_size($name);
    });
    return $this;
  }


  public function removeNavigation($location)
  {
    $this->actionAfterSetup(function() use ($location){
      unregister_nav_menu($location);
    });
    unset($this->_menus[$location]);
    return $this;
  }


  public function removeSupport($feature)
  {
    $this->actionAfterSetup(function() use ($feature){
      remove_theme_support($feature);
    });
    return $this;
  }
}
