<?php
/**
 * @var Wordpress2StepVerification $this
 */
?><h1>
	<?php _e( 'Set up 2-step verification for', 'wp2sv' ); ?> <span
		class="bolder"><?php _e( $this->user->display_name ); ?></span>
</h1>
<ol class="process-map" id="process-map">
	<li class="pm-current" id="pm-generate"><?php _e( 'Set up your phone', 'wp2sv' ); ?></li>
	<li class="pm-incomplete" id="pm-remember"><?php _e( 'Verify computer', 'wp2sv' ); ?></li>
	<li class="pm-incomplete" id="pm-confirm"><?php _e( 'Activate', 'wp2sv' ); ?></li>
</ol>
<div id="config-container">
	<div id="pm-subheader">
		<div class="pm-subheader" id="generate-header">
			<?php _e( 'Choose how you\'d like to get verification codes &mdash; in an email,or from an application on your smartphone.', 'wp2sv' ); ?>
		</div>
	</div>
	<div class="config-section" id="mobile-device-type">
		<div class="icon-content  icon-primaryphone" id="mobile-device-icon">
			<select name="deviceType" id="device-type">
				<option value=""><?php _e('Choose one:','wp2sv');?></option>
				<optgroup label="<?php _e( 'Smartphone application', 'wp2sv' ); ?>">
					<option value="android"<?php $this->last_page_selected('android');?>>Android</option>
					<option value="iphone"<?php $this->last_page_selected('iphone');?>>iPhone</option>
					<option value="blackberry"<?php $this->last_page_selected('blackberry');?>>BlackBerry</option>
				</optgroup>
				<optgroup label="Email">
					<option value="email"<?php $this->last_page_selected('email');?>>Email</option>
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
        <a id="submit-button" style="text-decoration:none; color:white;"
           href="#"><?php _e( 'TURN ON 2-STEP VERIFICATION', 'wp2sv' ); ?></a>
    </span>
	<span id="skip-step-container"><a href="#" id="skip-step" class="inactive"><?php _e('Skip this step','wp2sv')?></a></span>
	<a href="#" id="cancel-link"><?php _e('Cancel','wp2sv');?></a>
	<input type="hidden" name="Cancel">
	<input type="hidden" value="primaryphone" name="WizardState">
</div>
<div id="inactive-elements" class="inactive">
	<div class="config-section" id="email-address">
		<div class="heading"><?php _e('Add an email address where Wordpress 2-step verification can send codes.','wp2sv');?></div>
		<div class="phone-widget" id="primary-phone-widget">
			<table>
				<tbody>
				<tr>
					<td></td>
					<td></td>
					<td class="device-address" id="primary-email-address-location">
						<input type="text" value="<?php _e( $this->wp2sv_email ); ?>" id="primary-email"
						       name="emailAddress" dir="ltr"></td>
					<td class="primary-phone">&nbsp;<img src="<?php $this->plugin_url(); ?>/images/loading.gif"
					                                     class="smallicon" id="primary-phone-valid" alt=""
					                                     style="visibility: inherit;"></td>
					<td class="phone-usage-message primary-phone">
						<div><?php _e('Enter your email address','wp2sv');?>.</div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="primary-phone">
						<div class="example" id="primary-example-container"><?php _e('ex:','wp2sv');?> <span dir="ltr" id="primary-example-number"><?php _e('example@domain.com','wp2sv');?></span>
						</div>
					</td>

				</tr>
				</tbody>
			</table>
			<div class="inactive w2sverror" id="primary-error"></div>
		</div>
		<div class=" " id="primary-number-test">
			<div id="primary-test-heading" class="heading"><?php _e('Let\'s test the email.','wp2sv');?></div>
			<div id="primary-verify-inputs" class="border-box phone-test">
				<ol class="phone-test-steps">
					<li>
						<div class="ml-list-item"><?php _e('Click "Send code" and check your email for the verification code.','wp2sv');?>
							<div class="send-code-container">
								<input type="submit" name="SendCode" value="<?php _e('Send code','wp2sv');?>" id="primary-send-code"
								       disabled=""/>
                                <span class="box">
                                    <img style="visibility: hidden;" class="icon smallicon" alt=""
                                         src="<?php $this->plugin_url(); ?>/images/loading.gif">
                                    <div id="primary-code-sent" class="smallicon-content"></div>
