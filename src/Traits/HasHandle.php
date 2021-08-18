<?php 

namespace kbwp\Traits;

use kbwp\kbwp as kbwp;

Trait HasHandle {

    protected $_handle;

    public function getHandle()
    {
        return $this->_handle;
    }

    protected function setHandle( string $handle )
    {
        $handle = kbwp::slugify($handle);
        $handle = sanitize_key($handle);
        $this->_handle = $handle;
    }
}