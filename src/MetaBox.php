<?php
namespace kbwp;

use Timber\Timber as Timber;

abstract class MetaBox implements Interfaces\MetaBoxInterface {

    use Traits\HasHandle;
    use Traits\HasName;
    use Traits\HasScreens;
    use Traits\MustBeRegistered;

    protected $_context;
    protected $_priority;

    /**
     * Creates new MetaBox.
     *
     * @param   string  $name           | Name that is dispayed in the backend.
     * @param   string  $handle         | Handle that WordPress uses for internal processing.
     * @param   mixed   $screens        
     * @param   string  $context        | Accepted valeus are 'side', 'normal' or 'advanced'. Defaults to 'advanced'
     * @param   string  $priority       | Accepted values are 'low', 'normal' or 'high'. Defaults to 'normal'.
     */
    public function __construct(
        string $name, 
        string $handle = '', 
        $screens = '',
        string $context = 'advanced', 
        string $priority = 'normal' 
    ) { 
        $handle = (!empty($handle)? $handle : $name);
        $this->setName($name);
        $this->setHandle($handle);
        $this->addScreen($screens);
        $this->setContext($context);
        $this->setPriority($priority); 
        $this->setRegistrationState(false);  
    }

    /**
     * Sets context for metabox. 
     *
     * @param   string  $context    | Other values than 'normal','side' or 'advanced' are ignored.
     * @return  obj     $this
     */
    public function setContext(string $context)
    {
        if (in_array($context, ['normal', 'side','advanced']))
        {
            $this->_context = $context;
        }
        return $this;
    } 

    /**
     * Returns metbox's context.
     *
     * @return  string  $context    | Can be either 'advanced', 'normal' or 'low'.
     */
    public function getContext(): string 
    {
        return $this->_context;
    }

    /**
     * Sets MetaBox's priority.
     *  
     * @param   string  $priority   | Accepted values are 'low', 'normal' or 'high'. Other values are ignored.
     * @return  obj     $this       | Returns the object.
     */
    public function setPriority(string $priority) 
    {
        if (in_array($priority, ['low','normal','high'])) {
            $this->_priority = $priority;
        }
        return $this;
    }

    /**
     * Get MetaBox's priority.
     *
     * @return  string  $priority   | MetaBox's priority. Can be either 'low', 'normal' or 'high'.
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Callback function for WordPress's registration hook.
     *
     * @return void
     */
    public function registration_callback() 
    {
        add_meta_box($this->getHandle(), $this->getName(), [$this, 'render'], $this->getScreens());
        return;
    }

    /**
     * Registers MetaBox with WordPress
     *
     * @return void
     */
    public function register() 
    {
        if (!$this->isRegistered()) 
        {        
            $this->setRegistrationState( true );
            add_action('add_meta_boxes', [ $this, 'registration_callback' ]);
        }
        return;
    }
}