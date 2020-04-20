<?php

namespace kbwp;

abstract class Plugin extends Extension
{

  public function __construct()
  {
    register_activation_hook(__FILE__, array($class, 'activation_hook'));
  }

  public function activation_hook() {
    flush_rewrite_rules();
  }
}
