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

    public function __construct($handle, $name, $is_public = true, $user_settings = array(), $user_labels = array()) {

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

    public function supports($feature = '') {
        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if (!$this->has_support_for($feature)) {
                $this->settings['supports'][] = $feature;
            }
        }
    }

    public function has_support_for($feature) {
        if (is_array($this->settings['supports'])) {
            if (in_array($feature, $this->settings['supports'])) {
                return true;
            }
        }
        return false;
    }

    public function does_not_support($feature = '') {

        if (!is_array($feature)) {
            $features[] = $feature;
        } else {
            $features = $feature;
        }

        foreach($features as $feature) {
            if ($this->has_support_for($feature)) {
                foreach($this->settings['supports'] as $key => $value) {
                    if ($value == $feature) {
                        unset($this->settings['supports'][$key]);
                    }
                }
            }
        }
    }

    public function get_settings() {
        return $this->settings;
    }

    public function __destruct() {
        register_post_type($this->slug, $this->get_settings());
        unset($this);
    }
}
