<?php

namespace kbwp;

// Base class for Themes and Plugins
abstract class Extension
{

  private function actionEnqueueScripts($function)
  {
    add_action('wp_enqueue_scripts', function() use ($function){
      $function();
    });
  }

  public function addStyle($handle,  $src = '',  $deps = array(), $ver = false, $media = 'all')
  {
    $this->actionEnqueueScripts(function() use ($handle, $src, $deps, $ver, $media){
      wp_enqueue_style($handle,  $src,  $deps, $ver, $media);
    });
    return $this;
  }


  public function addScript($handle,  $src = '',  $deps = array(), $ver = false, $in_footer = false)
  {
    $this->actionEnqueueScripts(function() use ($handle, $src, $deps, $ver, $in_footer){
      wp_enqueue_script($handle,  $src,  $deps, $ver, $in_footer);
    });
    return $this;
  }


  public function addTypekit( $id = '' )
  {
    wp_enqueue_script( 'typekit-' . $id, 'https://use.typekit.net/' . $id . '.js' );

    if ( !has_action( 'wp_head', 'typekit_init' ) ) {
      add_action( 'wp_head', array( $this, 'typekit_init' ) );
    }
  }


  public function removeStyle($handle)
  {
    $this->actionEnqueueScripts(function() use ($handle){
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    });
    return $this;
  }


  public function removeScript($handle)
  {
    $this->actionEnqueueScripts(function() use ($handle){
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    });
    return $this;
  }


  public function typekit_init()
  {
    echo "<script>\n//Init typekit\ntry{Typekit.load({ async: true });}catch(e){}\n</script>";
  }
}
