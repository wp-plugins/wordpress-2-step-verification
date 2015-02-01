<h2 class="section-heading">
	<?php _e( 'How to receive codes', 'wp2sv' ); ?>
</h2>
<h3 class="section-label">
	<?php _e( 'Mobile application', 'wp2sv' ); ?>
</h3>
<div class="section-data">
	<?php if ( $this->wp2sv_mobile ): ?>
		<div class="smallicon-content icon-checkmark">
			<?php echo $this->wp2sv_mobile; ?>
			<span class="edit-links-container">
        <a id="change-app-link" class="modal-open" data-modal="phone-change"
           href="#"><?php _e( 'Move to a different phone', 'wp2sv' ); ?></a>

      </span>
		</div>
	<?php else: ?>

		<span class="edit-links-container">
      <a id="add-android-link" href="#">Android</a>
      -
      <a id="add-iphone-link" href="#">iPhone</a>
      -
      <a id="add-blackberry-link" href="#">BlackBerry</a>
      </span>
	<?php endif; ?>
</div>

<div class="section-spacer"></div>
<h3 class="section-label">
	<?php _e( 'Email' ); ?>
</h3>
<div class="section-data">
	<?php if ( $this->wp2sv_email ): ?>
		<div class="smallicon-content icon-checkmark">
      <span class="email-display">
      <span dir="ltr">
        <?php echo $this->wp2sv_email; ?>
      </span>
      </span>
      <span class="edit-links-container">
      <a id="edit-email-link" href="#"><?php _e( 'Edit', 'wp2sv' ); ?></a>
      -
      <a id="remove-email-link" href="#"><?php _e( 'Remove', 'wp2sv' ); ?></a>
      </span>

		</div>
	<?php else: ?>
		<span class="edit-links-container">
            <a id="add-email-link" href="#">Add an email</a>
        </span>
	<?php endif; ?>
</div>
<!--
  <div class="section-spacer"></div>
  <div class="section-label">
  <h3>
  Printable backup codes
  </h3>
  <div id="printable-warning">
  <span class="warning">Warning:</span>
  If your phone is unavailable, these codes will be the only way to
  sign in to your account. Keep them someplace accessible, like your
  wallet.
  </div>
  </div>
  <div class="section-data">
  <a id="show-codes-link" href="#">
  Show backup codes</a>
 
  <div id="printable-codes" style="display: none;">
        <?php $this->the_backup_codes(); ?>
  </div>
  </div>
    -->
<div class="section-spacer"></div>
<div class="section-label">
	<h3>
		<?php _e( 'Trust this computer', 'wp2sv' ); ?>
	</h3>

	<div id="printable-warning">
		<span class="warning"><?php _e( 'Warning:' ,'wp2sv'); ?></span>
		<?php _e( 'Trusted computers only ask for verification codes once every 30 days. If you lose your phone, you might be able to access your account from a trusted computer without needing a code. We recommend that you make this a trusted computer only if you trust the people who have access to it.', 'wp2sv' ); ?>
	</div>
</div>
<div class="section-data">
	<input type="checkbox" name="trusted" id="trust_computer" value="1"<?php checked( $this->auth->remember ) ?>/>
	<label for="trust_computer"><?php echo $this->auth->remember ? __('Trusted','wp2sv') : __('Untrusted','wp2sv'); ?></label>
</div>
<div class="section-data modal-section">
	<div class="modal-dialog-bg" style="opacity: 0.75; display: none;" aria-hidden="true"></div>
	<div id="phone-change" class="modal-dialog" tabindex="0" style="opacity: 0;display: none;" role="dialog"
	     aria-labelledby="">
		<div class="modal-dialog-title modal-dialog-title-draggable"><span class="modal-dialog-title-text" id=":7"
		                                                                   role="heading"></span><span
				class="modal-dialog-title-close" role="button" tabindex="0" aria-label="Close"></span></div>
		<div class="modal-dialog-content">
			<div class="smsauth-kd-dialog chooseapptype-dialog" id="settings-choose-app-type-dialog">
				<h2 id="settings-choose-app-type-title">
					<?php _e( 'Move Authenticator to a different phone', 'wp2sv' ); ?>
				</h2>

				<p>
					<?php _e( 'We only support a single Authenticator app configured for your account. Please select your new phone type:', 'wp2sv' ); ?>
				</p>

				<p id="settings-no-choice-app-error" style="color:red;display: none">
					<?php _e( 'Please select one phone type bellow', 'wp2sv' ); ?>
				</p>

				<div class="settings-apptype-selector-box">
					<div class="settings-apptype-radio">
						<label>
							<input type="radio" value="android" id="settings-choose-app-type-radio-android"
							       class="apptype-android" name="settings-choose-app-type-radio">
							Android
						</label>
					</div>
					<div class="settings-apptype-radio">
						<label>
							<input type="radio" value="iphone" id="settings-choose-app-type-radio-iphone"
							       class="apptype-iphone" name="settings-choose-app-type-radio">
							iPhone
						</label>
					</div>
					<div class="settings-apptype-radio">
						<label>
							<input type="radio" value="blackberry" id="settings-choose-app-type-radio-blackberry"
							       class="apptype-blackberry" name="settings-choose-app-type-radio">
							BlackBerry
						</label>
					</div>
				</div>
				<p id="settings-choose-app-type-old-authenticator-invalid">
					<?php _e( 'Once you complete this setup,', 'wp2sv' ); ?> <span
						style="font-weight: bold;"><?php _e( 'the codes generated by your old Authenticator app will stop working', 'wp2sv' ); ?></span>.
				</p>

			</div>
		</div>
		<div class="modal-dialog-buttons">

			<button name="action" class="wp2sv-buttonset-action"><?php _e( 'Continue', 'wp2sv' ); ?></button>
			<button name="cancel"><?php _e( 'Cancel', 'wp2sv' ); ?></button>
		</div>
	</div>
	<div id="wp2sv-disable" class="modal-dialog" tabindex="0" style="opacity: 0;display: none;" role="dialog"
	     aria-labelledby=":9">
		<div class="modal-dialog-title modal-dialog-title-draggable"><span class="modal-dialog-title-text" id=":9"
		                                                                   role="heading"></span><span
				class="modal-dialog-title-close" role="button" tabindex="0" aria-label="Close"></span></div>
		<div class="modal-dialog-content">
			<div class="disable-dialog" id="settings-disable-dialog">
				<h2>
					<?php _e('Turn off 2-step verification','wp2sv');?>
				</h2>

				<p class="settings-textparagraph">
					<?php _e('You will no longer be asked for verification codes when you sign in to your account','wp2sv');?>.
				</p>



				<div class="settings-textparagraph">
					<input type="checkbox" id="settings-disable-dialog-clearsettings" checked="checked"
					       class="settings-checkbox clearsettings" value="yes" name="wp2sv_clear_settings">
					<label for="settings-disable-dialog-clearsettings">
						<?php _e('Also clear my 2-step verification settings','wp2sv');?>
					</label>
				</div>
			</div>
		</div>
		<div class="modal-dialog-buttons">
			<button name="action" class="wp2sv-buttonset-action"><?php _e('Turn off','wp2sv');?></button>
			<button name="cancel"><?php _e('Cancel','wp2sv');?></button>
		</div>
	</div>
</div>