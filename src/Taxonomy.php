<?php

namespace kbwp;

class Taxonomy {

    private $slug;
    public $hook;
    private $post_types;
    private $settings;

    public function __construct($handle, $post_type = '', $user_labels = array(), $user_settings = array(), $is_public = true) {

        $this->slug = kbwp::slugify($handle);

        $this->hooks = array(
            'before' => 'before_taxonomy_' . $this->slug . '_is_registered',
            'after' => 'after_taxonomy_' . $this->slug . '_is_registered'
        );

        if (is_array($post_type)) {
            $this->post_types = $post_type;
        } else {
            $this->post_types = array();
            $this->post_types[] = $post_type;
        }

        $default_settings = array(
            'labels'    => $user_labels
        );

        if ($is_public) {
            $default_settings = array_merge($default_settings, array(

            ));
        } else {
            $default_settings = array_merge($default_settings, array(

            ));
        }

        $this->settings = array_merge($default_settings, $user_settings);
    }

    public function registered_for_post_type($post_type) {

        if (in_array($post_type, $this->post_types)) {
            return true;
        }
        return false;
    }

    public function register_for_post_type($post_type = '') {

        if (is_array($post_type)) {
            $post_types = $post_type;
        } else {
            $post_types = array();
            $post_types[] = $post_type;
        }

        foreach($post_types as $post_type) {
            if (!$this->registered_for_post_type($post_type)) {
                $this->post_types[] = $post_type;
            }
        }
    }

    public function remove_from_post_type($post_type) {

        if (is_array($post_type)) {
            $post_types = $post_type;
        } else {
            $post_types = array();
            $post_types[] = $post_type;
        }

        foreach($post_types as $post_type) {
            if ($this->registered_for_post_type($post_type)) {
                foreach($this->post_types as $key => $value) {
                    if ($value == $post_type) {
                        unset($this->post_types[$key]);
                    }
                }
            }
        }
    }

    public function __destruct() {

        register_taxonomy($this->slug, $this->post_types, $this->settings);
        unset($this);
    }
}
