<?php

namespace kbwp;

class Taxonomy {

    use PostTypeTrait;
    protected $_slug = '';
    protected $_post_types = [];
    protected $_settings = [];

    public function __construct($handle, $post_type = '', $user_labels = array(), $user_settings = array(), $is_public = true)
    {
      $this->_slug = kbwp::slugify($handle);

      $default_settings = [
        'labels' => $user_labels
      ];

      $this->_settings = array_merge($default_settings, $user_settings);
      $post_types = (is_array($post_type) ? $this->addPostTypes($post_type) : $this->addPostType($post_type));
    }


    public function hasPostType($post_type)
    {
      $post_type  = ($post_type instanceof PostType ? $post_type->getSlug() : $post_type);
      $return     = (in_array($post_type, $this->_post_types) ? true : false);
      return $return;
    }


    public function debug()
    {
      kbwp::log($this);
    }


    public function addPostType($post_type = '', bool $return_obj = true)
    {
      $return = false;
      if (!is_array($post_type)) {
        if ($post_type instanceof PostType) {
          $return = $this->hasPostType($post_type->getSlug()) ? false : array_push($this->_post_types, $post_type->getSlug());
        } else {
          $return = $this->hasPostType($post_type) ? false : array_push($this->_post_types, $post_type);
        }
        $return = is_array($return) ? true : false;
      }
      $return = ($return_obj ? $this : $return);
      return $return;
    }


    public function addPostTypes(array $post_types, bool $return_obj = true)
    {
      $return = false;
      if (is_array($post_types))
      {
        foreach($post_types as $post_type)
        {
          $this->addPostType($post_type);
        }
        $return = true;
      }
      $return = ($return_obj ? $this : $return);
      return $return;
    }


    public function register()
    {
      add_action('init', [$this, 'init']);
    }


    public function init()
    {
      register_taxonomy($this->_slug, $this->_post_types, $this->_settings);
    }


    public function removePostType($post_type = '', bool $return_obj = true)
    {
      $return = false;
      if (!is_array($post_type)) {
        if ($post_type instanceof PostType)
        {
          $post_type = $post_type->getSlug();
        }
        if ($this->hasPostType($post_type))
        {
          unset($this->_post_types[array_search($post_type, $this->_post_types)]);
          $return = true;
        }
      }
      $return = ($return_obj ? $this : $return);
      return $return;
    }


    public function removePostTypes(array $post_type, bool $return_obj = true)
    {
      $return = false;
      if (is_array($post_type))
      {
        foreach($post_type as $post_type)
        {
          $this->removePostType($post_type, false);
        }
        $return = true;
      }
      $return = ($return_obj ? $this : $return);
      return $return;
    }
}
