<?php
 /*
  * @file:			Form.php
  * @author:		kristianb
  * @version:		1.0.0
  * @description:	Efficient class for rapidly creating HTML-forms by using simple array syntax.
*/
namespace kbwp;
use Timber;

abstract class Form {

	// Pre-define properties
	public $action;
    public $class;
	public $errors;
	public $fields;
    public $id;
	public $method;
	public $title;
	public $submit_key;
	public $status;
	
    /**
     * @author: kristianb
     * @since:  1.0.0
     * @param   $title  string  Lomakkeen otsikko
     * @param   $class  string  Lomakkeen class
     * @param   $id     string  Lomakkeen id
     */
	public function __construct( $title = 'Lomakkeen nimi', $class = '', $id = '', $fields = array() ) {
        
		// Define form's class if not defined
		if ( empty( $class ) ) {
			$class = $this->uglify( $title );
		}
		
		// Define form's ID if not defined
		if ( empty( $id ) ) {	
			$id = $this->uglify( $title );
		}
		
		
		// Define form properties
        $this->title   		= $title;
        $this->class    	= $class;
        $this->id       	= $id;
        $this->method   	= 'post';
		$this->errors		= false;
		$this->status		= 'form';
		$this->submit_key	= 'form-' . $id . '-submit';
		$this->action   	= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		// Add fields to form
		$this->add_fields( $fields );
		
		// Add hidden field for submit_key
		$this->add_field( $this->submit_key, array( 'type' => 'hidden', 'value' => 'true' ) );
		
		// Validate form if it's submitted
		if ( $this->is_submitted() ) {
			
			if ( $this->form_is_valid() ) {
				
				$this->handle();
			}
		}
	}
    
    
    
	/**
	 * Add multiple fields at once
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$fields		array		Multidimensional array of fields to be added
	 */
    public function add_fields( $fields = array() ) {
        
        foreach( $fields as $field => $properties ) {
            
            $this->add_field( $field, $properties );
        }
    }
    
    
    
	/**
	 * Add single field
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field		string		Name of the field
	 * @param	$properties	array		Field properties
	 */
    public function add_field( $field = '', $properties = array() ) {
        
        $field = $this->uglify( $field );
        $properties['name'] = $field;
		
		if ( isset( $properties['options'] ) ) {
			
			foreach( $properties['options'] as $option => $value ) {
				
				$option = $this->uglify( $option );
				$options[ $option ] = $value;
				$options[ $option ]['value'] = $option;
			}
			
			$properties['options'] = $options;
		}
		
        $this->fields[ $field ] = $properties;
    }
	
	
	
	/**
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field		string
	 * @return	$return		boolean		true, if field is required
	 */
	public function is_required( $field = '' ) {
		
		$field = $this->uglify( $field );
		
		if ( isset( $this->fields[ $field ][ 'required' ] ) ) {
			
			if ( $this->fields[ $field ][ 'required' ] === true ) {
				
				$return = true;
			} else {
				
				$return = false;
			}
		} else {
			$return = false;
		}
		
		return $return;
	}
	
	
	
	/**
	 * Checks if form is submitted. Pass $field to test if single field is submitted.
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field		string		Optional
	 * @return	$return		boolean		True, if submitted
	 */
	public function is_submitted( $field = false ) {
		
		// Check if field is submitted
		if ( $field ) {
			
			$field = $this->uglify( $field );
		
			if ( isset( $_POST[ $field ] ) && !empty( $_POST[ $field ] ) ) {
				
				$return = true;
			} else {
				
				$return = false;
			}
		
		return $return;
		} 
		
		// Check if form is submitted
		if ( isset( $_POST[ $this->submit_key ] ) ) {
			
			$return = true;
		} else {
			
			$return = false;
		}
		
		return $return;
	}
	
    
    
	/**
	 * Remove multiple fields at once
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$fields		array	Names of all fields to be removed
	 */
    public function remove_fields( $fields = array() ) {
        
        foreach ( $fields as $field ) {
            
            $this->remove_field( $field );
        }
    }
    
    
    
	/**
	 * Remove single field
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field		string	Name of the field to be removed
	 */
    public function remove_field( $field = '' ) {
        
        $field = $this->uglify( $field ); 
        
        if ( $this->fields[ $field ] ) {
            
            unset( $this->fields[ $field ] );
        }
    }
    
	
	
	/**
	 * @author	kristianb
	 * @since	1.0.0
	 */
	private function uglify( $string = false ) {
		
		if ( $string ) {
			
			$search 	= array( 'ä', 'ö', 'å', ' ' );
			$replace	= array( 'a', 'o', 'a', '-' );
			$lowercased	= strtolower( $string );
			
			return str_replace ( $search, $replace, $lowercased );
	
		} else {
			
			return false;
		}
	}
	
	
	
	/**
	 *	Validates form
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 */
	public function validate_form() {
		
		if ( $this->is_submitted() ) {
			
			foreach( $this->fields as $field => $properties ) {
				
				$this->validate_field( $field );		
			}
		} 
	}
	
	
	
	/**
	 * Validate field and store user input as field's default value
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field			string		Name of the field to be validated
	 * @return	$return			boolean		True, if field is valid
	 */
	public function validate_field( $field = '' ) {
		
		$field = $this->uglify( $field );
		
		if ( $this->is_submitted( $field ) ) {
			
			$user_input = $_POST[ $field ];
			$this->set_field_default( $field, $user_input );
		}
				
		if ( $this->is_required( $field ) && !$this->is_submitted( $field ) ) {
			
			$return = false;
			$this->fields[ $field ]['not_valid'] = true;
			$this->errors = true;	
		} else {
			
			$return = true;
		}
		
		return $return;
	}
	
	
	
	/**
	 * Set default value for single field
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @param	$field		string		Name of the field
	 * @param	$value		mixed		String for simple fields, array for checkboxes, selects and radios
	 */
	public function set_field_default( $field, $value ) {
		
		$field = $this->uglify( $field );
		
		if ( is_array( $value ) ) {
		
			foreach( $this->fields[ $field ][ 'options' ] as $option => $property ) {
				
				if ( array_key_exists( $option, $value ) ) {
					
					$this->fields[ $field ][ 'options' ][ $option ][ 'checked' ] = true;
				}
			}
			
		} else {
			
			$this->fields[ $field ][ 'value' ] = $value;
		}
	}
	
	
	
	/**
	 * Checks if form is valid or not
	 *
	 * @author	kristianb
	 * @since	1.0.0
	 * @return	$return		boolean		True, if form is valid
	 */
	public function form_is_valid()	 {
		
		$this->validate_form();
		
		if ( $this->errors === false ) {
			return true;
		} else {
			return false;
		}
	}

	
	
	/**
	 * Handles the form. Should be overwrited by child class
	 */
	public function handle() {
		
	}
	
} // End of class Form		