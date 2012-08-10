
            <h1>
        Set up 2-step verification for <span class="bolder"><?php _e($this->user->display_name);?></span>
        </h1>
        <ol class="process-map" id="process-map">
        <li class="pm-current" id="pm-generate">
        Set up your phone
        </li>
        <li class="pm-incomplete" id="pm-remember">
        Verify computer
        </li>
        <li class="pm-incomplete" id="pm-confirm">
        Activate
        </li>
        </ol>
            <div id="config-container"><div id="pm-subheader"><div class="pm-subheader" id="generate-header">
  Choose how you'd like to get verification codes &mdash; in an email,
  or from an application on your smartphone.
  </div></div>
                <div class="config-section" id="mobile-device-type">
                  <div class="icon-content  icon-primaryphone" id="mobile-device-icon">
                  <select name="deviceType" id="device-type">
                  <option value="">Choose one:</option>
                  <optgroup label="Smartphone application">
                      <option value="android">
                      Android
                      </option>
                      <option value="blackberry">
                      BlackBerry
                      </option>
                      <option value="iphone">
                      iPhone
                      </option>
                  </optgroup>
                  <optgroup label="Email">
                      <option value="email">
                        Email
                      </option>
                  </optgroup>
                  
                  </select>
                  </div>
                </div>
                
                
            </div>
        <div id="button-container">
  <input type="button" id="back-button" value="« Back" name="Back" disabled=""/>
  <span class="button-separator"></span>
  <input type="button" id="next-button" value="Next »" name="Next" disabled=""/>
  <span id="activate-button" class="g-button-activate" style="display: none">
  <a id="submit-button" style="text-decoration:none; color:white;" href="#">
  TURN ON 2-STEP VERIFICATION
  </a>
  </span>
  <span id="skip-step-container"><a href="#" id="skip-step" class="inactive">Skip this step</a></span>
  <a href="#" id="cancel-link">Cancel</a>
  <input type="hidden" name="Cancel">
  <input type="hidden" value="primaryphone" name="WizardState">
  </div>
        <div id="inactive-elements" class="inactive">
  
  <div class="config-section" id="email-address">
  <div class="heading">
  Add an email address where Wordpress 2-step verification can send codes.
  </div>
  <div class="phone-widget" id="primary-phone-widget">
  <table>
  <tbody><tr>
  <td>

  </td>
  <td></td>
  <td class="device-address" id="primary-email-address-location">
    <input type="text" value="<?php _e($this->wp2sv_email);?>" id="primary-email" name="emailAddress" dir="ltr"></td>
  <td class="primary-phone">
  
  &nbsp;
  <img src="<?php $this->plugin_url();?>/images/loading.gif" class="smallicon" id="primary-phone-valid" alt="" style="visibility: inherit;">
  </td>
  <td class="phone-usage-message primary-phone">
  <div>
  Enter your email address.
  </div>
  </td>
  </tr>
  <tr>
  <td></td>
  <td></td>
  <td class="primary-phone">
  <div class="example " id="primary-example-container">
  ex: <span dir="ltr" id="primary-example-number">example@domain.com</span>
  </div>
  </td>
 
  </tr>
  </tbody></table>
