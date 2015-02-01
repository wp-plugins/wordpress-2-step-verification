<?php
/**
Plugin Name: Wordpress 2-Step Verification
Plugin URI: http://as247.vui30.com/blog/wordpress-2-step-verification/
Description: Wordpress 2-Step Verification adds an extra layer of security to your Wordpress Account. In addition to your username and password, you'll enter a code that generated by Android/iPhone/Blackberry app or Plugin will send you via email upon signing in.
Author: As247
Version: 1.1.2
Author URI: http://as247.vui360.com/
Compatibility: WordPress 3.0
Text Domain: wp2sv
Domain Path: /languages
License: GPLv2 or later
*/
class Wordpress2StepVerification{
    /**
     * @var As247_OTP
     */
    var $otp;
    /**
     * @var Wp2sv_Auth
     */
    var $auth;
    var $user_id;
    var $user;
    var $wp2sv_email;
    /**
     * @var String
     * Mobile device name: Android, iPhone or BlackBerry
     */
    var $wp2sv_mobile;
    var $wp2sv_enabled;
    var $backup_codes;
    var $backup_codes_used;
    var $backup_codes_lock;
    var $email_sent_last;
    var $email_limit_per_day=10;
    var $error_message;
    var $wp2sv_page_menu;
    var $current_config_page;
    var $current_action;

