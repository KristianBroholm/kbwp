<?php

namespace kbwp;

// Base class for Themes and Plugins
abstract class Extension
{
    private static $_instance = array();
    private $_actions = array();
    private $_filters = array();
    private $_scripts = array();
    private $_styles = array();
    private $_counter = array();


    public function __toString()
    {
        $class = get_called_class();
        return $class;
    }


    public function addAction($hook, $action, int $priority = 10, int $accepted_args = 1, bool $return_obj = true)
    {
        if (!is_array($action) && is_callable($action))
        {
            return $this->addAnonymous('action', $hook, $action, $priority, $accepted_args, $return_obj);
        }
        $return               = false;
        $key                  = $this->generateKey($hook, $action);
        $properties           = [$hook, $action, $priority, $accepted_args];
        $this->_actions[$key] = $properties;

        if (!$this->hasAction($hook, $action))
        {
            $this->_actions[$key]   = $properties;
            $return                 = true;
        }
        $return = ($return_obj ? $this : $return);
        return $return;
    }


    private function addAnonymous($type = 'action', $hook, $action, int $priority = 10, int $accepted_args = 1, bool $return_obj = true)
    {
        $key                  = $hook . '_anonymous_' . count($this->_counter);
        $this->_counter[]     = $key;
        $properties           = [$hook, $action, $priority, $accepted_args];

        switch($type) {
            case 'action':
                $this->_actions[$key] = $properties;
                break;
            case 'filter':
                $this->_filters[$key] = $properties;
                break;
        }
        $return = ($return_obj ? $this : true);
    }


    public function addFilter($hook, $action, int $priority = 10, int $accepted_args = 1, bool $return_obj = true)
    {
        if (!is_array($action) && is_callable($action))
        {
            return $this->addAnonymous('filter', $hook, $action, $priority, $accepted_args, $return_obj);
        }
        $return               = false;
        $key                  = $this->generateKey($hook, $action);
        $properties           = [$hook, $action, $priority, $accepted_args];

        if (!$this->hasFilter($hook, $action))
        {
            $this->_filters[$key] = $properties;
            $return = true;
        }
        $return = ($return_obj ? $this : $return);
        return $return;
    }


    public function addGoogleAnalytics($id = '', array $environments = [], bool $disableIfAdmin = true,  bool $return_obj = true)
    {
        if ( in_array( $_ENV['WP_ENV'], $environments ) )
        {
            if ( ( $disableIfAdmin && !is_user_logged_in() ) || !$disableIfAdmin )
            {
                $this->addAction('wp_head', function() use ($id) {
                    echo '<!-- // Google Analytics -->';
                    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $id . '"></script>';
                    echo '<script>';
                    echo 'window.dataLayer = window.dataLayer || [];';
                    echo 'function gtag(){dataLayer.push(arguments);}';
                    echo 'gtag(\'js\', new Date());';
                    echo 'gtag(\'config\', \'' . $id . '\');';
                    echo '</script>';
                }, 2);
            }
        }
        $return = ($return_obj ? $this : true);
        return $return;
    }


    public function addScript($handle, $src, $deps = [], $ver = false, $in_footer = false)
    {
        $this->_scripts[$handle] = ['handle' => $handle, 'src' => $src, 'deps' => $deps, 'ver' => $ver, 'in_footer' => $in_footer];
    }


    public function addStyle($handle, $src, $deps = [], $ver = false, $in_footer = false)
    {
        $this->_styles[$handle] = ['handle' => $handle, 'src' => $src, 'deps' => $deps, 'ver' => $ver, 'in_footer' => $in_footer];
    }


    public function addTypekit($id = '')
    {
        $this->addScript('typekit-'. $id, 'https://use.typekit.net/' . $id . '.js');
        $this->addAction('wp_head', array( $this, 'initTypekit'));
    }


    public function debug( bool $all_instances = false )
    {
        if ($all_instances)
        {
            kbwp::log(self::$_instance);
        }
        kbwp::log($this);
    }


    public function enqueueScripts()
    {
        foreach($this->_styles as $style)
        {
            wp_enqueue_style($style['handle'], $style['src'], $style['deps'], $style['ver'], $style['in_footer']);
        }
        foreach($this->_scripts as $script)
        {
            wp_enqueue_script($script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer']);
        }
    }


    private function generateKey($hook, $action)
    {
        if (is_array($action))
        {
            $key = $hook . '_' . strval($action[0]) . '_' . $action[1];
        }
        else
        {
            $key = $hook . '_' . $action;
        }
        return $key;
    }


    public function hasAction($hook, $action)
    {
        $key      = $this->generateKey($hook, $action);
        $return   = array_key_exists($key, $this->_actions);
        return $return;
    }


    public function hasFilter($hook, $filter)
    {
        $key      = $this->generateKey($hook, $filter);
        $return   = array_key_exists($key, $this->_filters);
        return $return;
    }


    public function hasStyle($handle = '')
    {
        $return = (array_key_exists($handle, $this->_styles) ? true : false);
        return $return;
    }


    public static function init()
    {
        $class = get_called_class();

        if (!isset(self::$_instance[$class]))
        {
            self::$_instance[$class] = new $class();
        }
        return self::$_instance[$class];
    }


    public function initTypekit()
    {
        echo "<script>\n//Init typekit\ntry{Typekit.load({ async: true });}catch(e){}\n</script>";
    }


    public static function load()
    {
        $class = get_called_class();
        $extension = $class::init();
        $extension->loadScripts();
        $extension->loadActions();
        $extension->loadFilters();
    }


    private function loadActions()
    {
        foreach($this->_actions as $action)
        {
            add_action($action[0], $action[1], $action[2], $action[3]);
        }
    }


    private function loadFilters()
    {
        foreach($this->_filters as $filter)
        {
            add_filter($filter[0], $filter[1], $filter[2], $filter[3]);
        }
    }


    private function loadScripts()
    {
        $this->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }


    public function removeAction($hook, $action, bool $return_obj = true)
    {
        $return = false;

        if ($this->hasAction($hook, $action))
        {
            unset($this->_actions[$this->generateKey($hook, $action)]);
            $return = true;
        }
        $return = ($return_obj ? $this : $return);
        return $return;
    }


    public function removeFilter($hook, $action, bool $return_obj = true)
    {
        $return = false;

        if ($this->hasFilter($hook, $action))
        {
            unset($this->_filters[$this->generateKey($hook, $action)]);
            $return = true;
        }
        $return = ($return_obj ? $this : $return);
        return $return;
    }


    public function removeStyle($handle = '', bool $return_obj = false)
    {
        $return = ($return_obj ? $this : false);

        if ($this->hasStyle($handle))
        {
            unset($this->_styles[$handle]);
            $return = ($return_obj ? $this : true);
        }
        return $return;
    }
}
