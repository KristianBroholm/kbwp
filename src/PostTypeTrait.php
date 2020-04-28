<?php
namespace kbwp;

Trait PostTypeTrait {

  public function addLabel( $key = '', $label = '', bool $return_obj = true )
  {
    $return = false;

    if( !is_array( $key ) && !empty( $key ) && !is_array( $label ) && !empty( $label ))
    {
      $this->_labels[$key]  = $label;
      $return = true;
    }
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function addLabels( array $labels = [], bool $return_obj = true )
  {
    $return = false;
    $errors = [];

    if ( is_array( $labels ))
    {
      foreach( $labels as $key => $label )
      {
        $errors[] = $this->addLabel( $key, $label, false );
      }
      $return = $this->hasErrors( $errors );
    }
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function addSetting( $setting = '', $value = '', $return_obj = true )
  {
    $return = false;

    if ( !is_array( $setting ) && !empty( $setting ) && !is_array( $value ) && !empty( $value ))
    {
      $this->_settings[$setting] = $value;
      $return = true;
    }
    $return( $return_obj ? $this : $true );
  }


  public function addSettings( array $settings = [], $return_obj = true )
  {
    $return = false;
    $errors = [];

    if( is_array( $settings ))
    {
      foreach( $settings as $setting => $value )
      {
        $errors[] = $this->addSetting( $settings, $value, false );
      }
      $return = $this->hasErrors( $errors );
    }
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function debug( bool $all_instances = false )
  {
    if ($all_instances)
    {
      kbwp::log( self::$_instances );
    }
    kbwp::log( $this );
  }


  public function getSlug()
  {
    return $this->_slug;
  }


  public static function getInstance( $slug = '' )
  {
      if ( array_key_exists( $slug, self::$_instances ))
      {
        $class = get_called_class();
        return $class::$_instances[$slug];
      }
      return false;
  }


  public function hasErrors( array $errors = [] )
  {
    $return = in_array( false, $errors );
    return $return;
  }


  public function hasLabel( $label )
  {
    $return = array_key_exists( $label, $this->_labels );
    return $return;
  }


  public function hasSetting( $setting )
  {
    $return = array_key_exists( $settings, $this->_settings );
    return $return;
  }


  public function instanceExists( $slug )
  {
    $class = get_called_class();
    $return = array_key_exists( $slug, $class::$_instances );
    return $return;
  }


  public function isRegistered()
  {
    return $this->_is_registered;
  }


  public function removeLabel( $key = '', bool $return_obj = true )
  {
    $return = false;

    if ( !is_array( $key ) && !empty( $key ) && $this->hasLabel( $key ))
    {
      unset( $this->_labels[$key] );
      $return = true;
    }
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function removeLabels( array $labels = [], bool $return_obj = true )
  {
    $errors = [];

    foreach( $labels as $label )
    {
      $errors[] = $this->removeLabel( $label, false );
    }
    $return = $this->hasErrors( $errors );
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function removeSetting( $setting = '', bool $return_obj = true )
  {
    $return = false;

    if ( !is_array( $setting ) && !empty( $setting ) && $this->hasSetting( $setting ))
    {
      unset( $this->_settings[$setting] );
      $return = true;
    }
    $return = ( $return_obj ? $this : $return );
    return $return;
  }


  public function removeSettings( array $settings = [], bool $return_obj )
  {
    $return = false;
    $errors = [];

    foreach( $settings as $setting => $value )
    {
      $errors[] = $this->addSetting( $setting, $value, false );
    }
    $return = $this->hasError( $errors );
    $return = ( $return_obj ? $this : $return );
    return $return;
  }
}
