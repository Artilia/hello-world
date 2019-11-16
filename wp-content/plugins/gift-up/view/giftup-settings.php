<div class="wrap">
  <a href="<?php echo giftup_tools::dashboard_root() ?>" target="_blank">
    <img src="<?php echo plugins_url( '/images/logo.png', __FILE__ ) ?>" width="150" alt="Gift Up!" />
  </a>

  <script>
  var nag = document.getElementById('giftup-nag');
  if (nag) {
    nag.style.display='none';
  }
  </script>
  
  <?php if ( isset( $message ) ): ?>
    <div id="message" class="<?php echo $status ?>">
      <p>
        <strong><?php echo $message ?></strong>
      </p>
    </div>
  <?php endif; ?>
  
  <?php if ( strlen($giftup_company_id) > 0 ): ?>

      <h1 class="giftup-settings-top-header"><?php echo _e('Gift Up! Account Connected') ?></h1>

      <?php if ( FALSE == $giftup_company['isCheckoutLive'] or FALSE == $giftup_company['canShowCheckout'] ): ?>
        <div class="notice notice-warning">
          <p>You've successfully connected your Gift Up! (<?php echo $giftup_company['name'] ?>) account to Wordpress, but you need to do a few more steps before you are selling gift cards ...</p>
          <ol>
            <li><a href="<?php echo giftup_tools::dashboard_root() ?>/welcome" target="_blank" <?php if ( $giftup_company['canShowCheckout'] ): ?>style="text-decoration: line-through;"<?php endif; ?>>Complete the Gift Up! account setup process</a><?php if ( $giftup_company['canShowCheckout'] ): ?> (completed)<?php endif; ?></li>
            <li>Insert our shortcode <code>[giftup]</code> anywhere on a post or a page. This will render our checkout enabling your customers to buy your gift cards.</li>
          </ol>
        </div>

      <?php else: ?>
        <div class="notice notice-success">
          <p>You've successfully connected your Gift Up! (<?php echo $giftup_company['name'] ?>) account to Wordpress and you're now selling gift cards.</p>
        </div>
      <?php endif; ?>

      <p>
        <input class="button button-primary" type="button" value="<?php _e('View your Gift Up! dashboard') ?>" 
               onclick="window.open('<?php echo giftup_tools::dashboard_root() ?>')"/>
      </p>
      <p>&nbsp;</p>

      <?php if ( $woocommerce_installed ): ?>
        <p>&nbsp;</p>
        <hr>
        <p>&nbsp;</p>

        <h1 class="giftup-settings-top-header"><?php echo _e('WooCommerce Connection') ?></h1>
        <p>
          Gift Up! fully supports WooCommerce, enabling your customers to spend their gift cards inside of your WooCommerce cart.
          This means that when you sell a gift card, we will automatically create a matching discount 
          coupon in your WooCommerce store that matches the gift card value.
          <a href="https://help.giftupapp.com/integrations/woocommerce" target="_blank">Learn more...</a>
        </p>

        <?php if ( FALSE == $woocommerce_integration_enabled ): ?>

          <h3 class="giftup-settings-top-header"><?php echo _e('Requirements') ?></h3>
          <ul>
            <li>
              <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> WooCommerce installed</span>
            </li>
            <li>
              <?php if ( $woocommerce_version > 3 ): ?>
                <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> WooCommerce v3.0+ (using v<?php echo $woocommerce_version_str ?>)</span>
              <?php elseif ($woocommerce_version_str === 'unknown'): ?>
                <span style="color: #ffb900"><span class="dashicons dashicons-no"></span> Gift Up! requires WooCommerce v3.0+ (we could not determine your WooCommerce version)</span>
              <?php else: ?>
                <span style="color: #dc3232"><span class="dashicons dashicons-no"></span> Gift Up! requires WooCommerce v3.0+ (you are using v<?php echo $woocommerce_version_str ?>)</span>
              <?php endif; ?>
            </li>
            <li>
              <?php if ( $woocommerce_api ): ?>
                <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> WooCommerce API connection available over HTTPS</span>
              <?php else: ?>
                <span style="color: #ffb900"><span class="dashicons dashicons-no"></span> (optional) WooCommerce API connection is unavailable over HTTPS</span> - <a href="/wp-admin/admin.php?page=wc-settings&tab=api" target="_blank">please check that it is enabled here</a> and then <a href="#" onclick="location.reload()">try to connect again</a>
              <?php endif; ?>
            </li>
          </ul>

          <?php if ( ($woocommerce_version > 3 or $woocommerce_version_str === 'unknown') ): ?>
            <p>You've passed all our required checks and can activate the WooCommerce integration now.</p>
            <p><input class="button button-primary" type="button" value="<?php _e('Activate WooCommerce integration') ?>"
                      onclick="location.href='<?php echo $woocommerce_connect ?>'" /></p>
          <?php else: ?>
            <p>In order to activate the WooCommerce integrations, please fix the issues above</p>
          <?php endif; ?>

        <?php else: ?>

          <p>
            
            <?php if ( $woocommerce_integration_can_connect and $woocommerce_integration_can_create_coupons and $woocommerce_integration_can_has_webhooks ): ?>
              <span style="color: #46b450">
                <span class="dashicons dashicons-yes"></span> 
                WooCommerce integration enabled &amp; working
              </span>
              <a href="#" onclick="document.getElementById('woocommerce-details').style.display=''; this.style.display='none';">details...</a>
            <?php else: ?>
              <span style="color: #dc3232">
                <span class="dashicons dashicons-warning"></span> 
                WooCommerce integration enabled but has a problem ...
              </span>
            <?php endif; ?>
          </p>

          <ul id="woocommerce-details" 
            <?php if ( $woocommerce_integration_can_connect and $woocommerce_integration_can_create_coupons and $woocommerce_integration_can_has_webhooks ): ?>
              style="display: none;"
            <?php endif; ?>
            >
            <li>
              <?php if ( $woocommerce_integration_can_connect ): ?>
                <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> Gift Up! can connect to WooCommerce</span>
              <?php else: ?>
                <span style="color: #dc3232"><span class="dashicons dashicons-no"></span> Gift Up! cannot connect to WooCommerce</span> - <a href="/wp-admin/admin.php?page=wc-settings&tab=api">is your WooCommerce REST API enabled?</a>
              <?php endif; ?>
            </li>
            <?php if ( $woocommerce_integration_can_connect ): ?>
              <li>
                <?php if ( $woocommerce_integration_can_create_coupons ): ?>
                  <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> Gift Up! can create coupons &amp; delete</span>
                <?php else: ?>
                  <span style="color: #dc3232"><span class="dashicons dashicons-no"></span> Gift Up! cannot create &amp; delete coupons</span> - <a href="/wp-admin/admin.php?page=wc-settings">is "Enable the use of coupons" checked on in WooCommerce?</a>
                <?php endif; ?>
              </li>
              <li>
                <?php if ( $woocommerce_integration_can_has_webhooks ): ?>
                  <span style="color: #46b450"><span class="dashicons dashicons-yes"></span> WooCommerce can notify Gift Up! of new orders</span>
                <?php else: ?>
                  <span style="color: #dc3232"><span class="dashicons dashicons-no"></span> WooCommerce cannot notify Gift Up! of new orders</span>
                <?php endif; ?>
              </li>
            <?php endif; ?>
          </ul>

        <form class="form-table" name="giftup_form" id="giftup_general_settings_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
          <input type="hidden" value="disconnect" name="woocommerce">
          <p>
            <?php if ( $woocommerce_integration_can_connect == FALSE or $woocommerce_integration_can_create_coupons == FALSE or $woocommerce_integration_can_has_webhooks == FALSE ): ?>
              <input class="button button-primary" type="button" onclick="location.reload()" value="<?php _e('Test connection again ...') ?>" />
            <?php endif; ?>
            <input class="button button-secondary" type="submit" value="<?php _e('Disable WooCommerce integration') ?>" />
          </p>
        </form>

        <?php endif; ?>

        <p>&nbsp;</p>
        <hr>
        <p>&nbsp;</p>
      <?php endif; ?>

      <h1 class="giftup-settings-top-header"><?php echo _e('Help') ?></h1>

      <h3 class="giftup-settings-top-header"><?php echo _e('Knowledge Base / FAQs') ?></h3>
      <p>We have a full <a href="https://help.giftupapp.com/" target="_blank">knowledge base</a> available (and live chat) for you to view if you have any questions.</p>

      <h3 class="giftup-settings-top-header"><?php echo _e('Checkout Installation Shortcode') ?></h3>
      <p>Insert our shortcode <code>[giftup]</code> anywhere on a post or a page to install your gift card checkout.</p>

      <h3 class="giftup-settings-top-header"><?php echo _e('Disconnect Gift Up!') ?></h3>
      <p>This will disconnect your Gift Up! account (<?php echo $giftup_company['name'] ?>) from WordPress, meaning you will no longer be able to sell gift cards online.</p>
      <form class="form-table" name="giftup_form" id="giftup_general_settings_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <textarea type="text" id="giftup_api_key" name="giftup_api_key" class="giftup-settings-key" rows="5" cols="40" placeholder="API Key" style="display: none;"></textarea>
        <p><input class="button button-secondary" type="submit" name="Submit" value="<?php _e('Disconnect Gift Up!') ?>" /></p>
      </form>

  <?php else: ?>

      <h1 class="giftup-settings-top-header"><?php echo _e('Connect to Gift Up! ...') ?></h1>
      <p>In order to sell gift cards on your WordPress website, you need a free Gift Up! account connected to your WordPress website. Follow the steps below ... </p>
      <p>&nbsp;</p>

      <?php if ( strlen($giftup_company_id) > 0 and strlen($giftup_api_key) > 0 ): ?>
        <div id="message" class="notice notice-error">
          <p>
            <strong>
              There has been a problem connecting to Gift Up! Please refresh this page and if the connection issue still exists, please <a href="<?php echo giftup_tools::dashboard_root() ?>/installation/wordpress" target="_blank">double check your API key</a>.
            </strong>
          </p>
        </div>
      <?php endif; ?>

      <ol>
        <li>
          <p>
            <input class="button button-primary" type="button" value="<?php _e('Create a new Gift Up! account') ?>" 
                   onclick="window.open('<?php echo giftup_tools::dashboard_root() ?>/account/register?returnUrl=/installation/wordpress&amp;email=<?php echo $giftup_email_address ?>')"/>
            or
            <input class="button" type="button" value="<?php _e('Log in to your existing Gift Up! account') ?>" 
                   onclick="window.open('<?php echo giftup_tools::dashboard_root() ?>/installation/wordpress')"/>
          </p>
        </li>
        <li>Once inside your Gift Up! account, <a href="<?php echo giftup_tools::dashboard_root() ?>/installation/wordpress" target="_blank">get your API key</a></li>
        <li>
          <p>Copy &amp; paste the provided Gift Up! API key below:</p>
          <form class="form-table" name="giftup_form" id="giftup_general_settings_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <textarea type="text" id="giftup_api_key" name="giftup_api_key" class="giftup-settings-key" rows="5" cols="40" placeholder="API Key"><?php echo ( $giftup_api_key ); ?></textarea>
            <p><input class="button button-primary" type="submit" name="Submit" value="<?php _e('Connect to Gift Up!') ?>" /></p>
          </form>
        </li>
      </ol>

  <?php endif; ?>
</div>
