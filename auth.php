<?php
class Wp2sv_Auth{
    var $cookie_name;
    var $trusted_cookie_name;
    function __construct(){
        $this->cookie_name='wp2sv_'.COOKIEHASH;
        $this->trusted_cookie_name='wp2sv_'.md5(get_current_user_id());
        
        if ($cookie_elements = $this->parse_cookie()){
            foreach($cookie_elements as $key=>$val){
                    $this->$key=$val;
            }
        }
        
    }
    function is_trusted(){
        return (bool)$_COOKIE[$this->trusted_cookie_name];
    }
    function validate_cookie(){
        if ( ! $cookie_elements = $this->parse_cookie() ) {
		  return false;
    	}

        //print_r($cookie_elements);
    	extract($cookie_elements, EXTR_OVERWRITE);
        
    	$expired = $expiration;
    
    	// Allow a grace period for POST and AJAX requests
    	if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
    		$expired += 3600;
    
    	// Quick check to see if an honest cookie has expired
    	if ( $expired < time() ) {
    		return false;
    	}
    
    	$user = get_user_by('login', $username);
    	if ( ! $user ) {
    		return false;
    	}
    
    	$pass_frag = substr($user->user_pass, 8, 4);
        $secret_frag=substr(md5(get_user_meta($user->ID,'wp2sv_secret_key',true)),4,4);
        $pass_frag .=$secret_frag;
    	$key = wp_hash($username . $pass_frag . '|' . $expiration, $scheme);
    	$hash = hash_hmac('md5', $username . '|' . $expiration, $key);
    
    	if ( $hmac != $hash ) {
    		return false;
    	}
    
    	if ( $expiration < time() ) // AJAX/POST grace period set above
    		$GLOBALS['login_grace_period'] = 1;
    
    	
    
    	return $user->ID;
    }
    function generate_cookie($user_id, $expiration,$remember="0",$scheme='auth'){
        $user = get_userdata($user_id);
        $remember = (bool)$remember;
    	$pass_frag = substr($user->user_pass, 8, 4);
        $secret_frag=substr(md5(get_user_meta($user_id,'wp2sv_secret_key',true)),4,4);
        $pass_frag .= $secret_frag;
    	$key = wp_hash($user->user_login . $pass_frag . '|' . $expiration, $scheme);
    	$hash = hash_hmac('md5', $user->user_login . '|' . $expiration, $key);
    
    	$cookie = $user->user_login . '|' .$remember.'|'. $expiration . '|' . $hash;
        return $cookie;
    }
    function parse_cookie(){
        $scheme='auth';
        $cookie_name=$this->cookie_name;
        if ( empty($_COOKIE[$cookie_name]) )
			return false;
		$cookie = $_COOKIE[$cookie_name];
        $cookie_elements = explode('|', $cookie);
    	if ( count($cookie_elements) != 4 )
    		return false;
        
    	list($username, $remember ,$expiration, $hmac) = $cookie_elements;
        $remember=(bool)$remember;
    	return compact('username', 'expiration', 'hmac', 'remember','scheme');
    }
    function set_cookie($user_id, $remember = false, $secure = ''){
        $remember=(bool)$remember;
        if ( $remember ) {
    		$expiration = $expire = time() + apply_filters('wp2sv_cookie_expiration', 2592000, $user_id, $remember);
    	} else {
    		$expiration = time() + apply_filters('wp2sv_cookie_expiration', 172800, $user_id, $remember);
    		$expire = 0;
    	}
        $scheme='auth';
        $secure=false;
        $auth_cookie=$this->generate_cookie($user_id,$expiration,$remember);
        //echo $auth_cookie;die;
        setcookie($this->cookie_name, $auth_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
        setcookie($this->trusted_cookie_name, $remember, time()+31536000, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
	    if ( COOKIEPATH != SITECOOKIEPATH ){
	       	setcookie($this->cookie_name, $auth_cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true);
            setcookie($this->trusted_cookie_name, $remember, time()+31536000, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
        }
    }
    function clear_cookie(){
        if ($cookie_elements = $this->parse_cookie() ) {
            if($cookie_elements['remember'])
		          return false;
    	}
        setcookie($this->cookie_name,' ',time() - 31536000,COOKIEPATH,COOKIE_DOMAIN);
        setcookie($this->cookie_name,' ',time() - 31536000,SITECOOKIEPATH,COOKIE_DOMAIN);
    }
}