</span>
							</div>
						</div>
					</li>
					<li class=" inactive-text" id="primary-test-input">
						<div class="ml-list-item">
							<?php _e('Enter the code you receive on your email','wp2sv');?>.
							<div>
								<div class="verify-code-widget">
									<label for="primary-verify-code">
										<?php _e('Code','wp2sv');?>:
									</label>
									<input type="text" disabled="" size="6" dir="ltr" id="primary-verify-code"
									       name="verifyPin" autocomplete="off">&nbsp;
									<input type="submit" disabled="" value="<?php _e('Verify','wp2sv');?>" id="primary-verify-button"
									       name="VerifyPhone">
								</div>
								<img style="visibility: hidden;" class="icon smallicon" alt=""
								     src="<?php $this->plugin_url(); ?>/images/loading.gif">

								<div id="primary-verify-container" class="smallicon-content"></div>
							</div>
						</div>
					</li>
				</ol>
			</div>
		</div>

	</div>

	<div id="remember-computer-state" class="config-section">
		<div class="remember-heading">
			<?php _e('Make this a <span class="trusted-computer-emphasis">trusted computer</span>','wp2sv');?>?
		</div>
		<div class="remember-box">
			<p class="remember-text">
				<?php
				_e('Trusted computers only ask for verification codes once every 30 days. If you lose your phone, you might be able to access your account from a trusted computer without needing a code. We recommend that you make this a trusted computer only if you trust the people who have access to it.','wp2sv');?>
			</p>
		</div>
		<label for="rememberComputerVerify">
			<input type="checkbox" id="rememberComputerVerify" name="trusted" checked=""/>
			<?php _e('Trust this computer','wp2sv');?>
			<br>
			<span class="smaller" style="margin-left:24px;"><?php _e('You can always change which computers you trust in your Account settings.','wp2sv');?></span>
		</label>
	</div>


	<div id="confirm-section" class="config-section">
		<div class="confirm-heading">
			<?php _e('Turn on 2-step verification','wp2sv');?>
		</div>
		<div id="confirm-action">
			<p>
				<?php _e('You will be asked for a code whenever you sign in from an unrecognized computer or device.','wp2sv');?>
			</p>
		</div>
	</div>

	<div id="configure-app-android">
		<div class="heading">
			<?php _e('Install the verification application for','wp2sv');?>
			<span id="app-download-type">Android</span>.
		</div>
		<ol class="app-instructions">
			<li><p class="ml-list-item">
					<?php _e('On your phone, go to the Google Play Store.','wp2sv');?>
				</p></li>
			<li><p class="ml-list-item">
					<?php _e('Search for <b>Google Authenticator</b>.','wp2sv');?>
					<span class="smaller secondary">(<a target="_blank"
					                                    href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"><?php
							_e('Download from the Google Play Store','wp2sv');?></a>)</span>
				</p></li>
			<li><p class="ml-list-item">
					<?php _e('Download and install the application.','wp2sv');?>
					<br>
				</p></li>
		</ol>
		<div class="heading">
			<?php _e('Now open and configure Google Authenticator.','wp2sv');?>
		</div>
		<ol class="app-instructions">
			<li>
				<?php _e('In Google Authenticator, touch Menu and select "Set up account." ','wp2sv');?>
			</li>
			<li>
				<?php _e('Select "Scan a barcode."','wp2sv');?>
			</li>
			<li>
				<?php _e('Use your phone\'s camera to scan this barcode.','wp2sv');?>
				<div class="qr-box">
					<img src="<?php $this->chart_url(); ?>"/>
				</div>
			</li>
		</ol>
		<div class="manual-zippy">
			<a href="#"><img src="<?php $this->plugin_url(); ?>/images/zippy_plus_sm.gif" class="icon"
			                 style="margin-top: 2px; visibility: inherit;"></a>

			<div class="smallicon-content"><a href="#"><p id="manual-label-android">
						<?php _e('Can\'t scan the barcode?','wp2sv');?>
					</p></a>
				<ol class="app-instructions inactive" id="manual-content-android">
					<li>
						<?php _e(' In Google Authenticator, touch Menu and select "Set up account."','wp2sv');?>
					</li>
					<li>
						<?php _e('Select "Enter provided key"','wp2sv');?>
					</li>
					<li>
						<?php _e('In "Enter account name" type your wordpress username','wp2sv');?>
					</li>
					<li>
						<?php _e('In "Enter your key" type your secret key:','wp2sv');?>
						<div class="secret-key-box">
							<div class="secret-key">
								<?php $this->secret_key(); ?>
							</div>
							<div class="smaller subtitle">
								<?php _e('Spaces don\'t matter.','wp2sv');?>
							</div>
						</div>
					</li>
					<li>
						<?php _e('Key type: make sure "Time-based" is selected','wp2sv');?>
					</li>
					<li>
						<?php _e('Tap Add.','wp2sv');?>
					</li>
				</ol>
			</div>
			<div style="clear: both;"></div>

		</div>
	</div>

	<div id="configure-app-iphone">
		<div class="heading">
			<?php _e('Install the verification application for','wp2sv');?>
			<span id="app-download-type">iPhone</span>.
		</div>
		<ol class="app-instructions">
			<li><p class="ml-list-item">
					<?php _e('On your iPhone, tap the App Store icon.','wp2sv');?>
				</p></li>
			<li><p class="ml-list-item">
					<?php _e('Search for <b>Google Authenticator</b>.','wp2sv');?>
					<span class="smaller secondary">(<a
							href="http://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" target="_blank"><?php _e('Download from the App Store');?></a>)</span>
				</p></li>
			<li><p class="ml-list-item">
					<?php _e('Tap the app, and then tap Free to download and install it.','wp2sv');?>
				</p></li>
		</ol>
		<div class="heading">
			<?php _e('Now open and configure Google Authenticator.','wp2sv');?>
		</div>
		<ol class="app-instructions">
			<li>
				<?php _e('In Google Authenticator, tap "+", and then "Scan Barcode." ','wp2sv');?>
			</li>
			<li>
				<?php _e('Use your phone\'s camera to scan this barcode','wp2sv');?>
				<div class="qr-box">
					<img src="<?php $this->chart_url(); ?>">
				</div>
			</li>
		</ol>
		<div class="manual-zippy">
			<a href="#"><img style="margin-top: 2px; visibility: inherit;" class="icon"
			                 src="<?php $this->plugin_url(); ?>/images/zippy_plus_sm.gif"></a>

			<div class="smallicon-content"><a href="#"><p id="manual-label-iphone">
						<?php _e('Can\'t scan the barcode?','wp2sv');?>
					</p></a>
				<ol id="manual-content-iphone" class="app-instructions inactive">
					<li>
						<?php _e('In Google Authenticator, tap +.','wp2sv');?>
					</li>
					<li>
						<?php _e('Key type: make sure "Time-based" is selected. ','wp2sv');?>
					</li>
					<li>
						<?php _e('In "Account" type your wordpress username.','wp2sv');?>
					</li>
					<li>
						<?php _e('In "Key" type your secret key:','wp2sv')?>
						<div class="secret-key-box">
							<div class="secret-key">
								<?php $this->secret_key(); ?>
							</div>
							<div class="smaller subtitle">
								<?php _e('Spaces don\'t matter.','wp2sv');?>
							</div>
						</div>
					</li>
					<li>
						<?php _e('Tap Done.','wp2sv');?>
					</li>
				</ol>
			</div>
			<div style="clear: both;"></div>

		</div>
	</div>
	<div id="configure-app-blackberry">
		<div class="heading">
			<?php _e('Install the verification application for','wp2sv');?>
			<span id="app-download-type">BlackBerry</span>.
		</div>
		<ol class="app-instructions">
			<li>
				<?php _e('On your phone, open a web browser.','wp2sv');?>
			</li>
			<li>
				<?php _e('Go to <strong>m.google.com/authenticator</strong>','wp2sv');?>.
			</li>
			<li>
				<?php _e('Download and install the Google Authenticator application.','wp2sv');?>
			</li>
		</ol>
		<div class="heading">
			<?php _e('Now open and configure Google Authenticator.','wp2sv');?>
		</div>
		<ol class="app-instructions">
			<li>
				<?php _e('In Google Authenticator, select Manual key entry.','wp2sv');?>
			</li>
			<li>
				<?php _e('In "Enter account name" type your wordpress username.','wp2sv');?>
			</li>
			<li>
				<?php _e('In "Enter key" type your secret key:','wp2sv');?>
				<div class="secret-key-box">
					<div class="secret-key">
						<?php $this->secret_key(); ?>
					</div>
					<div class="smaller subtitle">
						<?php _e('Spaces don\'t matter.','wp2sv');?>
					</div>
				</div>
			</li>
			<li>
				<?php _e('Choose Time-based type of key.','wp2sv');?>
			</li>
			<li>
				<?php _e('Tap Save.','wp2sv');?>
			</li>
		</ol>
	</div>
	<div id="app-verify-success" class="active-text">
		<p>
			<?php
			printf(__('Your %s device is configured','wp2sv'),$this->configuring_device());?>.
		</p>

		<p class="last verify-success-click-next-message">
			<?php _e('Click Next to continue.','wp2sv');?>
		</p>
	</div>
	<div id="app-verify-failures" class="verify-tip">
		<?php _e('Tip: Codes are time-dependent. Make sure your phone is set to the
		correct local time.','wp2sv');?>
	</div>
	<div id="email-verify-success" class="active-text">
		<p>
			<?php _e('Your email is configured.','wp2sv');?>
		</p>

		<p class="last verify-success-click-next-message">
			<?php _e('Click Next to continue.','wp2sv');?>
		</p>
	</div>
	<div id="configure-app" class="config-section">
		<div class="border-box mobile-app-step">
			<div id="configure-app-instructions"></div>
			<p class="last">
				<?php _e('Once you have scanned the barcode, enter the 6-digit verification code generated by the Authenticator app.','wp2sv');?>
			</p>

			<div class="verify-code-widget">
				<label for="app-verify-code">
					<?php _e('Code:','wp2sv');?>
				</label>
				<input type="text" size="6" dir="ltr" id="app-verify-code" name="verifyPinApp" autocomplete="off">&nbsp;
				<input type="submit" value="<?php _e('Verify','wp2sv');?>" id="app-verify-button" name="VerifyApp">
			</div>
			<img style="visibility: hidden;" class="icon smallicon" alt=""
			     src="<?php $this->plugin_url(); ?>/images/loading.gif">

			<div id="app-verify-container" class="smallicon-content"></div>
		</div>
	</div>
</div>
