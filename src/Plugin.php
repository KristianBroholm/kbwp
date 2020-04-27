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


  public function addPostType($handle = '', $user_labels = array(), $user_settings = array(), $is_public = true)
  {
    $post_type = new PostType($handle);
    return $post_type;
  }
}
