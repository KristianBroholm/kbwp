<?php
/*
 * @file:           PostType.php
 * @description:    Collection of methods for creating new
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

    public function($singular_name, $plural_name = null, $user_settings = array(), $user_labels = array(), $is_public = true) {

        $this->name     = ucfirst($singular_name);

        if ($plural_name) {
            $this->plural   = ucfirst($plural_name);
        } else {
            $this->plural   = kbwp::pluralize($this->name);
        }

        $this->partitive = kbwp::partitize($this->name);

        $this->slug     = kbwp::slugify($this->name);

        $labels = array(
            'name'                  => $this->plural,
            'singular_name'         => $this->name,
            'add_new_item'          => 'Lisää uusi ' . $this->name,
            'edit_item'             => 'Muokkaa ' $this->partitive
            'new_item'              => 'Uusi ' . $this->name,
            'view_item'             => 'Näytä ' . $this->name,
            'view_items'            => 'Näytä ' . $this->plural,
            'search_items'          => 'Etsi ' . $this->partitive,
            'not_found'             => 'Yhtään ' . $this->partitive . ' ei löytynyt.',
            'not_found_in_trash'    => 'Yhtään ' . $this->partitive . ' ei löytynyt roskakorista',
            'all_items'             => 'Kaikki ' . $this->plural
        );

        $this->labels = array_merge($labels, $user_labels);

        $settings = array(
            'labels'    => $this->labels,
        );

        if($is_public) {
            $defaults = array_merge($settings, array(
                'public'    => true
            ));
        } else {
            $defaults = array($settings, array(
                'public'    => false,
                'show_ui'   => true,
            ));
        }

        $this->settings = array_merge($defaults, $user_settings);

        register_post_type($this->slug, $this->settings);
    }
}
