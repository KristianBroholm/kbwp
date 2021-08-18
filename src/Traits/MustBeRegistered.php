<?php

namespace kbwp\Traits;

Trait MustBeRegistered {

    protected $_is_registered = false;

    abstract public function register();

    public function isRegistered()
    {
        return $this->_is_registered;
    }

    protected function setRegistrationState( bool $state = false ) {
        $this->_is_registered = $state;
    }
}