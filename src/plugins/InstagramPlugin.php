<?php
/* @author  kristianb
 * @since   1.0.0
 */

namespace kbwp\plugins;
use kbwp\InstagramObject;

class InstagramPlugin {
    
    // Define variables used by Object
    private $_client_id = '7e3bb2067e9b44dbbf83f35e8a849b3c',
    $_client_secret = 'e0aeb17cb0154ed3b1fa8fd363f01f60',
    $_redirect_uri = 'http://adsek.fi/asiakkaat/adwp/instagram-auth-handler.php',
    $_options_page,
    $_return_uri,
    $_admin_uri,
    $_auth_url,
    $_options,
    $_access_token,
    $_cache,
    $_code;
    
    
    /* Creates new instance of a class
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * */
    public function __construct() {
        
        $this->_options_page = 'adwp-social-media-integrations.php';
        $this->_admin_uri = admin_url( 'options-general.php?page=' . $this->_options_page );
        $this->_return_uri = esc_url( $this->_admin_uri );
        $this->_auth_url = 'https://api.instagram.com/oauth/authorize/?client_id=' . $this->_client_id . '&response_type=code&redirect_uri=' . $this->_redirect_uri . '?return_uri=' . $this->_return_uri;
        
        // If authentication process were started by user
        if ( is_admin() && isset( $_GET[ 'adwp-instagram-authentication' ] ) ) {
            $this->authenticate_with_instagram( $_GET['adwp-instagram-authentication'] );
        }
        
        if ( is_admin() && isset( $_GET[ 'adwp-instagram-logout' ] ) ) {
            $this->logout_from_instagram();
        }
        
        $this->_options = get_option( 'adwp-ig' );
        
        if ( $this->_options ) {
            
            $this->_access_token = $this->_options[ 'access_token' ];
            
            $this->_user = array(
                'name'      => $this->_options[ 'username' ],
                'id'        => $this->_options[ 'user_id' ],
                'full_name' => $this->_options[ 'full_name' ]
            );
        }
        
        $this->_cache = array();
        
        add_action( 'admin_menu', array( $this, 'action_add_options_page' ) );
    }
    
    
    /* Add Options Page for plugin
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * */
    public function action_add_options_page() {
        
        add_options_page(
            __( 'ADWP Social Media Integrations', 'ADWP' ),
            __( 'ADWP Social Media', 'ADWP' ),
            'manage_options',
            $this->_options_page,
            array(
                  $this,
                  'callback_add_options_page'
            )
        );
    }
    
    
    
    /* Render options page for plugin
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * */
    public function callback_add_options_page() {
        
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'Pääsy kielletty.', 'ADWP' ) );
        }
        
        echo '<div class="wrap">';
        echo '<h1>' . __('ADWP Social Media Integrations', 'ADWP' ) . '</h1>';
        
        if ( !get_option( 'adwp-ig' ) ) {
            
            echo '<p class="warning">' . __( 'This site doesn\'t have permissions to interact with Instagram.', 'ADWP' ) . '</p>';
            echo '<p><a class="button button-primary" href="' . $this->_auth_url . '">' . __( 'Authenticate', 'ADWP' ) . '</a></p>';
            echo '<p><strong>IMPORTANT!</strong> Logging in will store your Instagram username, user ID and access token into a database for later use.<br />All information stored into a database will be deleted during the log out procedure.</p>';
            
        } else {
        
            echo '<p class="success">' . __( 'This site has now permissions to interact with Instagram as <a href="https://www.instagram.com/' . $this->_user[ 'name' ] . '" target="_blank">' . $this->_user[ 'full_name' ] . '</a>. Please log out to be able to interact with Instagram as another user.', 'ADWP' ) . '</p>';
            echo '<p><a class="button button-primary" href="' . $this->_auth_url . '">' . __( 'Re-authenticate', 'ADWP' ) . '</a><a class="button button-primary" style="margin-left: 1em;" href="' . $this->_admin_uri . '&adwp-instagram-logout=true">' . __( 'Log out', 'ADWP' ) . '</a></p>';
            echo '<p>' . __( '<strong>IMPORTANT!</strong> Logging out will permanently delete access token and all user related content from the database.', 'ADWP') . '</p>';
        }
        
        echo '</div>';   
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
    
    
    
    /* Get images by user
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * @param   $user_id    string              Defaults to user who has authenticated the plugin
     * @param   $count      int                 Defines how many images will be requested, defaults to 8
     * @param   $decode     boolean             Defines if JSON-object returned by Instagram will be decoded, default to true
     * @return  $return     object|booleand     Returns false, if request fails
     * */
    public function get_images_by_user( $user_id = 'self', $count = 8, $decode = true ) {
        
        $instagram = new InstagramObject( $this->_access_token );
        $result = $instagram->get_images_by_user( $user_id, $count, $decode );
        return $result;
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
        
        $instagram = new InstagramObject( $this->_access_token );
        $result = $instagram->get_images_by_tag( $tag_name, $count, $decode );
        return $result;
    }
    
    
    
    /* Gets Access Token from instagram
     * @author  kristianb | Adsek oy
     * @since   1.0.0
     * */
    public function authenticate_with_instagram( $code ) {
        
        $fields = array(
           'client_id'     => $this->_client_id,
           'client_secret' => $this->_client_secret,
           'grant_type'    => 'authorization_code',
           'redirect_uri'  => $this->_redirect_uri . '?return_uri=' . $this->_return_uri,
           'code'          => $code
        );
        
        $url = 'https://api.instagram.com/oauth/access_token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch,CURLOPT_POST,true); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($result);
        
        if ( isset( $result->user ) ) {
            $user = $result->user;
            
            $options = array(
                'access_token'  => $result->access_token,
                'username'      => $user->username,
                'full_name'     => $user->full_name,
                'user_id'       => $user->id
            );
            
            if ( !get_option( 'adwp-ig' ) ) {
                
                add_option( 'adwp-ig', $options );
            
            } else {
                
                update_option( 'adwp-ig', $options );
            }
            
            header('Location: ' . $this->_admin_uri );
        }
    }
    
    
    
    /* Log out from instagram and access_token
     * @author  kristianb | Adsek Oy
     * @since   1.0.0
     * */
    public function logout_from_instagram() {
        
        $delete_options = delete_option( 'adwp-ig' );
        
        if ( $delete_options ) {
            
            header('Location: ' . $this->_admin_uri );
        }
    }
}