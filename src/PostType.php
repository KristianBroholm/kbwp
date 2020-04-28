<?php
/*
 * @file:           PostType.php
 * @description:    Collection of methods for creating new Post Types
 * @package:        kbwp
 * @author:         kristianb
 * @email:          kristian.broholm@gmail.com
 * @since:          1.0.0
 * */

namespace kbwp;

class PostType
{
    use PostTypeTrait;
    protected $_slug;
    protected $_settings;
    protected $_labels;

    /**
     * Creates new Post Type to be registered.
     * @param string        $handle        Slug for post type eg. 'post' or 'page'.
     * @param array         $user_labels   Custom labels for the post type.
     * @param array         $user_settings Custom settings for the post type
     * @param boolean       $is_public     Defines should post type be public or not. Private post types are still visible on the admin side by default.
     */
    public function __construct($handle = '', $user_labels = array(), $user_settings = array(), $is_public = true )
    {
      $this->_slug = kbwp::slugify($handle);

      $default_labels = array(
          'name' => ucfirst($handle)
      );

      $this->_labels = array_merge($default_labels, $user_labels);

      $default_settings = array(
          'labels'        => $this->_labels,
          'supports'      => array(
              'title'
          ),
          'taxonomies'    => array()
      );

      if ($is_public) {
          $defaults = array_merge($default_settings, array(
              'public'    => true
          ));
      } else {
          $defaults = array_merge($default_settings, array(
              'public'        => false,
              'show_ui'       => true,
              'show_in_menu'  => true
          ));
      }

      $this->_settings = array_merge($defaults, $user_settings);
    }


    public function addSupport($feature = '')
    {
        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if (!$this->hasSupport($feature)) {
                $this->_settings['supports'][] = $feature;
            }
        }
    }


    public function getSlug() {
      return $this->_slug;
    }


    public function hasSupport($feature)
    {
        if (is_array($this->_settings['supports'])) {
            if (in_array($feature, $this->_settings['supports'])) {
                return true;
            }
        }
        return false;
    }








    public function debug()
    {
      kbwp::log($this);
    }


    public function removeSupport($feature = '')
    {
        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if ($this->hasSupport($feature)) {
                foreach($this->_settings['supports'] as $key => $value) {
                    if ($value == $feature) {
                        unset($this->_settings['supports'][$key]);
                    }
                }
            }
        }
    }


    public function hasTaxonomy($taxonomy)
    {
        if (array_key_exists('taxonomies', $this->_settings)) {
            if (in_array($taxonomy, $this->_settings['taxonomies'])) {
                return true;
            }
        }
        return false;
    }


    public function addTaxonomy($taxonomy = '')
    {
        if (is_array($taxonomy)) {
            $taxonomies = $taxonomy;
        } else {
            $taxonomies[] = $taxonomy;
        }

        foreach($taxonomies as $taxonomy) {
            if (!$this->has_taxonomy($taxonomy)) {
                $this->_settings['taxonomies'][] = $taxonomy;
            }
        }
    }


    public function removeTaxonomy($taxonomy = '')
    {
        if (is_array($taxonomy)) {
            $taxonomies = $taxonomy;
        } else {
            $taxonomies[] = $taxonomy;
        }

        foreach($taxonomies as $taxonomy) {
            if ($this->hasTaxonomy($taxonomy)) {
                foreach($this->_settings['taxonomies'] as $key => $value) {
                    if ($value == $taxonomy) {
                        unset($this->_settings['taxonomies'][$key]);
                    }
                }
            }
        }
    }


    public function createTaxonomy($handle, array $user_labels = array(), array $user_settings = array(), bool $is_public = true)
    {
        $taxonomy = new Taxonomy($handle, $this->_slug, $user_labels, $user_settings, $is_public);
        return $taxonomy;
    }


    public function register()
    {
        add_action('init', array($this, 'init'));
    }


    public function init() {
      $this->_settings['labels'] = $this->_labels;
      register_post_type($this->_slug, $this->_settings);
    }
}
