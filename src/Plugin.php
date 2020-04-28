<?php

namespace kbwp;

abstract class Plugin extends Extension
{
  public $_path;

  protected function __construct()
  {
    register_activation_hook(__FILE__, array($this, 'activation_hook'));
  }


  public function activation_hook()
  {
    flush_rewrite_rules();
  }


  public function createPostType($handle = '', array $user_labels = array(), array $user_settings = array(), bool $is_public = true)
  {
    $post_type = new PostType($handle, $user_labels, $user_settings, $is_public);
    return $post_type;
  }


  public function createTaxonomy($handle, $post_type = '', array $user_labels = array(), array $user_settings = array(), bool $is_public = true)
  {
    $taxonomy = new Taxonomy($handle, $post_type, $user_labels, $user_settings, $is_public);
    return $taxonomy;
  }
}
