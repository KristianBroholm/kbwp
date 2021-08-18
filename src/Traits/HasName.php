<?php

namespace kbwp\Traits;

Trait HasName {
    protected $_name;

    public function getName()
    {
        return $this->_name;
    }

    protected function setName( $name ) {
        $this->_name = $name;
    }
}