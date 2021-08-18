<?php

namespace kbwp\Traits;

Trait HasSettings {

    protected $_settings = [];
    
    public function addSetting( string $key, $value )
    {
        $this->_settings[$key] = $value;
        return $this;
    }


    public function addSettings( array $settings = [] )
    {
        foreach( $settings as $key => $value )
        {
            $this->addSetting( $key, $value );
        }
        return $this;
    }


    public function hasSetting( $setting = '' )
    {
        $return = array_key_exists( $settings, $this->_settings );
        return $return;
    }


    public function getSettings() 
    {
        return $this->_settings;
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