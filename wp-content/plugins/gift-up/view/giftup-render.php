<?php

// [giftup]
function giftup_func( $atts ) {
    $a = shortcode_atts( array(
        'domain' => giftup_tools::dashboard_root() . '/',
        'cdn' => 'https://cdn.giftup.app/',
        'company' => giftup_tools::get_company_id(),
        'product' => '',
        'group' => '',
        'language' => '',
        'purchasername' => '',
        'purchaseremail' => '',
        'recipientname' => '',
        'recipientemail' => '',
        'whofor' => '',
        'step' => '',
        'promocode' => '',
        'hideartwork' => '',
        'hidegroups' => '',
        'hideungroupeditems' => '',
        'hidecustomvalue' => ''
    ), $atts );
    
    if ($a['company'] != '') {
        ob_start();
        
        ?><div class="gift-up-target" 
            data-site-id="<?php echo $a['company'] ?>" 
            data-domain="<?php echo $a['domain'] ?>"
            data-product-id="<?php echo $a['product'] ?>"
            data-group-id="<?php echo $a['group'] ?>"
            data-language="<?php echo $a['language'] ?>"
            data-purchaser-name="<?php echo $a['purchasername'] ?>"
            data-purchaser-email="<?php echo $a['purchaseremail'] ?>"
            data-recipient-name="<?php echo $a['recipientname'] ?>"
            data-recipient-email="<?php echo $a['recipientemail'] ?>"
            data-step="<?php echo $a['step'] ?>"
            data-who-for="<?php echo $a['whofor'] ?>"
            data-promo-code="<?php echo $a['promocode'] ?>"
            data-hide-artwork="<?php echo $a['hideartwork'] ?>"
            data-hide-groups="<?php echo $a['hidegroups'] ?>"
            data-hide-ungrouped-items="<?php echo $a['hideungroupeditems'] ?>"
            data-hide-custom-value="<?php echo $a['hidecustomvalue'] ?>"
            data-platform="Wordpress"
        ></div>
        <script type="text/javascript">
        (function (g, i, f, t, u, p, s) {
            g[u] = g[u] || function() { (g[u].q = g[u].q || []).push(arguments) };
            p = i.createElement(f);
            p.async = 1;
            p.src = t;
            s = i.getElementsByTagName(f)[0];
            s.parentNode.insertBefore(p, s);
        })(window, document, 'script', 'https://cdn.giftup.app/dist/gift-up.js', 'giftup');
        </script><?php
        
        return ob_get_clean();
    }
    
    return "Notice to site admin: Please connect your Gift Up! account to WordPress in Settings / Gift Up!";
}
