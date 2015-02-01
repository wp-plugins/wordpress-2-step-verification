/* global ajaxurl, wp2sv_url*/
jQuery('#wp2sv-config-section').ready(function($){
    var inactive_elements=$('#inactive-elements');
    var primary_email_el=$('#primary-email');
    var config_container_el=$('#config-container');
    var wp2sv_action_el=$("#wp2sv_action");
    $("#device-type").change(function(){
        var selected_config=$(this).val();
        var non_mobile_dev_el=$('.config-section:not(#mobile-device-type)');
        non_mobile_dev_el.appendTo(inactive_elements);
        $('#configure-app-instructions>div').appendTo(inactive_elements);
        var wp2sv_current_page_config=$("#wp2sv_current_page_config").val();
        if(wp2sv_current_page_config!='all'){
            $(".config-section").appendTo(inactive_elements);
            $("#pm-subheader").css('display','none');
            selected_config=wp2sv_current_page_config;
        }
        $("#wp2sv_device_type").val(selected_config);
        switch(selected_config){
            case 'email':
                $('#email-address').appendTo(config_container_el);
                break;
            case 'android': case 'blackberry': case 'iphone':
                $('#configure-app').appendTo(config_container_el);
                var instruction='#configure-app-'+selected_config;
                $(instruction).appendTo('#configure-app-instructions');
                break;
            
                
            default:
                non_mobile_dev_el.appendTo(inactive_elements);
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
        
    }).change();

    primary_email_el.change(function(){
        var email=$(this).val();
        var primary_phone_valid=$("#primary-phone-valid");
        primary_phone_valid.css('visibility','hidden');
        $("#primary-send-code").attr('disabled','');
        if(email){
            primary_phone_valid.attr('src',wp2sv_url+'/images/loading.gif');
            primary_phone_valid.css('visibility','inherit');
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
    primary_email_el.change().keyup(function(){
        Wp2svTyping.interceptKeypress();
    });
    $('#primary-send-code').on('click',function(){
        var send_code_icon=$(".send-code-container .icon");
        send_code_icon.attr('src',wp2sv_url+'/images/loading.gif');
        send_code_icon.css('visibility','inherit');
        $("#primary-code-sent").html("");
        $(this).attr('disabled','');
       $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'email': primary_email_el.val(), 'wp2sv_action':'send_mail' },
            success: function(data){
                if(data==null)
                    return false;
                if(data.result=='success'){
                    send_code_icon.attr('src',wp2sv_url+'/images/checkmark-g16.png');
                    send_code_icon.css('visibility','inherit');
                    var input=$("#primary-test-input");
                     input.removeClass('inactive-text');
                     input.find('input').removeAttr('disabled');
                }else{
                    send_code_icon.css('visibility','hidden');
                }
                
                $("#primary-code-sent").html(data.message);
                $('#primary-send-code').removeAttr('disabled');
                $('#primary-verify-button').focus();
                $('#primary-verify-code').focus();
            }

        });
        
       return false; 
    });
    $('#primary-verify-button').on('click',function(){
        var primary_icon=$("#primary-test-input .icon");
        primary_icon.attr('src',wp2sv_url+'/images/loading.gif');
        primary_icon.css('visibility','inherit');
        $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'code': $('#primary-verify-code').val(), 'wp2sv_action':'verify_code', 'is_email':'1' },
            success: function(data){
                if(data==null)
                    return false;
                if(data.result=='success'){
                    primary_icon.attr('src',wp2sv_url+'/images/checkmark-g16.png');
                    $("#primary-verify-container").html($('#email-verify-success'));
                    $("#next-button").removeAttr('disabled');
                     
                }else{
                    primary_icon.attr('src',wp2sv_url+'/images/warning-y16.png');
                    $("#primary-verify-container").html(data.message);
                }
                
                
            }
            

        });

        return false;
    });


    var app_verify_failed_attemp=0;
    $("#app-verify-button").on('click',function(){
        var code=$('#app-verify-code').val();
        var configure_icon=$("#configure-app .smallicon");
        var app_verify_container=$("#app-verify-container");
        configure_icon.attr('src',wp2sv_url+'/images/loading.gif');
        configure_icon.css('visibility','inherit');
        $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'code': code, 'wp2sv_action':'verify_code' },
            success: function(data){
                if(data==null)
                    return false;
                
                if(data.result=='success'){
                    $("#app-verify-failures").appendTo(inactive_elements);
                    configure_icon.attr('src',wp2sv_url+'/images/checkmark-g16.png');
                    app_verify_container.html($('#app-verify-success'));
                    $("#next-button").removeAttr('disabled');
                     
                }else{
                    app_verify_failed_attemp++;
                    configure_icon.attr('src',wp2sv_url+'/images/warning-y16.png');
                    app_verify_container.html(data.message);
                    if(app_verify_failed_attemp==3){
                        $("#app-verify-failures").appendTo(app_verify_container);
                    }
                }
                
                
            }
            

        });
       return false; 
    });
    $("#next-button").on('click',function(){
        $("#pm-subheader").css('display','none');
        var current=$("#process-map .pm-current").attr('id');
        $(".config-section").appendTo(inactive_elements);
        
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
            $('#confirm-section').appendTo(config_container_el);
        }
    });
    $("#back-button").on('click',function(){
        var current=$("#process-map .pm-current").attr('id');
        $(".config-section").appendTo(inactive_elements);
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
        $(this).closest('.manual-zippy').find('.app-instructions').removeClass('inactive');
        $(".manual-zippy img.icon").attr('src',wp2sv_url+'/images/zippy_minus_sm.gif');
    },function(){
        $(this).closest('.manual-zippy').find('.app-instructions').addClass('inactive');
        $(".manual-zippy img.icon").attr('src',wp2sv_url+'/images/zippy_plus_sm.gif');
    }
    );
    $('#activate-button').on('click',function(){
        wp2sv_action_el.val('enable');
        $("form#theform").submit();
        return false;
    });
    $('#cancel-link').on('click',function(){
        //if(confirm('Are you sure to cancel'))
        window.location.href=userSettings.url+'wp-admin/users.php?page=wp2sv';
        return false;
    });
    
   
    $('.modal-open').on('click',function(e){
        e.preventDefault();
        var modalToOpen=$(this).data('modal');
        modalToOpen='#'+modalToOpen;
        wp2sv.openModal(modalToOpen);
        return false;
    });
    $('.modal-dialog-title-close').on('click',function(e){
        e.preventDefault();
        wp2sv.closeModal(this);
        return false;
    });
    $('.modal-dialog-buttons button[name=cancel]').on('click',function(e){
        e.preventDefault();
        wp2sv.closeModal(this);
        return false;
    });
    $('#phone-change').on('click','.wp2sv-buttonset-action',function(e){
        if($('[name=settings-choose-app-type-radio]:checked').length<=0){
            e.preventDefault();
            $('#settings-no-choice-app-error').show();
            return false;
        }
        var confirm_message=$("#wp2sv_change_confirm").val();
        var result=true;
        if(confirm_message){
            result=confirm(confirm_message);
        }
        if(!result) {
            e.preventDefault();
            return false;
        }

        wp2sv_action_el.val('change_mobile');
    });
    $('[name=settings-choose-app-type-radio]').on('click',function(){
        $('#settings-no-choice-app-error').hide();
    });
    $("#remove-email-link").on('click',function(){
        confirm_message=$("#wp2sv_remove_confirm").val();
        result=true;
        if(confirm_message){
            result=confirm(confirm_message);
        }
        if(!result)
            return false;
        wp2sv_action_el.val('remove_email');
        $("#theform").submit();
        return false;
    });
    $("#wp2sv-enable-link").on('click',function(){
        $("#wp2sv_page_config").val('auto');
        wp2sv_action_el.val('enable');
        $("#theform").submit();
        return false;
    });
    $('#wp2sv-disable').on('click','.wp2sv-buttonset-action',function(){
        wp2sv_action_el.val('disable');
    });

    $("#add-android-link").on('click',function(){
        $("#wp2sv_page_config").val('android');
        $("#theform").submit();
        return false;
    });
    
    $("#add-iphone-link").on('click',function(){
        $("#wp2sv_page_config").val('iphone');
        $("#theform").submit();
        return false;
    });
    
    $("#add-blackberry-link").on('click',function(){
        $("#wp2sv_page_config").val('blackberry');
        $("#theform").submit();
        return false;
    });
    $("#add-email-link").on('click',function(){
        $("#wp2sv_page_config").val('email');
        $("#theform").submit();
        return false;
    });
    $("#edit-email-link").on('click',function(){
        $("#wp2sv_page_config").val('email');
        $("#theform").submit();
        return false;
    });
    $("#show-codes-link").on('click',function(){
        $("#printable-codes").slideDown();
        return false;
    });
    $("#sync-clock").on('click',function(e){
        e.preventDefault();
        wp2sv.updateTime($(this));
        return false;
    });
    $("#trust_computer").on('click',function(){
        wp2sv_action_el.val('set_remember');
        $("#theform").submit();
        return false;
    });
    wp2sv.setUpClock();
    wp2sv.updateTime();

    
    
});
var wp2sv=wp2sv||{};
(function(wp2sv,$){
    "use strict";
    var openedModal;
   // var vars={};
    var modalBG=$('.modal-dialog-bg');
    wp2sv.init=function(){
        wp2sv.vars={};
        wp2sv.setVars()
    };
    wp2sv.setVars=function(){
        wp2sv.vars.serverClock=$('#wp2sv-server-time');
        wp2sv.vars.localClock=$('#wp2sv-local-time');
    };
    wp2sv.openModal=function(modal){

        var ww=$(window).width();
        var wpContent=$('#wpcontent');

        var contentLeft=wpContent.offset().left+wpContent.outerWidth()-wpContent.width();

        var wwm=ww-contentLeft;
        var wh=$(window).height();
        modal=$(modal);
        var mw=modal.outerWidth();
        var mh=modal.outerHeight();
        modalBG.css('width',ww).css('height',wh).show();
        modal.css('top',(wh-mh)/2);
        modal.css('left',(wwm-mw)/2);
        modal.fadeTo(500,1,function(){
            modal.show();
        });
        openedModal=modal;
    };
    wp2sv.closeModal=function(modal){
        modalBG.hide();
        modal=modal||openedModal;
        modal=$(modal);
        modal=modal.closest('.modal-dialog');
        modal.fadeTo(500,0,function(){
            modal.hide();
        });
    };
    wp2sv.updateTime=function(link){
        if(typeof link != 'undefined')
            link.addClass('loading');
        $.ajax({
            type: "POST",
            dataType:"json",
            url: ajaxurl,
            data: { 'action': "wp2sv", 'wp2sv_action':'time_sync' },
            success: function(data){
                if(data.server_time){
                    wp2sv.vars.serverClock.data('timestamp',data.server_time).attr('data-timestamp',data.server_time);
                    wp2sv.vars.localClock.data('timestamp',data.local_time).attr('data-timestamp',data.local_time);
                    wp2sv.vars.serverClock.data('time-diff','').attr('data-time-diff','');
                    wp2sv.vars.localClock.data('time-diff','').attr('data-time-diff','');
                    if(typeof link != 'undefined')
                        link.removeClass('loading');
                }
            }

        });
    };
    wp2sv.setUpClock=function(){

        setInterval(function(){
            wp2sv.setClock(wp2sv.vars.serverClock);
            wp2sv.setClock(wp2sv.vars.localClock);
        },100);
    };
    wp2sv.setClock=function (e){
        e=$(e);
        var server=e.data('timestamp');
        if(!server){
            //console.log('no sever time');
            return false;
        }
        server=server*1000;
        var diff= e.data('time-diff');
        var local=new Date().getTime();
        if(!diff){
            diff=server-local;
            e.data('time-diff',diff);
        }
        diff=parseFloat(diff);
        var new_server=diff+local;
        //console.log('server time'+new_server);
        var timeString=wp2sv.timeConverter(new_server);
        if(timeString!= e.html()) {
            e.html(timeString);
        }
    };


    wp2sv.timeConverter=function(UNIX_timestamp){
        var a = new Date(UNIX_timestamp);
        var year = a.getUTCFullYear();
        var month = a.getUTCMonth()+1;
        var date = a.getUTCDate();
        var hour = a.getUTCHours();
        var min = a.getUTCMinutes();
        var sec = a.getUTCSeconds();
        month=this.checkTime(month);
        date=this.checkTime(date);
        min=this.checkTime(min);
        sec=this.checkTime(sec);
        return ( year + '-' + month + '-' + date + ' ' + hour + ':' + min + ':' + sec );

    };
    wp2sv.checkTime=function(i) {
        if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
        return i;
    };
    wp2sv.init();



})(wp2sv,jQuery);
//wp2sv.openModal('.modal-dialog');
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

};