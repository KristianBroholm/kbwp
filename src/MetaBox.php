<?php

namespace kbwp;

class MetaBox {

    use Traits\HasHandle;
    use Traits\HasName;
    use Traits\HasPostTypes;
    use Traits\MustBeRegistered;

    public function __construct( string $name, array $post_types = [] ) {
        
        $this->setHandle($name);
        $this->setName($name);
        $this->setRegistrationState(false);  

        kbwp::log($this);
        return $this;
    }

    public function register() {}
}