<div class="inactive w2sverror" id="primary-error"></div>
  
  
  </div>
  
  <div class=" " id="primary-number-test">
  <div id="primary-test-heading" class="heading">
  Let's test the email.
  </div>
  <div id="primary-verify-inputs" class="border-box phone-test">
  <ol class="phone-test-steps">
  <li><div class="ml-list-item">
  Click "Send code" and check your email for the verification code.
  <div class="send-code-container">
  <input type="submit" name="SendCode" value="Send code" id="primary-send-code" disabled=""/>
  <span class="box">
  <img style="visibility: hidden;" class="icon smallicon" alt="" src="<?php $this->plugin_url();?>/images/loading.gif">
  <div id="primary-code-sent" class="smallicon-content"></div>
  </span>
  </div>
  </div></li>
  <li class=" inactive-text" id="primary-test-input"><div class="ml-list-item">
  Enter the code you receive on your email.
  <div>
  <div class="verify-code-widget">
  <label for="primary-verify-code">
  Code:
  </label>
  <input type="text" disabled="" size="6" dir="ltr" id="primary-verify-code" name="verifyPin" autocomplete="off">&nbsp;
  <input type="submit" disabled="" value="Verify" id="primary-verify-button" name="VerifyPhone">
  </div>
  <img style="visibility: hidden;" class="icon smallicon" alt="" src="<?php $this->plugin_url();?>/images/loading.gif"><div id="primary-verify-container" class="smallicon-content"></div>
  </div>
  </div></li>
  </ol>
  </div>
  </div>
  
  </div>
  
  <div id="remember-computer-state" class="config-section">
  <div class="remember-heading">
  Make this a <span class="trusted-computer-emphasis">trusted computer</span>?
  </div>
  <div class="remember-box">
  <p class="remember-text">
  Trusted computers only ask for verification codes once every 30 days. If you lose your phone, you might be able to access your account from a trusted computer without needing a code. We recommend that you make this a trusted computer only if you trust the people who have access to it.
  </p>
  </div>
  <label for="rememberComputerVerify">
  <input type="checkbox" id="rememberComputerVerify" name="trusted" checked="" onclick="setRememberCookieTypeFromCheckbox()"/>
  Trust this computer
  <br>
  <span class="smaller" style="margin-left:24px;">
    You can always change which computers you trust in your Account settings.
  </span>
  </label>
  </div>
  
  
  <div id="confirm-section" class="config-section">
  <div class="confirm-heading">
  Turn on 2-step verification
  </div>
  <div id="confirm-action">
  <p>
  You will be asked for a code whenever you sign in from an unrecognized computer or device.
  </p>
  </div>
  </div>
  
  <div id="configure-app-android">
  <div class="heading">
  Install the verification application for
  <span id="app-download-type">Android</span>.
  </div>
  <ol class="app-instructions">
  <li><p class="ml-list-item">
  On your phone, go to the Android Market.
  </p></li>
  <li><p class="ml-list-item">
  Search for <b>Google Authenticator</b>.
  <span class="smaller secondary">(<a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">Download from the Google Play Store</a>)</span>
  </p></li>
  <li><p class="ml-list-item">
  Download and install the application.
  <br>
  </p></li>
  </ol>
  <div class="heading">
  Now open and configure Google Authenticator.
  </div>
  <ol class="app-instructions">
  <li>
  In Google Authenticator, select Scan a barcode.
  </li>
  <li>
  Use your phone's camera to scan this barcode.
  <div class="qr-box">
  <img src="<?php $this->chart_url();?>"/>
  
  </div>
  </li>
  </ol>
  <div class="manual-zippy">
  <a href="#"><img src="<?php $this->plugin_url();?>/images/zippy_plus_sm.gif" class="icon" style="margin-top: 2px; visibility: inherit;"></a><div class="smallicon-content"><a href="#"><p id="manual-label-android">
  Can't scan the barcode?
  </p></a><ol class="app-instructions inactive" id="manual-content-android">
  <li>
  In Google Authenticator, select Manually add account.
  </li>
  <li>
  In "Enter account name" type your wordpress username.
  </li>
  <li>
  In "Enter key" type your secret key:
  <div class="secret-key-box">
  <div class="secret-key">
  <?php $this->secret_key();?>
</div>
  <div class="smaller subtitle">
  Spaces don't matter.
  </div>
  </div>
  </li>
  <li>
  Choose Time-based type of key.
  </li>
  <li>
  Tap Save.
  </li>
  </ol></div><div style="clear: both;"></div>
  
  </div>
  </div>
  
  <div id="configure-app-iphone">
  <div class="heading">
  Install the verification application for
  <span id="app-download-type">mobile</span>.
  </div>
  <ol class="app-instructions">
  <li><p class="ml-list-item">
  On your iPhone, tap the App Store icon.
  </p></li>
  <li><p class="ml-list-item">
  Search for <b>Google Authenticator</b>.
  <span class="smaller secondary">(<a href="http://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank">iTunes URL</a>)</span>
  </p></li>
  <li><p class="ml-list-item">
  Tap the app, and then tap Free to download and install it.
  </p></li>
  </ol>
  <div class="heading">
  Now open and configure Google Authenticator.
  </div>
  <ol class="app-instructions">
  <li>
  In Google Authenticator, tap +, and then Scan Barcode.
  </li>
  <li>
  Use your phone's camera to scan this barcode.
  <div class="qr-box">
  <img src="<?php $this->chart_url();?>">
  
  </div>
  </li>
  </ol>
  <div class="manual-zippy">
  <a href="#"><img style="margin-top: 2px; visibility: inherit;" class="icon" src="<?php $this->plugin_url();?>/images/zippy_plus_sm.gif"></a><div class="smallicon-content"><a href="#"><p id="manual-label-iphone">
  Can't scan the barcode?
  </p></a><ol id="manual-content-iphone" class="app-instructions inactive">
  <li>
  In Google Authenticator, tap +.
  </li>
  <li>
  Choose Time-based type of key.
  </li>
  <li>
  In "Account" type your wordpress username.
  </li>
  <li>
  In "Key" type your secret key:
  <div class="secret-key-box">
  <div class="secret-key">
  <?php $this->secret_key();?>
</div>
  <div class="smaller subtitle">
  Spaces don't matter.
  </div>
  </div>
  </li>
  <li>
  Tap Done.
  </li>
  </ol></div><div style="clear: both;"></div>
  
  </div>
  </div>
  <div id="configure-app-blackberry">
  <div class="heading">
  Install the verification application for
  <span id="app-download-type">mobile</span>.
  </div>
  <ol class="app-instructions">
  <li>
  On your phone, open a web browser.
  </li>
  <li>
  Go to <strong>m.google.com/authenticator</strong>.
  </li>
  <li>
  Download and install the Google Authenticator application.
  </li>
  </ol>
  <div class="heading">
  Now open and configure Google Authenticator.
  </div>
  <ol class="app-instructions">
  <li>
  In Google Authenticator, select Manual key entry.
  </li>
  <li>
  In "Enter account name" type your wordpress username.
  </li>
  <li>
  In "Enter key" type your secret key:
  <div class="secret-key-box">
  <div class="secret-key">
  <?php $this->secret_key();?>
</div>
  <div class="smaller subtitle">
  Spaces don't matter.
  </div>
  </div>
  </li>
  <li>
  Choose Time-based type of key.
  </li>
  <li>
  Tap Save.
  </li>
  </ol>
  </div>
  <div id="app-verify-success" class="active-text">
  <p>
  Your <span id="app-verify-type">mobile</span> device is configured.
  </p>
  <p class="last verify-success-click-next-message">
  Click Next to continue.
  </p>
  </div>
  <div id="app-verify-failures" class="verify-tip">
  Tip: Codes are time-dependent. Make sure your phone is set to the
  correct local time.
  </div>
  <div id="email-verify-success" class="active-text">
  <p>
  Your email is configured.
  </p>
  <p class="last verify-success-click-next-message">
  Click Next to continue.
  </p>
  </div>
  <div id="configure-app" class="config-section">
  <div class="border-box mobile-app-step">
  <div id="configure-app-instructions"></div>
  <p class="last">
  When the application is configured, type the code generated, and click Verify.
  </p>
  <div class="verify-code-widget">
  <label for="app-verify-code">
  Code:
  </label>
  <input type="text" size="6" dir="ltr" id="app-verify-code" name="verifyPinApp" autocomplete="off">&nbsp;
  <input type="submit" value="Verify" id="app-verify-button" name="VerifyApp">
  </div>
  <img style="visibility: hidden;" class="icon smallicon" alt="" src="<?php $this->plugin_url();?>/images/loading.gif"><div id="app-verify-container" class="smallicon-content"></div>
  </div>
  </div></div>
      