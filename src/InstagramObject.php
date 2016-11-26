<?php
/*
 * @author  kristianb
 * @since   1.0.0
 */

namespace kbwp;

class InstagramObject {
    
    private $_api,
    $_access_token,
    $_cache;
    
    public function __construct( $_access_token ) {
     
        $this->_api = 'https://api.instagram.com/v1';
        $this->_access_token = $_access_token;
    }
    
    public function get_images_by_user( $user_id = 'self', $count = 8, $decode = true ) {
        
        if ( !isset( $this->_cache[ 'get_images_by_user_' . $user_id ] ) ) {
            
            $url = $this->_api . '/users/' . $user_id . '/media/recent/?access_token=' . $this->_access_token . '&count=' . $count;
            $return = $this->get_from_instagram( $url, $decode );
            $this->_cache[ 'get_images_by_user_' . $user_id ] = $return;
        }
        
        return $this->_cache[ 'get_images_by_user_' . $user_id ];
    }
    
    
    /* Get images by tag
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * @param   $tag_name   string              Tag which will be requested
     * @param   $count      int                 Defines how many images will be requested, defaults to 8
     * @param   $decode     boolean             Defines if JSON-object returned by Instagram will be decoded, default to true
     * @return  $return     object|booleand     Returns false, if request fails
     * */
    public function get_images_by_tag( $tag_name, $count = 8, $decode = true ) {
        
        if ( !isset( $this->_cache[ 'get_images_by_tag_' . $tag_name ] ) ) {
            
            $url = $this->_api . '/tags/' . strtolower( $tag_name ) . '/media/recent/?access_token=' . $this->_access_token . '&count=' . $count . '&scope=public_content';
            print_r( $url );
            $return = $this->get_from_instagram( $url, $decode );
            $this->_cache[ 'get_images_by_tag_' . $tag_name ] = $return;
        }
        
        return $this->_cache[ 'get_images_by_tag_' . $tag_name ];
    }
    
    
    /* Request data from instagram
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * $param   $request_uri    string          Request url as a string
     * $param   $json_decode    boolean         Decodes json if true, defaults true
     * $return  $result         object|boolean  False, if request fails
     * */
    private function get_from_instagram( $request_url, $json_decode = true ) {
        
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $request_url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
        $result = curl_exec( $ch );
        
        if ( $json_decode === true ) {
            $result = json_decode( $result );
        }
        
        curl_close( $ch);
        
        if ( $result ) {
            
            return $result;
            
        } else {
            
            return false;   
        }
    }
    
    
    /* Destroys object
     * author   kristianb
     * since    1.0.0
     * */
    public function __destruct() {
        
        $this->_access_token = null;
    }
}