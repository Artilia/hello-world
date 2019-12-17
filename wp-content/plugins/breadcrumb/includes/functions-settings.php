<?php
if ( ! defined('ABSPATH')) exit;  // if direct access



add_action('breadcrumb_settings_tabs_content_options','breadcrumb_settings_tabs_content_options');

function breadcrumb_settings_tabs_content_options(){

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_text = get_option( 'breadcrumb_text' );
    $breadcrumb_separator = get_option( 'breadcrumb_separator' );
    $breadcrumb_display_last_separator = get_option( 'breadcrumb_display_last_separator' );
    $breadcrumb_word_char = get_option( 'breadcrumb_word_char' );
    $breadcrumb_word_char_count = get_option( 'breadcrumb_word_char_count' );
    $breadcrumb_word_char_end = get_option( 'breadcrumb_word_char_end' );
    $breadcrumb_display_home = get_option( 'breadcrumb_display_home' );
    $breadcrumb_home_text = get_option( 'breadcrumb_home_text' );
    $breadcrumb_hide_on_pages = get_option( 'breadcrumb_hide_on_pages' );
    $breadcrumb_hide_on_page_by_id = get_option( 'breadcrumb_hide_on_page_by_id' );
    $breadcrumb_url_hash = get_option( 'breadcrumb_url_hash' );

    //$screen = get_current_screen();



    ?>


    <div class="section">
        <div class="section-title">General Option</div>
        <p class="description section-description">Set some basic option to get start.</p>

        <?php

        $args = array(
            'id'		=> 'breadcrumb_text',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb front text','breadcrumb'),
            'details'	=> __('Display custom text before breadcrumb.','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_text,
            'default'		=> __('You are here', 'breadcrumb'),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'breadcrumb_separator',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb separator text','breadcrumb'),
            'details'	=> __('You can display custom separator. ex: <code>&raquo;</code>','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_separator,
            'default'		=> '&raquo;',
        );

        $settings_tabs_field->generate_field($args);





        $args = array(
            'id'		=> 'breadcrumb_display_last_separator',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Display last separator','breadcrumb'),
            'details'	=> __('Display or hide last separator.','breadcrumb'),
            'type'		=> 'select',
            'value'		=> $breadcrumb_display_last_separator,
            'default'		=> 'no',
            'args'		=> array(
                'no'=>__('No','breadcrumb'),
                'yes'=>__('Yes','breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'		=> 'breadcrumb_word_char',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb link text limit by?','breadcrumb'),
            'details'	=> __('You can limit link text by word or character','breadcrumb'),
            'type'		=> 'select',
            'value'		=> $breadcrumb_word_char,
            'default'		=> 'word',
            'args'		=> array(
                'word'=>__('Word','breadcrumb'),
                'character'=>__('Character','breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'breadcrumb_word_char_count',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Limit count','breadcrumb'),
            'details'	=> __('Set custom limit value, number only.','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_word_char_count,
            'default'		=> '5',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_word_char_end',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Ending character','breadcrumb'),
            'details'	=> __('Set custom Ending character, ex: ...','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_word_char_end,
            'default'		=> '...',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'breadcrumb_display_home',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Display "Home" on breadcrumb?','breadcrumb'),
            'details'	=> __('You can hide or display Home on breadcrumb.','breadcrumb'),
            'type'		=> 'select',
            'value'		=> $breadcrumb_display_home,
            'default'		=> 'no',
            'args'		=> array(
                'no'=>__('No','breadcrumb'),
                'yes'=>__('Yes','breadcrumb'),



            ),
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'		=> 'breadcrumb_home_text',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Custom home text','breadcrumb'),
            'details'	=> __('You can set custom text for "Home"','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_home_text,
            'default'		=> __('Home','breadcrumb'),
        );

        $settings_tabs_field->generate_field($args);




        $args = array(
            'id'		=> 'breadcrumb_hide_on_pages',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Hide breadcrumb on these pages','breadcrumb'),
            'details'	=> __('You can hide breadcrumb on these pages.','breadcrumb'),
            'type'		=> 'select',
            'multiple'		=> true,
            'value'		=> $breadcrumb_hide_on_pages,
            'default'		=> array(),
            'is_pro'=>true,
            'pro_text'=>'Only in pro',
            'args'		=> array(
                'home'=>__('Homepage','breadcrumb'),
                'front_page'=>__('Front page','breadcrumb'),
                'blog_front_page'=>__('Posts page','breadcrumb'),


            ),
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'breadcrumb_hide_on_page_by_id',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Hide on page by ID','breadcrumb'),
            'details'	=> __('Page ids, use comma separate. ex: 12,15','breadcrumb'),
            'type'		=> 'text',
            'is_pro'=>true,
            'pro_text'=>'Only in pro',
            'value'		=> $breadcrumb_hide_on_page_by_id,
            'default'		=> '',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_url_hash',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Current URL hash','breadcrumb'),
            'details'	=> __('If you want to keep # on current url, otherwise keep empty','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_url_hash,
            'default'		=> '#',
        );

        $settings_tabs_field->generate_field($args);





        ?>


    </div>
    <?php

}







add_action('breadcrumb_settings_tabs_content_style','breadcrumb_settings_tabs_content_style');

function breadcrumb_settings_tabs_content_style(){

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_padding = get_option( 'breadcrumb_padding' );
    $breadcrumb_margin = get_option( 'breadcrumb_margin' );
    $breadcrumb_bg_color = get_option( 'breadcrumb_bg_color' );
    $breadcrumb_link_color = get_option( 'breadcrumb_link_color' );
    $breadcrumb_font_size = get_option( 'breadcrumb_font_size' );

    $breadcrumb_themes = get_option( 'breadcrumb_themes' );
    $breadcrumb_separator_color = get_option( 'breadcrumb_separator_color' );



    ?>
    <div class="section">
        <div class="section-title">Choose style</div>
        <p class="description section-description">Customize the breadcrumb.</p>

        <?php



        $args = array(
            'id'		=> 'breadcrumb_themes',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb Themes','breadcrumb'),
            'details'	=> __('Choose breadcrumb theme','breadcrumb'),
            'type'		=> 'radio_image',
            'value'		=> $breadcrumb_themes,
            'default'		=> 'theme5',
            'width'		=> '350px',
            'args'		=> array(

                'theme1'=>array('name'=>'theme1','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme1.png'),
                'theme2'=>array('name'=>'theme1','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme2.png'),

                'theme3'=>array('name'=>'theme1','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme3.png'),
                'theme4'=>array('name'=>'theme1','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme4.png'),

                'theme5'=>array('name'=>'theme5','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme5.png'),
                'theme6'=>array('name'=>'theme6','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme6.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),

                'theme7'=>array('name'=>'theme7','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme7.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme8'=>array('name'=>'theme8','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme8.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme9'=>array('name'=>'theme9','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme9.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme10'=>array('name'=>'theme10','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme10.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme11'=>array('name'=>'theme11','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme11.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme12'=>array('name'=>'theme12','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme12.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme13'=>array('name'=>'theme13','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme13.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme14'=>array('name'=>'theme14','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme14.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),
                'theme15'=>array('name'=>'theme15','thumb'=>breadcrumb_plugin_url.'assets/admin/images/theme15.png', 'disabled'=>true, 'pro_msg'=>'Only in pro'),




            ),
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_font_size',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb font size','breadcrumb'),
            'details'	=> __('Set custom font size','breadcrumb'),
            'type'		=> 'text',
            'value'		=> $breadcrumb_font_size,
            'default'		=> '14px',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_padding',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb container padding','breadcrumb'),
            'details'	=> __('Put custom padding size for breadcrumb container.','breadcrumb'),
            'type'		=> 'text',
            'placeholder'		=> '10px',
            'value'		=> $breadcrumb_padding,
            'default'		=> '10px',
        );

        $settings_tabs_field->generate_field($args);



        $args = array(
            'id'		=> 'breadcrumb_margin',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb container margin','breadcrumb'),
            'details'	=> __('Put custom margin size for breadcrumb container.','breadcrumb'),
            'type'		=> 'text',
            'placeholder'		=> '10px',
            'value'		=> $breadcrumb_margin,
            'default'		=> '10px',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_bg_color',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb link background color','breadcrumb'),
            'details'	=> __('Choose custom background color for links','breadcrumb'),
            'type'		=> 'colorpicker',
            'value'		=> $breadcrumb_bg_color,
            'default'		=> '#278df4',
        );

        $settings_tabs_field->generate_field($args);

        $args = array(
            'id'		=> 'breadcrumb_link_color',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb link color','breadcrumb'),
            'details'	=> __('Choose custom link color','breadcrumb'),
            'type'		=> 'colorpicker',
            'value'		=> $breadcrumb_link_color,
            'default'		=> '#ffffff',
        );

        $settings_tabs_field->generate_field($args);


        $args = array(
            'id'		=> 'breadcrumb_separator_color',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Breadcrumb separator color','breadcrumb'),
            'details'	=> __('Choose custom separator color','breadcrumb'),
            'type'		=> 'colorpicker',
            'value'		=> $breadcrumb_separator_color,
            'default'		=> '#727272',
        );

        $settings_tabs_field->generate_field($args);

        ?>


    </div>
    <?php

}



add_action('breadcrumb_settings_tabs_content_shortcodes','breadcrumb_settings_tabs_content_shortcodes');

function breadcrumb_settings_tabs_content_shortcodes(){

    $settings_tabs_field = new settings_tabs_field();


    ?>
    <div class="section">
        <div class="section-title">Shortcodes</div>
        <p class="description section-description">Simply copy these shortcode and user under post or page content</p>


        <?php
        ob_start();
        ?>

        <div class="copy-to-clipboard">
            <input type="text" value="[breadcrumb]"> <span class="copied">Copied</span>
            <p class="description">You can use this shortcode under post content</p>
        </div>


        <div class="copy-to-clipboard">
            <textarea cols="50" rows="2" style="background:#bfefff" onClick="this.select();"><?php echo '<?php echo do_shortcode("[breadcrumb'; echo "]"; echo '"); ?>'; ?></textarea> <span class="copied">Copied</span>
            <p class="description">PHP Code, you can use under theme .php files.</p>
        </div>



        <style type="text/css">
             .copy-to-clipboard {
            }

             .copy-to-clipboard .copied {
                display: none;
                background: #e5e5e5;
                padding: 4px 10px;
                line-height: normal;
            }
        </style>

        <script>
            jQuery(document).ready(function ($) {
                $(document).on('click', '.copy-to-clipboard input, .copy-to-clipboard textarea', function () {
                    $(this).focus();
                    $(this).select();
                    document.execCommand('copy');
                    $(this).parent().children('.copied').fadeIn().fadeOut(2000);
                })
            })
        </script>
        <?php
        $html = ob_get_clean();
        $args = array(
            'id' => 'breadcrumb_shortcodes',
            'title' => __('Get shortcode', 'breadcrumb'),
            'details' => '',
            'type' => 'custom_html',
            'html' => $html,
        );
        $settings_tabs_field->generate_field($args);
















        ?>
    </div>
    <?php

}




add_action('breadcrumb_settings_tabs_content_custom_css','breadcrumb_settings_tabs_content_custom_css');

function breadcrumb_settings_tabs_content_custom_css(){

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_custom_css = get_option( 'breadcrumb_custom_css' );


    ?>
    <div class="section">
        <div class="section-title">Custom CSS</div>
        <p class="description section-description">Add your own scripts and style css.</p>

        <?php

        $args = array(
            'id'		=> 'breadcrumb_custom_css',
            //'parent' => 'breadcrumb_options',
            'title'		=> __('Custom CSS','breadcrumb'),
            'details'	=> __('Add your own CSS.','breadcrumb'),
            'type'		=> 'scripts_css',
            'value'		=> $breadcrumb_custom_css,
            'default'		=> '.breadcrumb-container{}&#10;.breadcrumb-container ul{}&#10;.breadcrumb-container li{}&#10;.breadcrumb-container a{}&#10;.breadcrumb-container .separator{}&#10;',
        );

        $settings_tabs_field->generate_field($args);









        ?>


    </div>
    <?php

}






add_action('breadcrumb_settings_tabs_content_buy_pro','breadcrumb_settings_tabs_content_buy_pro');

function breadcrumb_settings_tabs_content_buy_pro(){

    $settings_tabs_field = new settings_tabs_field();

    $breadcrumb_custom_css = get_option( 'breadcrumb_custom_css' );


    ?>
    <div class="section">
        <div class="section-title">Try our premium version</div>
        <p class="description section-description">If you love our plugin and need some extra feature please buy our pro version.</p>



        <?php

        ob_start();
        ?>

        <h3>Try Pro features</h3>
        <a class="button" href="https://www.pickplugins.com/item/breadcrumb-awesome-breadcrumbs-style-navigation-for-wordpress/?ref=dashboard" target="_blank">Buy Now</a>
        <p class="description"></p>



        <?php

        $html = ob_get_clean();
        $args = array(
            'id' => 'breadcrumb_buy',
            'title' => __('Buy Premium', 'breadcrumb'),
            'details' => '',
            'type' => 'custom_html',
            'html' => $html,
        );
        $settings_tabs_field->generate_field($args);

        ?>


        <?php

        ob_start();
        ?>

        <h3>Documentation</h3>
        <a class="button" href="https://www.pickplugins.com/documentation/breadcrumb/?ref=dashboard" target="_blank">Documentation</a><p class="description">Before asking, submitting reviews please take a look on our documentation, may help your issue fast.</p>

        <h3>Looking for support?</h3>
        <a class="button" href="https://www.pickplugins.com/forum/?ref=dashboard" target="_blank">Ask Question</a><p class="description">Its free and you can ask any question about our plugins and get support fast.</p>

        <h3>Provide your feedback</h3>

        <a class="button" href="https://wordpress.org/support/plugin/breadcrumb/reviews/?filter=5" target="_blank">Submit Reviews</a> <a class="button" href="https://wordpress.org/support/plugin/breadcrumb/#new-topic-0" target="_blank">Ask wordpress.org</a><p>We spent thousand+ hours to development on this plugin, please submit your reviews wisely.</p><p>If you have any issue with this plugin please submit our forums or contact our support first.</p><p class="description">Your feedback and reviews are most important things to keep our development on track. If you have time please submit us five star <span style="color: orange"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span> reviews.</p>

        <?php

        $html = ob_get_clean();
        $args = array(
            'id' => 'breadcrumb_support',
            'title' => __('Get support', 'breadcrumb'),
            'details' => '',
            'type' => 'custom_html',
            'html' => $html,
        );
        $settings_tabs_field->generate_field($args);

        ?>


    </div>
    <?php

}
