<?php
if ( ! defined('ABSPATH')) exit;  // if direct access






function breadcrumb_trail_array_list(){

    $breadcrumb_home_text = get_option('breadcrumb_home_text', __('Home','breadcrumb'));
    $breadcrumb_display_home = get_option('breadcrumb_display_home');
    $breadcrumb_url_hash = get_option('breadcrumb_url_hash');

    $home_url = get_bloginfo('url');

    $array_list = array();
    $active_plugins = get_option('active_plugins');

    if(is_front_page() && is_home()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_front_page is_home',

            );

    }elseif( is_front_page()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_front_page',
            );

    }elseif( is_home()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_home',
            );

        $array_list[1] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $home_url,
            'title' => __('Blog'),
            'location' => 'is_home',
        );


    }else if(is_attachment()){

        $current_attachment_id = get_query_var('attachment_id');
        $current_attachment_link = get_attachment_link($current_attachment_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_home',
            );

        $array_list[1] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $current_attachment_link,
            'title' => get_the_title(),
            'location' => 'is_attachment',
        );

    }
    else if(in_array( 'woocommerce/woocommerce.php', (array) $active_plugins ) && is_woocommerce() && is_shop()){
        $shop_page_id = wc_get_page_id('shop');

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_page',
            );

        $array_list[1] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($shop_page_id),
            'title' => get_the_title($shop_page_id),
            'location' => 'is_page',
        );
    }


    else if(is_page()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_page',
            );


        global $post;
        $home = get_page(get_option('page_on_front'));

        $j = 1;

        for ($i = count($post->ancestors)-1; $i >= 0; $i--) {
            if (($home->ID) != ($post->ancestors[$i])){

                $array_list[$j] = array(
                    'link'=>get_permalink($post->ancestors[$i]),
                    'title' => get_the_title($post->ancestors[$i]),
                    'location' => 'is_page',
                );
            }

            $j++;
        }


        $array_list[$j] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash :  get_permalink($post->ID),
            'title' => get_the_title($post->ID),
            'location' => 'is_page',
        );

