<?php

namespace kbwp\Traits;

Trait HasPostTypes {

    protected $_post_types = [];


    public function hasPostType( $post_type )
    {
        $post_type  = ( $post_type instanceof PostType ? $post_type->getSlug() : $post_type );
        $return     = ( in_array( $post_type, $this->_post_types ) ? true : false );
        return $return;
    }


    public function addPostType( $post_type )
    {
        if ( !is_array( $post_type ))
        {
            if ( $post_type instanceof PostType )
            {
                $post_type = $post_type->getSlug();
            }
            if ( !$this->hasPostType( $post_type ))
            {
                array_push( $this->_post_types, $post_type );
            }
        } else {
            $this->addPostTypes( $post_type );
        }
        return $this;
    }


    protected function addPostTypes( array $post_types )
    {
        foreach( $post_types as $post_type )
        {
            $this->addPostType( $post_type );
        }
        return $this;
    }


    public function removePostType( $post_type )
    {

        if ( !is_array( $post_type ) )
        {
            if ( $post_type instanceof PostType )
            {
                $post_type = $post_type->getSlug();
            }
            if ( $this->hasPostType( $post_type ))
            {
                unset( $this->_post_types[ array_search( $post_type, $this->_post_types ) ] );
            }
        } else {
            $this->removePostTypes( $post_type );
        }
        return $this;
    }


    public function removePostTypes( array $post_type )
    {
        foreach( $post_type as $post_type )
        {
            $this->removePostType( $post_type, false );
        }
        return $this;
    }


    public function getPostTypes() {
        return $this->_post_types;
    }
}