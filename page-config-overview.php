 <h2 class="section-heading">
  How to receive codes
  </h2>
  <h3 class="section-label">
  Mobile application
  </h3>
  <div class="section-data">
  <?php if($this->wp2sv_mobile):?>
      <div class="smallicon-content icon-checkmark">
      <?php echo $this->wp2sv_mobile;?>
      <span class="edit-links-container">
      <a id="remove-app-link" href="#">Remove/Replace</a>
      </span>
      </div>
  <?php else:?>
  
      <span class="edit-links-container">
      <a id="add-android-link" href="#">Android</a>
      -
      <a id="add-iphone-link" href="#">iPhone</a>
      -
      <a id="add-blackberry-link" href="#">BlackBerry</a>
      </span>
  <?php endif;?>
  </div>

  <div class="section-spacer"></div>
  <h3 class="section-label">
  Email
  </h3>
  <div class="section-data">
    <?php if($this->wp2sv_email):?>
      <div class="smallicon-content icon-checkmark">
      <span class="email-display">
      <span dir="ltr">
        <?php echo $this->wp2sv_email;?>
      </span>
      </span>
      <span class="edit-links-container">
      <a id="edit-email-link" href="#">Edit</a>
      -
      <a id="remove-email-link" href="#">Remove</a>
      </span>
     
      </div>
     <?php else:?>
         <span class="edit-links-container">
            <a id="add-email-link" href="#">Add an email</a>
        </span>
     <?php endif;?>
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
        <?php $this->the_backup_codes();?>
  </div>
  </div>
    -->
  <div class="section-spacer"></div>
  <div class="section-label">
  <h3>
    Trust this computer
  </h3>
  <div id="printable-warning">
  <span class="warning">Warning:</span>
  Trusted computers only ask for verification codes once every 30 days. 
  If you lose your phone, you might be able to access your account from 
  a trusted computer without needing a code. We recommend that you make this a trusted computer only if you trust the people who have access to it.
  </div>
  </div>
  <div class="section-data">
      <input type="checkbox" name="trusted" id="trust_computer" value="1"<?php checked($this->auth->remember)?>/>
        <label for="trust_computer"><?php echo $this->auth->remember?'Trusted':'Untrusted';?></label>
  <div id="printable-codes">
  </div>
  </div>