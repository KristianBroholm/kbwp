<?php

namespace kbwp\Traits;

Trait HasLabels {

    protected $_labels = [];

    public function addLabel( string $key, string $label )
    {
        $this->_labels[$key]  = $label;
        return $this;
    }


    public function addLabels( array $labels )
    {
        foreach( $labels as $key => $label )
        {
            $this->addLabel( $key, $label, false );
        }
        return $this;
    }


    public function getLabel( string $key )
    {
        $return = false;

        if ( array_key_exists( $key, $this->_labels )) {
            $return = $this->_labels[$key];
        }
        return $return;
    }


    public function getLabels() {
        return $this->_labels;
    }


    public function hasLabel( $label = '' )
    {
        $return = array_key_exists( $label, $this->_labels );
        return $return;
    }

    
    public function removeLabel( $key ) 
    {
        if ( !is_array ) {
            if ( array_key_exists( $key, $this->_labels )) {
                unset( $this->_labels[$key] );
            }
        } else {
            $this->removeLabels( $key );
        }
        return $this;
    }


    protected function removeLabels( array $key )
    {
        foreach ($key as $key) {
            $this->removeLabel( $key );
        }
    }
}