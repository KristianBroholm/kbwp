<?php
namespace kbwp;

class PostType
{
    use PostTypeTrait;
    protected static $_instances = [];
    protected $_is_registered;
    protected $_slug;
    protected $_settings;
    protected $_labels;

    public function __construct($handle = '', array $user_labels = [], $user_settings = [], $is_public = true)
    {
        $handle = sanitize_key($handle);

        if ( !$this->instanceExists( $handle ) )
        {
            $this->_slug = $handle;
            $this->_is_registered = false;

            $default_labels = [
                'name' => ucfirst($this->_slug),
            ];

            $this->_labels = array_merge( $default_labels, $user_labels );

            $default_settings = [
                'public'  => true,
                'supports' => [
                'title'
                ]

            ];

            if ( !$is_public )
            {
                $default_settings = array_merge( $default_settings, [
                    'public'        => false,
                    'show_ui'       => true,
                    'show_in_menu'  => true
                ]);
            }

            $this->_settings = array_merge( $default_settings, $user_settings );

            self::$_instances[$this->_slug] = $this;
            return self::$_instances[$this->_slug];
        }
        return self::$_instances[$handle];
    }


    public function addSupport( $feature = '', bool $return_obj = true )
    {
        $return = false;
        $errors = [];

        if ( !is_array( $feature ) && !empty( $feature ))
        {
            $features[] = $feature;
        }

        foreach( $features as $feature )
        {
            $errors[] = ( $this->hasSupport( $feature ) ? true : false );

            if ( !$this->hasSupport( $feature ))
            {
                $this->_settings['supports'][] = $feature;
            }
        }
        $return = $this->hasErrors( $errors );
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function hasSupport( $feature = '' )
    {
        if ( is_array($this->_settings['supports'] ))
            {
            if ( in_array( $feature, $this->_settings['supports'] ))
            {
                return true;
            }
        }
        return false;
    }


    public function removeSupport($feature = '', bool $return_obj = true)
    {
        $errors = [];

        if ( !is_array( $feature ))
        {
          $feature[] = $feature;
        }

        foreach( $feature as $feature )
        {
              $errors[] = $this->hasSupport( $feature );

              if ( $this->hasSupport( $feature ))
              {
                  $key = array_search( $feature, $this->_settings['supports'] );
                  unset( $this->_settings['supports'][$key] );
              }
        }
        $return = $this->hasErrors( $errors );
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function hasTaxonomy( $taxonomy )
    {
        if ( array_key_exists( 'taxonomies', $this->_settings ))
        {
            if ( in_array( $taxonomy, $this->_settings['taxonomies'] ))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds existing Taxonomy for PostType
     * @param string|array|object   $taxonomy
     * @param boolean               $return_obj, defaults to true
     */
    public function addTaxonomy( $taxonomy = '', bool $return_obj = true )
    {
        $return = false;

        if ( !is_array( $taxonomy ))
        {
            if ( $taxonomy instanceof Taxonomy )
            {
                $taxonomy = $taxonomy->getSlug();
            }

            if ( !$this->hasTaxonomy( $taxonomy ))
            {
                $this->_settings['taxonomies'][] = $taxonomy;
                $return = true;
            }
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    /**
     * Adds multiple existing Taxonomies for Post Type
     * @param array   $taxonomies Array of taxonomy names or objects to be added
     * @param boolean $return_obj Returns $this if set to true. Defaultcs to true.
     */
    public function addTaxonomies( array $taxonomies, bool $return_obj = true )
    {
        $return = false;
        $errors = [];

        foreach ( $taxonomies as $taxonomy )
        {
            $errors[] = $this->addTaxonomy( $taxonomy, false );
        }
        $return = ( $return_obj ? $this : $this->hasErrors( $errors ) );
        return $return;
    }


    public function removeTaxonomy( $taxonomy = '', bool $return_obj = true )
    {
        $return = false;

        if ( !is_array( $taxonomy ))
            {
            if ( $taxonomy instanceof Taxonomy )
            {
                $taxonomy = $taxonomy->getSlug();
            }

            if ( $this->hasTaxonomy( $taxonomy ))
            {
                $key = array_search( $taxonomy, $this->_settings['taxonomies'] );
                unset( $this->_settings['taxonomies'][$key] );
                $return = true;
            }
        }
        $return = ( $return_obj ? $this : $return );
        return $return;
    }


    public function createTaxonomy( $handle, array $user_labels = [], array $user_settings = [], bool $is_public = true )
    {
        $taxonomy = new Taxonomy( $handle, $this->getSlug(), $user_labels, $user_settings, $is_public );
        $this->addTaxonomy( $taxonomy );
        return $taxonomy;
    }


    public function register()
    {
        if ( !$this->isRegistered() )
        {
            $this->_is_registered = true;
            add_action('init', [$this, 'init']);
            return;
        }
        throw new \Exception('PostTypes can only be registered once!');
    }


    public function init()
    {
        $this->_settings['labels'] = $this->_labels;
        register_post_type( $this->getSlug(), $this->_settings );
    }
}