//        if(is_woocommerce()){
//
//            $array_list[10] = array(
//                'link'=>get_permalink($post->ID),
//                'title' => get_the_title($post->ID),
//                'location' => 'is_woocommerce',
//            );
//
//        }




    }

    else if(is_singular()){

        if ( is_preview() ) {

            $array_list[1] = array(
                'link'=> '#',
                'title' => 'Post preview',
                'location' => 'post',
            );


            return $array_list;
        }


        $permalink_structure = get_option('permalink_structure',true);
        $permalink_structure = str_replace('%postname%','',$permalink_structure);
        $permalink_structure = str_replace('%post_id%','',$permalink_structure);

        $permalink_items = array_filter(explode('/',$permalink_structure));

        global $post;
        $author_id = $post->post_author;
        $author_posts_url = get_author_posts_url($author_id);
        $author_name = get_the_author_meta('display_name', $author_id);

        $post_date_year = get_the_time('Y');
        $post_date_month = get_the_time('m');
        $post_date_day = get_the_time('d');

        $get_month_link = get_month_link($post_date_year,$post_date_month);
        $get_year_link = get_year_link($post_date_year);
        $get_day_link = get_day_link($post_date_year, $post_date_month, $post_date_day);


        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_singular',
            );


        if(!empty($permalink_structure) && get_post_type()=='post'){
            if(in_array('%year%',$permalink_items)){

                $array_list[1] = array(
                    'link'=> $get_year_link,
                    'title' => $post_date_year,
                    'location' => 'is_singular',
                );

            }

            if(in_array('%monthnum%',$permalink_items)){
                $array_list[1] = array(
                    'link'=> $get_month_link,
                    'title' => $post_date_month,
                    'location' => 'is_singular',
                );
            }
            if(in_array('%day%',$permalink_items)){
                $array_list[1] = array(
                    'link'=> $get_day_link,
                    'title' => $post_date_day,
                    'location' => 'is_singular',
                );

            }

            if(in_array('%author%',$permalink_items)){
                $array_list[1] = array(
                    'link'=> $author_posts_url,
                    'title' => $author_name,
                    'location' => 'is_singular',
                );
            }

            if(in_array('%category%',$permalink_items)){
                $category_string = get_query_var('category_name');
                $category_arr = array();
                $taxonomy = 'category';
                if(strpos( $category_string, '/' )){

                    $category_arr = explode('/', $category_string);
                    $category_count = count($category_arr);
                    $last_cat = $category_arr[($category_count-1)];

                    $term_data = get_term_by('slug',$last_cat, $taxonomy);

                    $term_id = $term_data->term_id;
                    $term_name = $term_data->name;
                    $term_link = get_term_link( $term_id , $taxonomy);


                    $parents_id  = get_ancestors( $term_id, $taxonomy );

                    $parents_id = array_reverse($parents_id);

                    $i = 1;
                    foreach($parents_id as $id){

                        $parent_term_link = get_term_link( $id , $taxonomy);
                        $paren_term_name = get_term_by('id', $id, $taxonomy);

                        $array_list[$i] = array(
                            'link'=> $parent_term_link,
                            'title' => $paren_term_name->name,
                            'location' => 'is_singular',
                        );


                        $i++;
                    }

                    $array_list[$i] = array(
                        'link'=> $term_link,
                        'title' => $term_name,
                        'location' => 'is_singular',
                    );


                    $array_list[$i+1] = array(
                        'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                        'title' => get_the_title($post->ID),
                        'location' => 'is_singular',
                    );


                }else{

                    $term_data = get_term_by('slug',$category_string, $taxonomy);

                    $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                    $term_name = isset($term_data->name) ? $term_data->name : '';

                    if(!empty($term_id)):
                        $term_link = get_term_link( $term_id , $taxonomy);

                        $array_list[1] = array(
                            'link'=> $term_link,
                            'title' => $term_name,
                            'location' => 'is_singular',
                        );

                        $array_list[2] = array(
                            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                            'title' => get_the_title($post->ID),
                            'location' => 'is_singular',
                        );
                    endif;




                }







            }

        }
        elseif(get_post_type()=='product'){

//            if ( is_preview() ) {
//
//                $array_list[1] = array(
//                    'link'=> '#',
//                    'title' => 'Product preview',
//                    'location' => 'product',
//                );
//
//
//                return $array_list;
//            }


            $shop_page_id = wc_get_page_id('shop');
            $woocommerce_permalinks = get_option('woocommerce_permalinks', '');
            $product_base = $woocommerce_permalinks['product_base'];
            $permalink_items = array_filter(explode('/',$product_base));

            if(in_array('shop',$permalink_items)){

                $array_list[1] = array(
                    'link'=> get_permalink($shop_page_id),
                    'title' => get_the_title($shop_page_id),
                    'location' => 'product',
                );


            }
            if(in_array('%product_cat%',$permalink_items)){

                $category_string = get_query_var('product_cat');

                //$category_string = get_query_var('category_name');
                $category_arr = array();
                $taxonomy = 'product_cat';
                if(strpos( $category_string, '/' )){

                    $category_arr = explode('/', $category_string);
                    $category_count = count($category_arr);
                    $last_cat = $category_arr[($category_count-1)];

                    $term_data = get_term_by('slug',$last_cat, $taxonomy);

                    $term_id = $term_data->term_id;
                    $term_name = $term_data->name;
                    $term_link = get_term_link( $term_id , $taxonomy);


                    $parents_id  = get_ancestors( $term_id, $taxonomy );

                    $parents_id = array_reverse($parents_id);

                    $i = 2;
                    foreach($parents_id as $id){

                        $parent_term_link = get_term_link( $id , $taxonomy);
                        $paren_term_name = get_term_by('id', $id, $taxonomy);

                        $array_list[$i] = array(
                            'link'=> $parent_term_link,
                            'title' => $paren_term_name->name,
                            'location' => 'is_singular',
                        );


                        $i++;
                    }

                    $array_list[$i] = array(
                        'link'=> $term_link,
                        'title' => $term_name,
                        'location' => 'is_singular',
                    );

                }else{

                    $term_data = get_term_by('slug',$category_string, $taxonomy);

                    $term_id = isset($term_data->term_id) ? $term_data->term_id : '';
                    $term_name = isset($term_data->name) ? $term_data->name : '';

                    if(!empty($term_id)):
                        $term_link = get_term_link( $term_id , $taxonomy);

                        $array_list[2] = array(
                            'link'=> $term_link,
                            'title' => $term_name,
                            'location' => 'is_singular',
                        );

                        $array_list[3] = array(
                            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                            'title' => get_the_title($post->ID),
                            'location' => 'is_singular',
                        );
                    endif;



                }


            }

            $array_list_count = count($array_list);
            $array_list[$array_list_count] = array(
                'link'=>!empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                'title' => get_the_title($post->ID),
                'location' => 'is_singular',
            );



//            $array_list[3] = array(
//                'link'=>get_permalink($post->ID),
//                'title' => get_the_title($post->ID),
//                'location' => 'is_singular',
//            );


        }
        else{

            $array_list[1] = array(
                'link'=> '#',
                'title' => get_post_type(),
                'location' => 'is_singular',
            );

            $array_list[2] = array(
                'link'=>!empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_permalink($post->ID),
                'title' => get_the_title($post->ID),
                'location' => 'is_singular',
            );
        }





    }




    else if( is_tax()){

        $queried_object = get_queried_object();
        $term_name = $queried_object->name;
        $term_id = $queried_object->term_id;

        $taxonomy = $queried_object->taxonomy;
        $term_link = get_term_link( $term_id , $taxonomy);
        $parents_id  = get_ancestors( $term_id, $taxonomy );

        $parents_id = array_reverse($parents_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_tax',
            );

        $i = 1;
        foreach($parents_id as $id){

            $parent_term_link = get_term_link( $id , $taxonomy);
            $paren_term_name = get_term_by('id', $id, $taxonomy);

            $array_list[$i] = array(
                'link'=> $parent_term_link,
                'title' => $paren_term_name->name,
                'location' => 'is_tax',
            );


            $i++;
        }

        $array_list[$i] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $term_link,
            'title' => $term_name,
            'location' => 'is_tax',
        );



    }


    else if(is_category()){

        $current_cat_id = get_query_var('cat');
        $queried_object = get_queried_object();

        $taxonomy = $queried_object->taxonomy;
        $term_id = $queried_object->term_id;
        $term_name = $queried_object->name;
        $term_link = get_term_link( $term_id , $taxonomy);

        $parents_id  = get_ancestors( $term_id, $taxonomy );
        $parents_id = array_reverse($parents_id);

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_category',
            );

        $array_list[1] = array(
            'link'=> '#',
            'title' => $taxonomy,
            'location' => 'is_category',
        );


        $i = 2;
        foreach($parents_id as $id){

            $parent_term_link = get_term_link( $id , $taxonomy);
            $paren_term_name = get_term_by('id', $id, $taxonomy);

            $array_list[$i] = array(
                'link'=> $parent_term_link,
                'title' => $paren_term_name->name,
                'location' => 'is_category',
            );


            $i++;
        }

        $array_list[$i] = array(
            'link'=> !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $term_link,
            'title' => $term_name,
            'location' => 'is_category',
        );






    }


    else if(is_tag()){

        $current_tag_id = get_query_var('tag_id');
        $current_tag = get_tag($current_tag_id);
        $current_tag_name = $current_tag->name;

        $current_tag_link = get_tag_link($current_tag_id);;

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_tag',
            );

        $array_list[1] = array(
            'link'=> '#',
            'title' => __('Tag'),
            'location' => 'is_tag',
        );


        $array_list[2] = array(
            'link'=>  !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : $current_tag_link,
            'title' => $current_tag_name,
            'location' => 'is_tag',
        );
    }



    else if(is_author()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_author',
            );


        $array_list[1] = array(
            'link'=> '#',
            'title' => __('Author'),
            'location' => 'is_author',
        );

        $array_list[2] = array(
            'link'=>  !empty($breadcrumb_url_hash) ? $breadcrumb_url_hash : get_author_posts_url( get_the_author_meta( "ID" ) ),
            'title' => get_the_author(),
            'location' => 'is_author',
        );



    }else if(is_search()){

        $current_query = sanitize_text_field(get_query_var('s'));


        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_search',
            );

        $array_list[1] = array(
            'link'=>  '#',
            'title' => 'Search',
            'location' => 'is_search',
        );


        $array_list[2] = array(
            'link'=>  '#',
            'title' => $current_query,
            'location' => 'is_search',
        );

    }else if(is_year()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_year',
            );

        $array_list[1] = array(
            'link'=> '#',
            'title' => __('Year'),
            'location' => 'is_year',
        );

        $array_list[2] = array(
            'link'=>  '#',
            'title' => get_the_date('Y'),
            'location' => 'is_search',
        );

    }else if(is_month()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_month',
            );
        $array_list[1] = array(
            'link'=> '#',
            'title' => __('Month'),
            'location' => 'is_month',
        );


        $array_list[2] = array(
            'link'=>  '#',
            'title' => get_the_date('F'),
            'location' => 'is_month',
        );

    }
    else if(is_date()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_date',
            );

        $array_list[1] = array(
            'link'=> '#',
            'title' => __('Date'),
            'location' => 'is_date',
        );

        $array_list[2] = array(
            'link'=>  '#',
            'title' => get_the_date(),
            'location' => 'is_date',
        );
    }
    elseif(is_404()){

        if($breadcrumb_display_home == 'yes')
            $array_list[0] = array(
                'link'=> $home_url,
                'title' => $breadcrumb_home_text,
                'location' => 'is_404',
            );

        $array_list[1] = array(
            'link'=>  '#',
            'title' => __('404', 'breadcrumb'),
            'location' => 'is_404',
        );

    }

    return $array_list;

}








function breadcrumb_shorten_string($string, $limit_by='word', $limit_count = 4, $ending='...'){
	
	if(empty($limit_count)){
		
		$limit_count = 4;
		}
	
	

		if($limit_by == 'character'){

            if (strlen($string) > $limit_count){

                $stringCut = substr($string, 0, $limit_count);
                $string = substr($stringCut, 0, strrpos($stringCut, ' '));

                return $string.$ending;

            }
            else{

                return $string;

            }
		}else{

            $retval = $string;  //    Just in case of a problem
            $array = explode(" ", $string);
            if (count($array)<=$limit_count) {
                $retval = $string;
            }
            else{
                array_splice($array, $limit_count);
                $retval = implode(" ", $array).$ending;
            }

            return $retval;


        }
		

		
    }
	
	

	
	
	
	
	