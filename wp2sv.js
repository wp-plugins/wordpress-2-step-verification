jQuery('#wp2sv-config-section').ready(function($){
    $("#device-type").change(function(){
        var selected_config=$(this).val();
        $('.config-section:not(#mobile-device-type)').appendTo($('#inactive-elements'));
        $('#configure-app-instructions>div').appendTo($('#inactive-elements'));
        var wp2sv_current_page_config=$("#wp2sv_current_page_config").val();
        if(wp2sv_current_page_config!='all'){
            $(".config-section").appendTo($('#inactive-elements'));
            $("#pm-subheader").css('display','none');
            selected_config=wp2sv_current_page_config;
        }
        $("#wp2sv_device_type").val(selected_config);
        switch(selected_config){
            case 'email':
                $('#email-address').appendTo($('#config-container'));
                break;
            case 'android': case 'blackberry': case 'iphone':
                $('#configure-app').appendTo($('#config-container'));
                var instruction='#configure-app-'+selected_config;
                $(instruction).appendTo('#configure-app-instructions');
                break;
            
                
            default:
                $('.config-section:not(#mobile-device-type)').appendTo($('#inactive-elements'));
                break;
        }
        if(selected_config)
            $.ajax({
                type: "POST",
                dataType:"json",
                url: ajaxurl,
                data: { 'action': "wp2sv", 'device-type': selected_config, 'wp2sv_action':'device_type_choice' },
                success: function(data){
                    
                }
    
            });
        
    });
    $("#device-type").change();
    $('#primary-email').change(function(){
        var email=$(this).val();
        $("#primary-phone-valid").css('visibility','hidden');
        $("#primary-send-code").attr('disabled','');
        if(email){
            $("#primary-phone-valid").attr('src',wp2sv_url+'/images/loading.gif');
            $("#primary-phone-valid").css('visibility','inherit');
            $("#primary-error").addClass('inactive');
            $.ajax({
                type: "POST",
                dataType:"json",
                url: ajaxurl,
                data: { 'action': "wp2sv", 'email': email, 'wp2sv_action':'check' },
                success: function(data){
                    if(data.result=='success'){
                         $("#primary-phone-valid").attr('src',wp2sv_url+'/images/checkmark-g16.png');
                         $("#primary-send-code").removeAttr('disabled');
                         
                    }else{
                        $("#primary-phone-valid").css('visibility','hidden');
                        $("#primary-error").html(data.message).removeClass('inactive');
                    }
                }
    
            });
        }
    });
    $('#primary-email').change();
    $('#primary-email').keyup(function(){
        Wp2svTyping.interceptKeypress();
    });
    $('#primary-send-code').click(function(){
        $(".send-code-container .icon").attr('src',wp2sv_url+'/images/loading.gif');
        $(".send-code-container .icon").css('visibility','inherit');
        $("#primary-code-sent").html("");
        $(this).attr('disabled','');
       $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'email': $('#primary-email').val(), 'wp2sv_action':'send_mail' },
            success: function(data){
                if(data==null)
                    return false;
                if(data.result=='success'){
                    $(".send-code-container .icon").attr('src',wp2sv_url+'/images/checkmark-g16.png');
                     $(".send-code-container .icon").css('visibility','inherit');
                     $("#primary-test-input").removeClass('inactive-text');
                     $("#primary-test-input input").removeAttr('disabled');
                }else{
                    $(".send-code-container .icon").css('visibility','hidden');
                }
                
                $("#primary-code-sent").html(data.message);
                $('#primary-send-code').removeAttr('disabled'); 
            }

        });
        
       return false; 
    });
    $('#primary-verify-button').click(function(){
        $("#primary-test-input .icon").attr('src',wp2sv_url+'/images/loading.gif');
        $("#primary-test-input .icon").css('visibility','inherit');
        $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'code': $('#primary-verify-code').val(), 'wp2sv_action':'verify_code', 'is_email':'1' },
            success: function(data){
                if(data==null)
                    return false;
                if(data.result=='success'){
                    $("#primary-test-input .icon").attr('src',wp2sv_url+'/images/checkmark-g16.png');
                    $("#primary-verify-container").html($('#email-verify-success'));
                    $("#next-button").removeAttr('disabled');
                     
                }else{
                    $("#primary-test-input .icon").attr('src',wp2sv_url+'/images/warning-y16.png');
                    $("#primary-verify-container").html(data.message);
                }
                
                
            }
            

        });

        return false;
    });
    var app_verify_failed_attemp=0;
    $("#app-verify-button").click(function(){
        var code=$('#app-verify-code').val();
        $("#configure-app .smallicon").attr('src',wp2sv_url+'/images/loading.gif');
        $("#configure-app .smallicon").css('visibility','inherit');
        $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'code': code, 'wp2sv_action':'verify_code', },
            success: function(data){
                if(data==null)
                    return false;
                
                if(data.result=='success'){
                    $("#app-verify-failures").appendTo($('#inactive-elements'));
                    $("#configure-app .smallicon").attr('src',wp2sv_url+'/images/checkmark-g16.png');
                    $("#app-verify-container").html($('#app-verify-success'));
                    $("#next-button").removeAttr('disabled');
                     
                }else{
                    app_verify_failed_attemp++;
                    $("#configure-app .smallicon").attr('src',wp2sv_url+'/images/warning-y16.png');
                    $("#app-verify-container").html(data.message);
                    if(app_verify_failed_attemp==3){
                        $("#app-verify-failures").appendTo($("#app-verify-container"));
                    }
                }
                
                
            }
            

        });
       return false; 
    });
    $("#next-button").click(function(){
        $("#pm-subheader").css('display','none');
        var current=$("#process-map .pm-current").attr('id');
        $(".config-section").appendTo($('#inactive-elements'));
        
        if(current=='pm-generate'){
            $("#pm-generate").removeClass().addClass('pm-complete');
            $("#pm-remember").removeClass().addClass('pm-current');
            $("#back-button").removeAttr('disabled');
            $("#remember-computer-state").appendTo('#config-container');
        }
        if(current=='pm-remember'){
            $("#pm-remember").removeClass().addClass('pm-complete');
            $('#pm-confirm').removeClass().addClass('pm-current');
            $(this).addClass('hidden');
            $('#activate-button').css('display','');
            $('#confirm-section').appendTo($('#config-container'));
        }
    });
    $("#back-button").click(function(){
        var current=$("#process-map .pm-current").attr('id');
        $(".config-section").appendTo($('#inactive-elements'));
        if(current=='pm-confirm'){
            $("#pm-confirm").removeClass().addClass('pm-incomplete');
            $("#pm-remember").removeClass().addClass('pm-current');
            $("#next-button").removeClass('hidden');
            $('#activate-button').css('display','none');
            $("#remember-computer-state").appendTo('#config-container');
        }
        if(current=='pm-remember'){
            $("#pm-remember").removeClass().addClass('pm-incomplete');
            $('#pm-generate').removeClass().addClass('pm-current');
            $(this).attr('disabled','');
            $("#pm-subheader").css('display','');
            $("#mobile-device-type").appendTo('#config-container');
            $('#device-type').change();
            
        }
    });
    $(".manual-zippy a").toggle(function(){
        $("#manual-content-android").removeClass('inactive');
        $(".manual-zippy img.icon").attr('src',wp2sv_url+'/images/zippy_minus_sm.gif');
    },function(){
        $("#manual-content-android").addClass('inactive');
        $(".manual-zippy img.icon").attr('src',wp2sv_url+'/images/zippy_plus_sm.gif');
    }
    );
    $('#activate-button').click(function(){
        $("#wp2sv_action").val('enable');
        $("form#theform").submit();
        return false;
    })
    $('#cancel-link').click(function(){
        //if(confirm('Are you sure to cancel'))
        window.location.href=userSettings.url+'wp-admin/users.php?page=wp2sv';
        return false;
    });
    
    $("#remove-app-link").click(function(){
        confirm_message=$("#wp2sv_remove_confirm").val();
        result=true;
        if(confirm_message){
            result=confirm(confirm_message);
        }
        if(!result)
            return false;
        $("#wp2sv_action").val('remove_mobile');
        $("#theform").submit();
        return false;
    });
    $("#remove-email-link").click(function(){
        confirm_message=$("#wp2sv_remove_confirm").val();
        result=true;
        if(confirm_message){
            result=confirm(confirm_message);
        }
        if(!result)
            return false;
        $("#wp2sv_action").val('remove_email');
        $("#theform").submit();
        return false;
    });
    $("#wp2sv-enable-link").click(function(){
        $("#wp2sv_page_config").val('auto');
        $("#wp2sv_action").val('enable');
        $("#theform").submit();
        return false;
    });
    $("#wp2sv-disable-link").click(function(){
        $("#wp2sv_action").val('disable');
        $("#theform").submit();
        return false;
    });
    $("#add-android-link").click(function(){
        $("#wp2sv_page_config").val('android');
        $("#theform").submit();
        return false;
    });
    
    $("#add-iphone-link").click(function(){
        $("#wp2sv_page_config").val('iphone');
        $("#theform").submit();
        return false;
    });
    
    $("#add-blackberry-link").click(function(){
        $("#wp2sv_page_config").val('blackberry');
        $("#theform").submit();
        return false;
    });
    $("#add-email-link").click(function(){
        $("#wp2sv_page_config").val('email');
        $("#theform").submit();
        return false;
    });
    $("#edit-email-link").click(function(){
        $("#wp2sv_page_config").val('email');
        $("#theform").submit();
        return false;
    });
    $("#show-codes-link").click(function(){
        $("#printable-codes").slideDown();
        return false;
    });
    $("#sync-clock").click(function(){
        $("#wp2sv_action").val('sync-clock');
        $("#theform").submit();
        return false;
    })
    $("#trust_computer").click(function(){
        $("#wp2sv_action").val('set_remember')
        $("#theform").submit();
        return false;
    })
    
    
});
function setRememberCookieTypeFromCheckbox(){
}
Wp2svTyping = {

   interval : 750,

   lastKeypress : null,

   interceptKeypress : function() {
      this.lastKeypress = new Date().getTime();
      var that = this;
      setTimeout(function() {
         var currentTime = new Date().getTime();
         if(currentTime - that.lastKeypress > that.interval) {
            that.sendRequest();
         }
      }, that.interval + 100);
   },

   sendRequest : function() {
      jQuery('#primary-email').change();
   }

}