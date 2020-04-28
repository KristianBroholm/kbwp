<?php
namespace kbwp;

Trait PostTypeTrait {

  public function hasLabel($tag = '')
  {
    $return = array_key_exists($tag, $this->_labels);
    return $return;
  }


  public function addLabel($key = '', $label = '', bool $return_obj = true)
  {
    if(!is_array($key))
    {
      $this->_labels[$key]  = $label;
    }
    $return = ($return_obj ? $this : true);
    return $return;
  }


  public function addLabels(array $labels = array(), bool $return_obj = true)
  {
    $return = false;
    if(is_array($labels))
    {
      foreach($labels as $key => $label)
      {
        $this->addLabel($key, $label, false);
      }
      $return = true;
    }
    $return = ($return_obj ? $this : $return);
    return $return;
  }


  public function removeLabel($key = '', bool $return_obj = true)
  {
    $return = false;
    if ($this->hasLabel($key)) {
      unset($this->_labels[$key]);
      $return = true;
    }
    $return = ($return_obj ? $this : $return);
  }


  public function addSetting($setting = '', $value = '', $return_obj = true)
  {
    $this->_settings[$setting] = $value;
    $return($return_obj ? $this : $value);
  }


  public function addSettings(array $settings = array(), $return_obj = true)
  {
    $return false;
    if(is_array($settings))
    {
      foreach($settings as $setting => $value)
      {
        $this->addSetting($settings, $value, false);
      }
      return $true;
    }
    $return = ($return_obj ? $this : $return);
    return $return;
  }
}
