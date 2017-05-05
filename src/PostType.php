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

    private $slug;
    private $settings;

    /**
     * Creates new Post Type to be registered.
     * @param string        $handle        Slug for post type eg. 'post' or 'page'.
     * @param array         $user_labels   Custom labels for the post type.
     * @param array         $user_settings Custom settings for the post type
     * @param boolean       $is_public     Defines should post type be public or not. Private post types are still visible on the admin side by default.
     */
    public function __construct($handle = '', $user_labels = array(), $user_settings = array(), $is_public = true ) {

        $this->slug = kbwp::slugify($handle);

        $default_labels = array(
            'name' => ucfirst($handle)
        );

        $this->labels = array_merge($default_labels, $user_labels);

        $default_settings = array(
            'labels'        => $this->labels,
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
    public function get_settings() {
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