    function __construct() {
        require_once(dirname(__FILE__).'/otp.php');
        require_once(dirname(__FILE__).'/auth.php');
        add_action( 'init', array( $this, 'init' ) );
        $this->load_text_domain('wp2sv');
        $this->set_config_page($_POST['wp2sv_page_config']);
    }
    function set_config_page($page){
        $this->current_config_page=$page;
    }
    function load_text_domain($domain){
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        $locates[]=$locale;
        if($locate_parts=explode('_',$locale)){
            $locates[]=$locate_parts[0];
        }
        $path=dirname(__FILE__).'/languages';
        foreach($locates as $locale){
            $mo_file = "$path/$locale.mo";
            if($loaded=load_textdomain($domain, $mo_file))
                return $loaded;
        }
        return false;
    }
    function init(){
        
        $this->otp=new As247_OTP($this);
        $this->auth=new Wp2sv_Auth($this);
        $this->user = wp_get_current_user();
        $this->user_id=$user_id=$this->user->ID;
        if($user_id){
            $this->init_user_data();
        }
        //if(is_admin())
            $this->handle();
        add_action('admin_menu',array($this,'user_menu_add'));
        add_action('wp_logout',array($this->auth,'clear_cookie'));
        add_action('wp_ajax_wp2sv',array($this,'ajax'));
        add_action( 'profile_personal_options', array( $this, 'profile_personal_options' ) );
        add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
        add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );
        $this->save_data();
    }
    function init_user_data(){
        //echo 'init user data';
        $this->wp2sv_enabled=get_user_meta($this->user_id,'wp2sv_enabled',true);
        $this->wp2sv_email=get_user_meta($this->user_id,'wp2sv_email',true);
        $this->wp2sv_mobile=get_user_meta($this->user_id,'wp2sv_mobile',true);
        $this->setup_user_secret();
    }
    function setup_user_secret($user_id=0){
        if(!$user_id){
            $user_id=$this->user_id;
        }
        $secret_key=get_user_meta($user_id,'wp2sv_secret_key',true);
        if(!$secret_key){
            $secret_key=$this->otp->generate_secret_key();
            update_user_meta($user_id,'wp2sv_secret_key',$secret_key);

        }
        $this->otp->set_secret_key($secret_key);
    }
    function handle(){
        $this->error_message='';
        if(!$this->is_enabled())
            return;
        if($this->validate())
            return;
        if($this->is_new_day()){
            $this->cleanup_restriction();
        }
        $scale=1;
        $code=$_POST['wp2sv_code'];
        $nonce=$_POST['wp2sv_nonce'];
        $action=$_POST['wp2sv_action'];
        if($action=='cancel'){
            wp_logout();
            wp_redirect(get_bloginfo('wpurl'));
            die;
        }
        if($_POST&&!wp_verify_nonce($nonce,'wp2sv_nonce')){
            wp_die('You do not have sufficient permissions to access this page.');
        }
        if($this->get_receive_method()=='email'&&$email=$this->wp2sv_email){

            $sent=get_user_meta($this->user_id,'wp2sv_email_sent',true);

            if($action=='send-email'||($this->get_available_method()=='email'&&!$sent)){
                if($sent<$this->email_limit_per_day){
                    $sent=absint($sent);
                    if(@wp_mail($email,$this->get_email_subject(),$this->get_email_content())){
                        $sent++;
                        update_user_meta($this->user_id,'wp2sv_email_sent',$sent);
                        update_user_meta($this->user_id,'wp2sv_email_sent_success',true);
                    }else{
                        $this->error_message=__("The e-mail could not be sent.
    Possible reason: your host may have disabled the mail() function...",'wp2sv');
                    }
                }else{
                    $this->error_message=__("Total emails can send per day has reached limit!",'wp2sv');
                }
            }
            if($code && get_user_meta($this->user_id,'wp2sv_email_sent_success',true)){
                $scale=$sent+1;
                update_user_meta($this->user_id,'wp2sv_email_sent_success',false);
            }
        }

        if($code){
            if($this->get_receive_method()!='backup-codes'){
                if($this->otp->check($code,$scale)){
                    $this->code_check_ok();
                }else{
                    $this->error_message=__("The code you entered didn't verify.",'wp2sv');
                }
            }else{
                if($this->check_backup_code($code)){
                    $this->code_check_ok();
                }else{
                    $this->fail_backup_code($code);
                    $this->error_message=__("The code you entered didn't verify.",'wp2sv');
                }
            }
        }
        $this->get_enter_code_template();
        die;
    }
    function save_data(){
        if(!current_user_can('read'))
            return false;
        $save=$_POST['wp2sv_save'];
        if(!wp_verify_nonce($save,'wp2sv_save'))
            return false;
        $action=$_POST['wp2sv_action'];
        $this->current_action=$action;
        $device=$_POST['wp2sv_device_type'];
        $email=$_POST['emailAddress'];
        if($action=='enable'){
            if(!$device)
                return false;
            if($device=='email'){
                update_user_meta($this->user_id,'wp2sv_email',$email);
            }else{
                update_user_meta($this->user_id,'wp2sv_mobile',$this->get_app_name($device));
                update_user_meta($this->user_id,'wp2sv_mobile_dev',$device);
            }
            update_user_meta($this->user_id,'wp2sv_enabled','yes');
            if($_POST['trusted']){
                $this->auth->set_cookie($this->user_id,true);
                
            }else{
                $this->auth->set_cookie($this->user_id);
                
            }update_user_meta($this->user_id,'wp2sv_user_fav_trusted',$_POST['trusted']);
            wp_redirect(wp_get_referer());
        }
        if($action=='set_remember'){
            if($_POST['trusted']){
                $this->auth->set_cookie($this->user_id,1);
                
            }else{
                $this->auth->set_cookie($this->user_id);
                
            }update_user_meta($this->user_id,'wp2sv_user_fav_trusted',$_POST['trusted']);
            wp_redirect(wp_get_referer());
            exit;
        }
        if($action=='remove_mobile'){
            //update_user_meta($this->user_id,'wp2sv_mobile','');
            //update_user_meta($this->user_id,'wp2sv_secret_key','');
        }
        if($action=='change_mobile'){
            $new_device=$_POST['settings-choose-app-type-radio'];
            $this->set_config_page($new_device);
            /*if(!get_user_meta($this->user_id,'wp2sv_tmp_secret_key',true)) {
                $tmp_key=$this->otp->generate_secret_key();
                update_user_meta($this->user_id, 'wp2sv_tmp_secret_key',$tmp_key);
                $this->otp->set_secret_key($tmp_key);
            }*/
            update_user_meta($this->user_id,'wp2sv_secret_key','');
            update_user_meta($this->user_id,'wp2sv_enabled','');
            //update_user_meta($this->user_id,'wp2sv_mobile_dev',$new_device);
        }
        if($action=='remove_email'){
            update_user_meta($this->user_id,'wp2sv_email','');
        }
        if($action=='disable'){
            //update_user_meta($this->user_id,'wp2sv_secret_key','');
            update_user_meta($this->user_id,'wp2sv_enabled','');
            update_user_meta($this->user_id,'last_selected_device','');
            if($_POST['wp2sv_clear_settings']){
                $this->clear_settings();
            }
        }
        if($action=='sync-clock'){
            $this->otp->sync_time();
        }
        
        if(!$this->wp2sv_email&&!$this->wp2sv_mobile&&in_array($action,array('remove_mobile','remove_email'))){
            update_user_meta($this->user_id,'wp2sv_enabled','');
            update_user_meta($this->user_id,'last_selected_device','');
        }
        $this->init_user_data();
        return true;
        
    }
    function clear_settings(){
        update_user_meta($this->user_id,'wp2sv_enabled','');
        update_user_meta($this->user_id,'wp2sv_email','');
        update_user_meta($this->user_id,'wp2sv_mobile','');
        update_user_meta($this->user_id,'wp2sv_mobile_dev','');
        update_user_meta($this->user_id,'wp2sv_secret_key','');
        update_user_meta($this->user_id,'wp2sv_user_fav_trusted','');
        update_user_meta($this->user_id,'last_selected_device','');
    }
    function get_app_name($device){
        switch($device){
            case 'android':
                $name='Android';
                break;
            case 'iphone':
                $name='iPhone';
                break;
            case 'blackberry':
                $name='BlackBerry';
            break;
            default:
                $name='';
        }
        return $name;
    }
    function is_configuring(){
        return (bool)$this->current_config_page;
    }
    function configuring_device(){
        $device=$this->get_app_name($this->get_current_page_config_name());
        if(!$device){
            $device=__('mobile','wp2sv');
        }
        return $device;
    }
    function save_key(){
        echo wp_create_nonce('wp2sv_save');
    }

    function code_check_ok(){
        $remember=$_POST['wp2sv_remember'];
        $this->cleanup_restriction();
        $this->auth->set_cookie($this->user_id,$remember);
        wp_redirect(wp_get_referer());
        die;
    }
    function cleanup_restriction(){
        update_user_meta($this->user_id,'wp2sv_email_sent',0);
    }
    function get_backup_codes(){
        $backup_codes=get_user_meta($this->user_id,'wp2sv_backup_codes',true);
        return $backup_codes;
    }
    function the_backup_codes(){
        
    }
    function check_backup_code($code){
        $backup_codes=$this->get_backup_codes();
        if(in_array($code,$backup_codes)){
            return true;
        }
        return false;
    }
    function generate_backup_codes(){
        
    }
    function fail_backup_code($code){
        
    }
    function get_receive_method(){
        $method=$_POST['wp2sv_type'];
        if(in_array($method,array('email','mobile','backup-codes'))){
            return $method;
        }
        if($this->wp2sv_mobile){
            return 'mobile';
        }
        if($this->wp2sv_email){
            return 'email';
        }
        return 'mobile';
    }
    function get_available_method(){
        if($this->wp2sv_mobile){
            return 'mobile';
        }
        if($this->wp2sv_email){
            return 'email';
        }
        return false;
    }
    function get_email_ending(){
        $email=$this->wp2sv_email;
        $end=substr($email,strpos($email,'@')-1);
        return $end;
    }
    function has_email(){
        return (bool)$this->wp2sv_email;
    }
    function get_enter_code_template(){
        $template_file='wp2sv.php';
        $templates=array(TEMPLATEPATH.'/'.$template_file,dirname(__FILE__).'/template/'.$template_file);
        foreach($templates as $template_file){
            if(file_exists($template_file)){
                include($template_file);
                return;
            }
                
        }
        return ;
    }
    function validate(){
        return $this->user_id==$this->auth->validate_cookie();
    }
    function wp2sv_user_fav_trusted(){
        return $this->auth->is_trusted();

    }
    function enqueue_scripts(){
        wp_enqueue_script( 'wp2sv_js',plugins_url('/wp2sv.js',__FILE__),array(),'1.1',true );
        wp_enqueue_style( 'wp2sv_css',plugins_url('/style.css',__FILE__) );
    }
    function user_menu_add(){
        
        $page=add_users_page( __('Wordpress 2-step verification','wp2sv'), __('2-Step Verification','wp2sv'), 'read', 'wp2sv', array($this,'config_page'));
        $this->wp2sv_page_menu=$page;
        add_action('admin_print_styles-' . $page, array($this,'enqueue_scripts'));
        add_action('admin_head-'.$page,array($this,'header'));
        add_action('admin_bar_menu',array($this,'admin_bar'),9);
    }

    /**
     * @param WP_Admin_Bar $wp_admin_bar
     * @return void
     */
    function admin_bar($wp_admin_bar){
        $wp_admin_bar->remove_menu('logout');
        $wp_admin_bar->add_menu( array(
    		'parent' => 'user-actions',
    		'id'     => '2-step-verification',
    		'title'  => __( '2-Step Verification' , 'wp2sv' ),
    		'href' => menu_page_url('wp2sv',false),
        ) );
        $wp_admin_bar->add_menu( array(
    		'parent' => 'user-actions',
    		'id'     => 'logout',
    		'title'  => __( 'Log Out' ),
    		'href'   => wp_logout_url(),
    	) );
    }
    function config_page(){
        
        
        include(dirname(__FILE__).'/page-config.php');
    }
    function get_current_page_config_name(){
        $allow_pages=array('all','overview','android','iphone','blackberry','email','backup');
        $current_page=$this->current_config_page;
        if(!$current_page){
            $current_page='overview';
        }
        //$last_page=get_user_meta($this->user_id,'last_selected_device',true);
        if($this->wp2sv_mobile&&$current_page=='auto'){
            $mobidev=get_user_meta($this->user_id,'wp2sv_mobile_dev',true);
            $current_page=$mobidev;
        }
        if(!in_array($current_page,$allow_pages)){
            //$current_page=$last_page;
        }
        if(!in_array($current_page,$allow_pages)){
            $current_page='all';
        }
        return $current_page;
    }
    function last_page_selected($page){
        $last_page=get_user_meta($this->user_id,'last_selected_device',true);
        selected($page,$last_page);
    }
    function get_current_page_config(){
        $current_page=$this->get_current_page_config_name();
        if($current_page!='overview')
            $current_page='all';
        $page_file=dirname(__FILE__).'/page-config-'.$current_page.'.php';
        if(file_exists($page_file)){
            include $page_file;
        }
    }

    function personal_options_update($user_id){
        
    }
    function is_new_day(){
        $today=date('Y-m-d',current_time('timestamp'));
        if($today!=get_user_meta($this->user_id,'wp2sv_lastday',true)){
            update_user_meta($this->user_id,'wp2sv_lastday',$today);
            return true;
        }
        return false;
    }
    function status($user_id=null){
        echo $this->get_status($user_id);
    }
    function get_status($user_id=null){
        if($this->is_enabled($user_id)){
            return __('ON','wp2sv');
        }else{
            return __('OFF','wp2sv');
        }
    }
    function is_enabled($user_id=null){
        if($user_id===null)
            $user_id=$this->user_id;
        $status=get_user_meta($user_id,'wp2sv_enabled',true);
        return $status=='yes';
    }
    function remove_confirm(){
        $message=__('Removing this data will turn off 2-step verification. Are you sure you want to delete this information?','wp2sv');
        if($this->wp2sv_email&&$this->wp2sv_mobile){
            $message='';
        }
        echo $message;
    }
    function change_confirm(){
        $message=__('This will temporary turn off 2-step verification and take you to setup page','wp2sv');
        echo $message;
    }
    function header(){
        echo '<script type="text/javascript">';
        echo 'var wp2sv_url=\'';$this->plugin_url();echo "'";
        //echo 'var wp2sv_remove_confirm_mess=\'';;echo "'";
        echo '</script>';
    }
    function plugin_url($path=''){
        echo plugins_url($path,__FILE__);
    }
    function ajax(){
        if(!current_user_can('read'))
            return false;
        $action=$_REQUEST['wp2sv_action'];
        $result=array();
        switch($action){
            case 'check':
                if(is_email($_REQUEST['email'])){
                    $result=array('result'=>'success');
                }else{
                    $result=array('result'=>'error','message'=>__('Email is invalid','wp2sv'));
                }
            break;
            case 'send_mail':
                $email=$_REQUEST['email'];
                if(@wp_mail($email,$this->get_email_subject(),$this->get_email_content())){
                    $result=array('result'=>'success','message'=>__('Code sent.','wp2sv'));
                }else{
                    $result=array('result'=>'error','message'=>'<div class="w2sverror">'.__('The e-mail could not be sent','wp2sv').'</div>');
                }
            break;
            case 'verify_code':
                $code=$_REQUEST['code'];
                $scale=$_REQUEST['is_email']?4:1;
                if($this->otp->check($code,$scale)){
                    $result=array('result'=>'success','message'=>'<p>'.__('Your device is configured.','wp2sv').' </p>
<p class="last verify-success-click-next-message">'.__('Click Next to continue.','wp2sv').' </p>');
                }else{
                    $result=array(
                        'result'=>'error',
                        'message'=>'<div class="w2sverror">'.__('The code is incorrect. Try again.','wp2sv').'</div>',
                    );
                }
            break;
            case 'device_type_choice':
                $device=$_REQUEST['device-type'];
                $result=update_user_meta($this->user_id,'last_selected_device',$device);
            break;
            case 'time_sync':
                $this->otp->sync_time();
                $result=array('server_time'=>$this->otp->time(),'local_time'=>$this->otp->local_time());
                break;
        }
        echo json_encode($result);
        die;
    }
    function chart_url($w=166,$h=166){
        $secret=$this->otp->get_secret_key();
        $display=$this->user->user_login;
        $name=parse_url(get_bloginfo('wpurl'),PHP_URL_HOST);
        //$name='As247';
        $display=$name.'%3A'.$display;
        $secret_url=sprintf("otpauth://totp/%s?secret=%s&issuer=%s",$display,$secret,$name);
        $secret_url=urlencode($secret_url);
        $chart_url=sprintf("https://chart.googleapis.com/chart?chs=%sx%s&chld=L|0&cht=qr&chl=%s",$w,$h,$secret_url);
        echo $chart_url;
    }
    function secret_key(){
        $secret=$this->otp->get_secret_key();
        $secret_arr=str_split($secret,4);
        //array_unshift($secret_arr,substr($secret,0,1));
        $secret_str=implode("\n",$secret_arr);
        echo $secret_str;
    }
    function get_email_subject(){
        return __('Your verification code','wp2sv');
    }
    function get_email_content(){
        $code=$this->otp->generate();
        $code=$code[1];
        return sprintf(__('Your verification code is %s','wp2sv'),$code);
    }
    function edit_user_profile_update($user_id){
        if(!current_user_can('edit_users'))
            return false;
        if($_POST['wp2sv-turn-off']){
            update_user_meta($user_id,'wp2sv_enabled','');
        }
        return true;
    }
    function edit_user_profile($user){
        ?>
        <h3><?php _e('2-Step Verification','wp2sv');?></h3>
        <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php _e('Status:','wp2sv');?></th>
                <td><label><?php $this->status($user->ID);?></label>
                <?php if($this->is_enabled($user->ID)):?>
                   <input type="hidden" name="wp2sv-turn-off" id="wp2sv-turn-off"/>
                    <input type="button" class="button" id="wp2sv-turn-off-button" value="<?php _e('Turn off 2-step verification','wp2sv');?>"/>
                
                <?php else:
                ?>
                <span class="description">
                <?php
                _e('Only the user can turn on 2-step verification','wp2sv');
                ?>
                </span>
                <?php
                endif;?>
            </tr>
        </tbody>
        </table>
        <script type="text/javascript">
            var jQuery=jQuery||{};
            jQuery('#your-profile').ready(function($){
                var turnOff;
                turnOff = $("#wp2sv-turn-off").val('');
                $("#wp2sv-turn-off-button").click(function(){
                    turnOff.val('');
                    if(!confirm('<?php _e('Are you sure to turn off 2-step verification? Only the user can turn it on again!','wp2sv')?>'))
                        return false;
                    turnOff.val('turn-off');
                    $("#submit").click();
                    return false;
                })
            });
        </script>
        <?php
    }
    function profile_personal_options(){
        ?>
        <h3><?php _e('2-Step Verification','wp2sv');?></h3>
        <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php _e('Status:','wp2sv');?></th>
                <td><label><?php $this->status();?></label>
                <a class="button" href="<?php menu_page_url('wp2sv');?>"> <?php _e('Edit','wp2sv');?> </a></td>
            </tr>
        </tbody>
        </table>
        <?php
    }
}
new Wordpress2StepVerification();
