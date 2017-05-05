<?php

namespace kbwp;

class Taxonomy {

    private $slug;
    private $post_types;
    private $settings;

    public function __construct($handle, $post_type = '', $user_labels = array(), $user_settings = array(), $is_public = true) {

        $this->slug = kbwp::slugify($handle);

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

    public function has_post_type($post_type) {

        if (in_array($post_type, $this->post_types)) {
            return true;
        }
        return false;
    }

    public function add_post_type($post_type = '') {

        if (is_array($post_type)) {
            $post_types = $post_type;
        } else {
            $post_types = array();
            $post_types[] = $post_type;
        }

        foreach($post_types as $post_type) {
            if (!$this->has_post_type($post_type)) {
                $this->post_types[] = $post_type;
            }
        }
    }

    public function remove_post_type($post_type) {

        if (is_array($post_type)) {
            $post_types = $post_type;
        } else {
            $post_types = array();
            $post_types[] = $post_type;
        }

        foreach($post_types as $post_type) {
            if ($this->has_post_type($post_type)) {
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
