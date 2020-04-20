<?php

namespace kbwp;

// Base class for Themes and Plugins
abstract class Extension
{

  public function addStylesheet($handle, $src = '', $deps = array(), $version = false, $in_footer = false)
  {
    wp_enqueue_script($name, $src, $deps, $version, $in_footer);
  }


  public function addScript($handle, $src ='', $deps = array(), $version = false, $in_footer = false)
  {
    wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
  }


  public function addTypekit( $id = '' ) {

      wp_enqueue_script( 'typekit-' . $id, 'https://use.typekit.net/' . $id . '.js' );

      if ( !has_action( 'wp_head', 'typekit_init' ) ) {
          add_action( 'wp_head', array( $this, 'typekit_init' ) );
      }
  }


  public function typekit_init() {

      echo "<script>\n//Init typekit\ntry{Typekit.load({ async: true });}catch(e){}\n</script>";
  }
}
