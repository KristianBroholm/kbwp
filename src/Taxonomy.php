<?php
namespace kbwp;

class Taxonomy {

    use Traits\HasHandle;
    use Traits\HasName;
    use Traits\HasLabels;
    use Traits\HasPostTypes;
    use Traits\HasSettings;
    use Traits\MustBeRegistered;

    public function __construct( $name, $post_type, $user_labels = array(), $user_settings = array(), $is_public = true )
    {

        $this->setName( $name );
        $this->setHandle( $name );
        $this->addLabels( $user_labels );

        $default_settings = [
            'labels'        => $this->_labels,
            'show_in_rest'  => true
        ];

        if ( !$is_public ) {
            $default_settings = array_merge(
                $default_settings,
                [
                    'show_in_ui'            => true,
                    'show_in_nav_menus'     => false,
                    'publicly_queryable'    => true
                ]
            );
        }

        $this->addSettings( $default_settings );
        $this->addSettings( $user_settings );
        $this->addPostType( $post_type );
            
        return $this;
    }


    public function register()
    {
        if ( !$this->isRegistered() )
        {
            $this->setRegistrationState( true );
            add_action( 'init', [$this, 'init'] );
            return;
        }
        throw new \Exception('Taxonomies can only be registered once!');
    }


    public function init()
    {
        register_taxonomy( $this->getHandle(), $this->getPostTypes(), $this->getSettings() );
    }
}