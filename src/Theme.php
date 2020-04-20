<?php

namespace kbwp;

abstract class Theme extends Extension
{

  public function __construct() {

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
  }


  private function actionAfterSetup($function)
  {
    add_action('after_setup_theme', function() use ($function) {
      $function();
    });
    return $this;
  }


  public function addImageSize($name, int $width, int $height, $crop = false)
  {
    add_image_size($name, $width, $height, $crop);
    return $this;
  }


  public function addNavigations($locations = array())
  {
      $this->actionAfterSetup(function() use ($locations){

        register_nav_menus($locations);
      });
      return $this;
  }


  public function addSupport($feature, $options = array())
  {

    $this->actionAfterSetup(function() use ($feature, $options) {

      if ($options) {
        add_theme_support($feature, $options);
      } else {
        add_theme_support($feature);
      }
    });
    return $this;
  }
}
