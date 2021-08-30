<?php

namespace kbwp\Traits;

Trait HasLabels {

    protected $_labels = [];

    public function setLabel(string $key, string $label, bool $override = false, bool $return_obj = true)
    {   
        $state = false;
        if ($this->hasLabel($key) && false == $override) {
            $state = false;
        } else {
            $this->_labels[$key] = $label;
            $state = true;
        }
        $return = ($return_obj ? $this : $state);
        return $return;
    }

    public function setLabels(array $labels, bool $override = false, bool $return_obj = true)
    {
        $state = true;

        foreach($labels as $key => $label)
        {
            $success = $this->setLabel($key, $label, $override, false);
            if (!$success) {
                $state = false;
            }
        }
        $return = ($return_obj ? $this : $state);
        return $this;
    }

    public function getLabel(string $key)
    {
        $return = false;

        if ($this->hasLabel($key)) 
        {
            $return = $this->_labels[$key];
        }
        return $return;
    }


    public function getLabels() 
    {
        return $this->_labels;
    }


    public function hasLabel($label = '')
    {
        $return = array_key_exists($label, $this->_labels);
        return $return;
    }

    
    public function removeLabel($key) 
    {
        if ( !is_array ) {
            if ($this->hasLabel($key)) {
                unset($this->_labels[$key]);
            }
        } else {
            $this->removeLabels($key);
        }
        return $this;
    }


    protected function removeLabels(array $key)
    {
        foreach ($key as $key) {
            $this->removeLabel($key);
        }
    }
}