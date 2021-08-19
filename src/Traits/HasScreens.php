<?php
namespace kbwp\Traits;

Trait HasScreens {

    protected $_screens = [];

    /**
     * Adds screen
     *
     * @param mixed $screen
     */
    public function addScreen($screen = '')
    {
        if (!empty($screen)) 
        {
            if (is_array($screen)) {
                $this->addScreens($screen);
                return $this;
            }
            if (is_string($screen)) 
            {
                if (!$this->hasScreen($screen)) {
                    $this->_screens[] = $screen;
                }
                return $this;
            }
            if (in_array('HasHandle', class_uses($screen, true))) 
            {
                if (!$this->hasScreen($screen)) {
                    $this->_screens[] = $screen->getHandle();
                }
            }
        }
        return $this;
    }


    protected function addScreens(array $screens)
    {
        foreach($screens as $screen) {
            $this->addScreen($screen);
        }
    }

    
    public function getScreens()
    {
        return $this->_screens;
    }


    public function hasScreen(string $screen)
    {
        return in_array($screen, $this->_screens);
    }
}