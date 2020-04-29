<?php
namespace kbwp;

class Taxonomy {

    use PostTypeTrait;

    protected static $_instances = [];
    protected $_is_registered;
    protected $_slug = '';
    protected $_post_types = [];
    protected $_settings = [];
    protected $_labels = [];

    public function __construct( $handle, $post_type = '', $user_labels = array(), $user_settings = array(), $is_public = true )
    {
        $handle = sanitize_key( $handle );

        if ( !$this->instanceExists( $handle ))
        {
            $this->_slug = $handle;
            $this->_is_registered = false;

            $default_settings = [
            ];

            $this->_settings = array_merge( $default_settings, $user_settings );

            if ( is_array( $post_type ))
            {
                $this->addPostTypes( $post_type );
            }
            else
            {
                $this->addPostType( $post_type );
            }
            self::$_instances[$this->_slug] = $this;
            return self::$_instances[$this->_slug];
        }
        return self::$_instances[$handle];
    }


    public function hasPostType($post_type)
    {
        $post_type  = ( $post_type instanceof PostType ? $post_type->getSlug() : $post_type );
        $return     = ( in_array( $post_type, $this->_post_types ) ? true : false );
        return $return;
    }


    public function addPostType($post_type = '', bool $return_obj = true)
    {
        $return = false;

        if ( !is_array( $post_type ))
        {
            if ( $post_type instanceof PostType )
            {
                $post_type = $post_type->getSlug();
            }
            if ( !$this->hasPostType( $post_type ))
            {
                array_push( $post_type, $this->_post_types )
                $return = true;
            }
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function addPostTypes( array $post_types, bool $return_obj = true )
    {
        $return = false;

        if ( is_array( $post_types ))
        {
            foreach( $post_types as $post_type )
            {
                $this->addPostType( $post_type );
            }
            $return = true;
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function register()
    {
        if ( !$this->isRegistered() )
        {
            $this->is_registered = true;
            add_action( 'init', [$this, 'init'] );
            return;
        }
        throw new \Exception('Taxonomies can only be registered once!');
    }


    public function init()
    {
        register_taxonomy( $this->_slug, $this->_post_types, $this->_settings );
    }


    public function removePostType( $post_type = '', bool $return_obj = true )
    {
        $return = false;

        if ( !is_array( $post_type ) )
        {
            if ( $post_type instanceof PostType )
            {
                $post_type = $post_type->getSlug();
            }
            if ( $this->hasPostType( $post_type ))
            {
                unset( $this->_post_types[array_search( $post_type, $this->_post_types )] );
                $return = true;
            }
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function removePostTypes( array $post_type, bool $return_obj = true )
    {
        $return = false;

        if ( is_array( $post_type ))
        {
            foreach( $post_type as $post_type )
            {
                $this->removePostType( $post_type, false );
            }
            $return = true;
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }
}
