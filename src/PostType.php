<?php
namespace kbwp;

class PostType
{
    use Traits\HasSettings;
    use Traits\HasLabels;
    use Traits\HasName;
    use Traits\HasHandle;
    use Traits\MustBeRegistered;

    public function __construct( 
        string  $name, 
        array   $user_labels    = [], 
        array   $user_settings  = [], 
        bool    $is_public      = true
        )
    {
        $this->setName($name);
        $this->setHandle($this->_name);

        $default_labels = [
            'name' => $this->getName()
        ];

        $this->setLabels( $default_labels );
        $this->setLabels( $user_labels, true );

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

        $this->addSettings($default_settings);
        $this->addSettings($user_settings);
        
        return $this;
    }


    public function addSupport( $feature, bool $return_obj = true )
    {
        $return = false;
        $features = [];

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
                $taxonomy = $taxonomy->getHandle();
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


    public function register()
    {
        if ( !$this->isRegistered() )
        {
            $this->setRegistrationState(true);
            add_action('init', [$this, 'registration_callback']);
            return;
        }
        throw new \Exception('PostTypes can only be registered once!');
    }


    public function registration_callback()
    {
        $this->addSetting('labels', $this->getLabels());
        register_post_type($this->getHandle(), $this->getSettings());
    }
}
