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

    public function __construct($handle, $name, $is_public = true, $user_settings = array(), $user_labels = array()) {

        if (is_array($name)) {
            $this->name     = ucfirst($name[0]);
            $this->plural   = ucfirst($name[1]);
        } else {
            $this->name     = ucfirst($name);
            $this->plural   = ucfirst(kbwp::pluralize($this->name));
        }

        $this->partitive = kbwp::partitize($this->name);

        $this->slug     = kbwp::slugify($handle);

        $labels = array(
            'name'                  => $this->plural,
            'singular_name'         => $this->name,
            'add_new_item'          => 'Lisää uusi ' . mb_strtolower($this->name),
            'edit_item'             => 'Muokkaa ' . mb_strtolower($this->partitive),
            'new_item'              => 'Uusi ' . mb_strtolower($this->name),
            'view_item'             => 'Näytä ' . mb_strtolower($this->name),
            'view_items'            => 'Näytä ' . mb_strtolower($this->name),
            'search_items'          => 'Etsi ' . mb_strtolower($this->partitive),
            'not_found'             => 'Yhtään ' . mb_strtolower($this->partitive) . ' ei löytynyt.',
            'not_found_in_trash'    => 'Yhtään ' . mb_strtolower($this->partitive) . ' ei löytynyt roskakorista',
            'all_items'             => 'Kaikki ' . mb_strtolower($this->plural)
        );

        $this->labels = array_merge($labels, $user_labels);

        $settings = array(
            'labels'        => $this->labels,
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
