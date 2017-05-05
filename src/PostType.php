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

class PostType {

    public $name;
    public $plural;
    public $partitive;
    public $slug;
    public $defaults;
    public $settings;
    public $labels;

    /**
     * Creates new Post Type to be registered.
     * @param string        $handle        Slug for post type eg. 'post' or 'page'.
     * @param string|array  $name          Name of the post type. First value of the array is used as singular_name and second value as plurarl_name.
     * @param boolean       $is_public     Defines should post type be public or not. Private post types are still visible on the admin side by default.
     * @param array         $user_settings Custom settings for the post type
     * @param array         $user_labels   Custom labels for the post type.
     */
    public function __construct($handle = '', $name = '', $is_public = true, $user_settings = array(), $user_labels = array()) {

        if (is_array($name)) {
            $this->name     = ucfirst($name[0]);
            $this->plural   = ucfirst($name[1]);
        } else {
            $this->name     = ucfirst($name);
            $this->plural   = ucfirst($name);
        }

        $this->slug = kbwp::slugify($handle);

        $default_labels = array(
            'name' => $this->plural,
        );

        $this->labels = array_merge($default_labels, $user_labels);

        $default_settings = array(
            'labels'        => $this->labels,
            'supports'      => array(
                'title'
            ),
            'taxonomies'    => array()
        );

        if($is_public) {
            $defaults = array_merge($default_settings, array(
                'public'    => true
            ));
        } else {
            $defaults = array($default_settings, array(
                'public'    => false,
                'show_ui'   => true,
            ));
        }

        $this->settings = array_merge($defaults, $user_settings);
    }

    public function add_support($feature = '') {
        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if (!$this->has_support($feature)) {
                $this->settings['supports'][] = $feature;
            }
        }
    }

    public function has_support($feature) {
        if (is_array($this->settings['supports'])) {
            if (in_array($feature, $this->settings['supports'])) {
                return true;
            }
        }
        return false;
    }

    public function remove_support($feature = '') {

        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if ($this->has_support($feature)) {
                foreach($this->settings['supports'] as $key => $value) {
                    if ($value == $feature) {
                        unset($this->settings['supports'][$key]);
                    }
                }
            }
        }
    }

    public function has_taxonomy($taxonomy) {
        if (array_key_exists('taxonomies', $this->settings)) {
            if (in_array($taxonomy, $this->settings['taxonomies'])) {
                return true;
            }
        }
        return false;
    }

    public function add_taxonomy($taxonomy = '') {

        if (is_array($taxonomy)) {
            $taxonomies = $taxonomy;
        } else {
            $taxonomies[] = $taxonomy;
        }

        foreach($taxonomies as $taxonomy) {
            if (!$this->has_taxonomy($taxonomy)) {
                $this->settings['taxonomies'][] = $taxonomy;
            }
        }
    }

    /**
     * Removes given taxonomy or taxonomies
     * @param  string|array     $taxonomy Taxonomies to be removed
     */
    public function remove_taxonomy($taxonomy = '') {
        if (is_array($taxonomy)) {
            $taxonomies = $taxonomy;
        } else {
            $taxonomies[] = $taxonomy;
        }

        foreach($taxonomies as $taxonomy) {
            if ($this->has_taxonomy($taxonomy)) {
                foreach($this->settings['taxonomies'] as $key => $value) {
                    if ($value == $taxonomy) {
                        unset($this->settings['taxonomies'][$key]);
                    }
                }
            }
        }
    }

    /**
     * Returns Post Type's settings.
     * @return array $this->settings
     */
    private function get_settings() {
        return $this->settings;
    }

    /**
     * Registers the post type by using WP's register_post_type -function.
     */
    public function __destruct() {
        register_post_type($this->slug, $this->get_settings());
        unset($this);
    }
}
