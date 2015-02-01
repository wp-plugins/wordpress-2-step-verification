<?php
class As247_OTP{
    var $secret_key;
    var $wp2sv;
    var $secret_key_length=32;
    function __construct($wp2sv=''){
        if($wp2sv){
            $this->wp2sv=$wp2sv;
        }
        date_default_timezone_set('UTC');
        if(!get_option('wp2sv_time_synced')){
            update_option('wp2sv_time_synced',time());
            $this->sync_time();
        }
    }
    
    function check($otp,$scale=1,$secret=''){
        $scale=intval($scale);
        if($scale<1)$scale=1;
        $otp_pass=$this->generate($scale,$secret);
        foreach($otp_pass as $pass){
            if($otp==$pass)
                return true;
        }
        return false;
    }
    function time(){
        $wp2sv_local_diff_utc=get_option('wp2sv_local_diff_utc');
        $time=time()-$wp2sv_local_diff_utc;
        return $time;
    }
    function local_time(){
        $gmt=$this->time();
        return $gmt+( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
    }
    function sync_time(){
        $utc=$this->get_internet_time();
        $last_sync=get_option('wp2sv_time_synced');
        $last_sync=intval($last_sync);
        if((time()-$last_sync)<60){
            return false;
        }
        update_option('wp2sv_time_synced',time());
        if($utc) {
            $wp2sv_local_diff_utc = time() - $utc;
            update_option( 'wp2sv_local_diff_utc', $wp2sv_local_diff_utc );
            return true;
        }
        return false;
    }
    function get_internet_time(){
        $time_stamp=wp_remote_get('http://www.timeanddate.com/scripts/ts.php');
        if(!is_object($time_stamp)){
            $time_stamp=$time_stamp['body'];
            $time_stamp=explode(' ',$time_stamp);
            $time_stamp=$time_stamp[0];
        }else{
            return 0;
        }
        $time_stamp=(int)$time_stamp;
        return $time_stamp;
    }
    function generate($scale=1,$secret_key=''){
        $scale=abs(intval($scale));
		$from = -$scale;
		$to =  $scale; 
    	$timer = floor( $this->time() / 30 );
    	$this->set_secret_key($secret_key);
    	$secret_key=$this->get_decoded_secret_key();
        $result=array();
    	for ($i=$from; $i<=$to; $i++) {
    		$time=chr(0).chr(0).chr(0).chr(0).pack('N*',$timer+$i);
    		$hm = hash_hmac( 'SHA1', $time, $secret_key, true );
    		$offset = ord(substr($hm,-1)) & 0x0F;
    		$hashpart=substr($hm,$offset,4);
    		$value=unpack("N",$hashpart);
    		$value=$value[1];
    		$value = $value & 0x7FFFFFFF;
    		$value = $value % 1000000;
    		$result[]=$value;	
    	}
    	return $result;
    }
    function get_secret_key(){
        
        return strtolower($this->secret_key);
    }
    function get_decoded_secret_key(){
        return $this->base32_decode($this->get_secret_key());
    }
    function set_secret_key($key){
        if(!$key){
            return ;
        }
        $this->secret_key=$key;
    }
    function generate_secret_key(){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // allowed characters in Base32
        $secret = '';
        for ( $i = 0; $i < $this->secret_key_length; $i++ ) {
            $secret .= substr( $chars, rand( 0, strlen( $chars ) - 1 ), 1 );
        }
        return $secret;
    }
    function base32_decode($input){
        $input=strtoupper($input);
        $keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567="; 
        $buffer = 0;
        $bitsLeft = 0;
        $output = array();
        $i = 0;
        $count = 0;
        $stop=strlen($input);
        while ($i < $stop) {
            $val =$input{$i++};
            $val=strpos($keyStr,$val);
            if ($val >= 0 && $val < 32) {
                $buffer <<= 5;
                $buffer |= $val;
                $bitsLeft += 5;
                if ($bitsLeft >= 8) {
                    $output[$count++] = ($buffer >> ($bitsLeft - 8)) & 0xFF;
                    $bitsLeft -= 8;
                }
            }
        }
        if ($bitsLeft > 0) {
            $buffer <<= 5;
            $output[$count++] = ($buffer >> ($bitsLeft - 3)) & 0xFF;
        }
        $output=array_map('chr',$output);
        $output=implode('',$output);
        return $output;
    }
    function is64bit(){
        if(PHP_INT_SIZE==8){
            return true;
        }
        return false;
    }
}