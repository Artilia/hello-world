<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access


class class_breadcrumb_shortcodes  {
	
	
    public function __construct(){

		add_shortcode( 'breadcrumb', array( $this, 'breadcrumb_display' ) );

    }
	

	
	
	public function breadcrumb_display($atts, $content = null ) {

        $atts = shortcode_atts(
            array(
                'themes' => '',

                ), $atts);

        $html = '';

        $themes = $atts['themes'];


        $breadcrumb_hide_on_pages = get_option( 'breadcrumb_hide_on_pages' );
        $breadcrumb_hide_on_page_by_id = get_option( 'breadcrumb_hide_on_page_by_id' );
        $breadcrumb_themes = get_option( 'breadcrumb_themes', 'theme5' );


        $breadcrumb_text = get_option('breadcrumb_text', __('You are here','breadcrumb'));

        $breadcrumb_separator = get_option('breadcrumb_separator','&raquo;');
        $breadcrumb_display_last_separator = get_option('breadcrumb_display_last_separator');

        $breadcrumb_font_size = get_option('breadcrumb_font_size');
        $breadcrumb_link_color = get_option('breadcrumb_link_color','#fff');
        $breadcrumb_separator_color = get_option('breadcrumb_separator_color');
        $breadcrumb_bg_color = get_option('breadcrumb_bg_color','#278df4');
        $breadcrumb_padding = get_option('breadcrumb_padding');
        $breadcrumb_margin = get_option('breadcrumb_margin');


        $breadcrumb_word_char = get_option('breadcrumb_word_char');
        $breadcrumb_word_char_count = get_option('breadcrumb_word_char_count');
        $breadcrumb_word_char_end = get_option('breadcrumb_word_char_end');

        $breadcrumb_display_home = get_option('breadcrumb_display_home');
        $breadcrumb_home_text = get_option('breadcrumb_home_text');
        $breadcrumb_custom_css = get_option('breadcrumb_custom_css');
        $breadcrumb_url_hash = get_option('breadcrumb_url_hash');










        //var_dump($breadcrumb_display_home);




        if(!empty($themes)){
            $breadcrumb_themes = $themes;
        }

        $hide_page_ids = explode(',', $breadcrumb_hide_on_page_by_id);

        $current_page_id = get_the_ID();

        $breadcrumb_items = breadcrumb_trail_array_list();


        if(!empty($breadcrumb_text)){

            $array_list[0] = array(
                'link'=> '#',
                'title' => $breadcrumb_text,
                'location' => 'is_singular',
            );

            $breadcrumb_items = array_merge($array_list, $breadcrumb_items);

        }


        //echo '<pre>'.var_export($breadcrumb_items, true).'</pre>';

        $breadcrumb_items = apply_filters('breadcrumb_items_array', $breadcrumb_items);

        //echo '<pre>'.var_export($breadcrumb_items, true).'</pre>';


        ob_start();
        ?>
        <div class="breadcrumb-container <?php echo $breadcrumb_themes; ?>">
            <ul itemscope itemtype="http://schema.org/BreadcrumbList">
                <?php
                $i = 1;
                foreach ($breadcrumb_items as $item_index => $item):
                    $title = !empty($item['title']) ? $item['title'] : __('No title', 'breadcrumb');
                    $title = breadcrumb_shorten_string($title, $breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end);
                    $link = isset($item['link']) ? $item['link'] : '';
                    ?>
                    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="<?php echo $title; ?>" href="<?php echo $link; ?>"><span itemprop="name"><?php echo $title; ?></span></a><span class="separator"><?php echo $breadcrumb_separator; ?></span><meta itemprop="position" content="<?php echo $i; ?>"></li>
                    <?php
                    $i++;
                endforeach;
                ?>
            </ul>
        </div>
        <style type="text/css">
            .breadcrumb-container {
                font-size: 13px;
            }
            .breadcrumb-container ul {
                margin: 0;
                padding: 0;
            }
            .breadcrumb-container li {
                box-sizing: unset;
                display: inline-block;
                margin: 0;
                padding: 0;
            }
            .breadcrumb-container li a {
                box-sizing: unset;
                padding: 0 10px;
            }
            .breadcrumb-container {
                font-size: <?php echo $breadcrumb_font_size; ?>  !important;
                padding: <?php echo $breadcrumb_padding; ?>;
                margin: <?php echo $breadcrumb_margin; ?>;
            }
            .breadcrumb-container li a{
                color:  <?php echo $breadcrumb_link_color; ?>  !important;
                font-size:  <?php echo $breadcrumb_font_size; ?>  !important;
                line-height:  <?php echo $breadcrumb_font_size; ?>  !important;
            }
            .breadcrumb-container li .separator {
                color: <?php echo $breadcrumb_separator_color; ?>  !important;
                font-size:  <?php echo $breadcrumb_font_size; ?>  !important;
            }
            <?php
            if($breadcrumb_display_last_separator=='no'){
                ?>
                .breadcrumb-container li:last-child .separator {
                    display: none;
                }
                <?php
            }
            echo breadcrumb_custom_css($breadcrumb_themes, $breadcrumb_bg_color);
            echo $breadcrumb_custom_css; ?>
        </style>
        <?php


        $html = ob_get_clean();

        return $html;
	}
	


}


new class_breadcrumb_shortcodes();