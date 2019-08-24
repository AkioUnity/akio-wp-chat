
<?php
/**
 * Ask contact shortcode.
 */
?>
<div id="lcx-shortcode-ask-contact" class="__lcx" data-type="ask-contact">
    
    <div class="lcx-shortcode lcx-shortcode-ask-contact" data-type="ask-contact">
        <div class="lcx-title">
            <span><?php echo $msg[ 'popup_ask_contact' ]; ?></span>
        </div>
        
        <form action="index.php" class="lcx-form lcx-form-ask-contact" autocomplete="off" data-bound-group="saveUserInfo">
            <div class="lcx-table lcx-field-wrap lcx-has-btn">
                <div class="lcx-cell">
                    <input type="email" class="lcx-field lcx-field-email lcx-is-input" placeholder="<?php echo $msg['forms_email']; ?>" data-name="email" data-input-type="user" tabindex="1">
                </div>
                <div class="lcx-cell">
                    <a href="#" class="lcx-btn lcx-is-secondary lcx-action lcx-no-anim" data-type="submitForm" data-bind-to=".lcx-form-ask-contact" data-read-later="true" tabindex="10">
                        <?php echo $msg['forms_save_btn']; ?>
                    </a>
                </div>
            </div>
        
            <?php
             /**
              * Additional fields.
              */
            ?>
            <!-- <div class="lcx-field">
                <div class="lcx-table">
                    <div class="lcx-cell">
                        <select name="" id="">
                            <option value="">+387</option>
                        </select>
                    </div>
                    <div class="lcx-cell">
                        <input type="text" name="phone" class="lcx-field-phone lcx-user-input" placeholder="<?php echo $msg['forms_phone']; ?>" tabindex="2">
                    </div>
                </div>
            </div> -->
        </form>
    </div>

</div>