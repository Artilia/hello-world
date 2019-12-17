<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/


if ( ! defined('ABSPATH')) exit;  // if direct access 

class breadcrumb{
		
		public function breadcrumb_get_option($option_name){
			
				return get_option($option_name);
			}	
		
		public function breadcrumb_get_page_childs(){
			
				$breadcrumb_separator = $this->breadcrumb_get_option('breadcrumb_separator');
				$breadcrumb_word_char = $this->breadcrumb_get_option('breadcrumb_word_char');					
				$breadcrumb_word_char_count = $this->breadcrumb_get_option('breadcrumb_word_char_count');				
				$breadcrumb_word_char_end = $this->breadcrumb_get_option('breadcrumb_word_char_end');				
				$breadcrumb_word_char = $this->breadcrumb_get_option('breadcrumb_word_char');
				$breadcrumb_url_hash = $this->breadcrumb_get_option('breadcrumb_url_hash');	
				
				global $post;
				$home = get_page(get_option('page_on_front'));
				
				$html = '';
				
				for ($i = count($post->ancestors)-1; $i >= 0; $i--) {
					if (($home->ID) != ($post->ancestors[$i]))
						{
							$html.= '<li><span  class="separator">'.$breadcrumb_separator.'</span>';
							$html.= '<a href="';
							$html.= get_permalink($post->ancestors[$i]); 
							$html.= '">';
							$html.= get_the_title($post->ancestors[$i]);
							$html.= '</a></li>';
						}
				}
				
				$html.= '<li><span class="separator">'.$breadcrumb_separator.'</span><a title="'.get_the_title().'" href="'.$breadcrumb_url_hash.'">'.breadcrumb_shorten_string(get_the_title(),$breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
				
				return $html;
			}		
			
			
		public function breadcrumb_html($themes){
			
			global $post;

			$breadcrumb_text = $this->breadcrumb_get_option('breadcrumb_text');

			$breadcrumb_separator = $this->breadcrumb_get_option('breadcrumb_separator');
			$breadcrumb_display_last_separator = $this->breadcrumb_get_option('breadcrumb_display_last_separator');

			$breadcrumb_font_size = $this->breadcrumb_get_option('breadcrumb_font_size');
			$breadcrumb_link_color = $this->breadcrumb_get_option('breadcrumb_link_color');
			$breadcrumb_separator_color = $this->breadcrumb_get_option('breadcrumb_separator_color');
			$breadcrumb_bg_color = $this->breadcrumb_get_option('breadcrumb_bg_color');
			$breadcrumb_padding = $this->breadcrumb_get_option('breadcrumb_padding');
			$breadcrumb_margin = $this->breadcrumb_get_option('breadcrumb_margin');

			$breadcrumb_themes = $this->breadcrumb_get_option('breadcrumb_themes');
			$breadcrumb_word_char = $this->breadcrumb_get_option('breadcrumb_word_char');
			$breadcrumb_word_char_count = $this->breadcrumb_get_option('breadcrumb_word_char_count');
			$breadcrumb_word_char_end = $this->breadcrumb_get_option('breadcrumb_word_char_end');
			$breadcrumb_word_char = $this->breadcrumb_get_option('breadcrumb_word_char');

			$breadcrumb_display_home = $this->breadcrumb_get_option('breadcrumb_display_home');
			$breadcrumb_home_text = $this->breadcrumb_get_option('breadcrumb_home_text');
			$breadcrumb_custom_css = $this->breadcrumb_get_option('breadcrumb_custom_css');
			$breadcrumb_url_hash = $this->breadcrumb_get_option('breadcrumb_url_hash');

			$active_plugins = get_option('active_plugins');


				
				
				if(!empty($themes)){
					$breadcrumb_themes = $themes;
					}

				$html  = '';
				
				$html.= '<style type="text/css">';				
				$html.= '
				
						.breadcrumb-container {
						  font-size: '.$breadcrumb_font_size.'  !important;
						  padding: '.$breadcrumb_padding.';
						  margin: '.$breadcrumb_margin.';
						}
				
						.breadcrumb-container li a{
						  color: '.$breadcrumb_link_color.'  !important;
						  font-size: '.$breadcrumb_font_size.'  !important;
						  line-height: '.$breadcrumb_font_size.'  !important;
						}
						
						.breadcrumb-container li .separator {
						  color: '.$breadcrumb_separator_color.'  !important;
						  font-size: '.$breadcrumb_font_size.'  !important;
						}						
						
						
				
				';			
				
				if($breadcrumb_display_last_separator=='no'){
					
					$html.= '
							.breadcrumb-container li .separator:last-child {
							  display: none;
							}
					';	
					}

				
				$html.= breadcrumb_custom_css($breadcrumb_themes, $breadcrumb_bg_color);
				
				$html.= $breadcrumb_custom_css;
					
				$html.= '</style>';					
				
				$html.= '
				<div class="breadcrumb-container '.$breadcrumb_themes.'" itemprop="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';
		
				$html.= '<ul itemtype="http://schema.org/BreadcrumbList" itemscope="">';		
				
				if(!empty($breadcrumb_text)){
					$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="#"><span>'.$breadcrumb_text.'</span></a></li>';
					}
				
					
				if(is_front_page() && is_home()){
					
						if($breadcrumb_display_home == 'yes'){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.$breadcrumb_url_hash.'">'.$breadcrumb_home_text.'</a><span>'.$breadcrumb_separator.'</span></li>';	
							}
	
					}
					
				elseif( is_front_page()){
					
						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.$breadcrumb_url_hash.'">'.$breadcrumb_home_text.'</a><span>'.$breadcrumb_separator.'</span></li>';	
							}	
					}	
					
				elseif( is_home()){
					
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$breadcrumb_url_hash.'">'.get_page(get_option('page_for_posts'))->post_title.'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
								
								
					}					
	
				else if(is_attachment()){
					
						$current_attachment_id = get_query_var('attachment_id');
						$current_attachment_link = get_attachment_link($current_attachment_id);				
						
						
						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}

						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$current_attachment_link.'">'.get_the_title().'</a><span>'.$breadcrumb_separator.'</span></li>';

					}

				else if(in_array( 'woocommerce/woocommerce.php', (array) $active_plugins ) && is_woocommerce() && is_shop()){
					$shop_page_id = wc_get_page_id('shop');

					if($breadcrumb_display_home == 'yes'){

						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
					}

					$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$breadcrumb_url_hash.'">'.get_the_title($shop_page_id).'</a></li>';

				}


				else if(is_singular()){

						$post_parent_id = wp_get_post_parent_id(get_the_ID());
						$parent_title = get_the_title($post_parent_id);
						$paren_get_permalink = get_permalink($post_parent_id);
						
						
						if($breadcrumb_display_home == 'yes'){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
					
						
						
						if(is_page() && $post->post_parent){
							
								$html.= $this->breadcrumb_get_page_childs(); // return array

							}

						else{

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

							$html_permalink = '';


							if(!empty($permalink_structure) && get_post_type()=='post'){

								if(in_array('%year%',$permalink_items)){
									$html_permalink .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$get_year_link.'">'.breadcrumb_shorten_string($post_date_year,$breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a></li>';

									}

								if(in_array('%monthnum%',$permalink_items)){

									$html_permalink .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$get_month_link.'">'.breadcrumb_shorten_string($post_date_month, $breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a></li>';

									}

								if(in_array('%day%',$permalink_items)){

									$html_permalink .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$get_day_link.'">'.breadcrumb_shorten_string($post_date_day, $breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a></li>';

								}


								if(in_array('%author%',$permalink_items)){

									$html_permalink .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$author_posts_url.'">'.breadcrumb_shorten_string($author_name, $breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a></li>';

									}



																				
								if(in_array('%category%',$permalink_items)){

									$post_categories = get_the_category();

									if(!empty($post_categories))

									$parent_cat_links = get_category_parents( $post_categories[0]->term_id, true, ',' );

									$parent_cat_links = array_filter(explode(",",$parent_cat_links));

									foreach($parent_cat_links as $link)
										{
										$html_permalink .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span>'.$link.'</li>';
										}


									}
									
									
									$html.= $html_permalink;
									}

								$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span  class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" title="'.get_the_title().'" href="'.$breadcrumb_url_hash.'">'.breadcrumb_shorten_string(get_the_title(),$breadcrumb_word_char, $breadcrumb_word_char_count, $breadcrumb_word_char_end).'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';

							}

					}
					
				else if( is_tax()){
					
						$queried_object = get_queried_object();
						$term_name = $queried_object->name;
						$term_id = $queried_object->term_id;
						
						$taxonomy = $queried_object->taxonomy;						
						$term_link = get_term_link( $term_id , $taxonomy);
   						$parents_id  = get_ancestors( $term_id, $taxonomy );		
						
						if($breadcrumb_display_home == 'yes'){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						
						$parents_id = array_reverse($parents_id);
						
						foreach($parents_id as $id){
							
								$parent_term_link = get_term_link( $id , $taxonomy);
								$paren_term_name = get_term_by('id', $id, $taxonomy);
								
								$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$parent_term_link.'">'.$paren_term_name->name.'</a></li>';
							}
							
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$term_link.'">'.$term_name.'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
						
					}
					
				else if(is_category()){
						
						$current_cat_id = get_query_var('cat');
						$parent_cat_links = get_category_parents( $current_cat_id, true, ',' );
						
						$parent_cat_links = explode(",",$parent_cat_links);
						
						if($breadcrumb_display_home == 'yes'){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						
						foreach($parent_cat_links as $link){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span>'.$link.'</li>';
							}	
					
					}
					
				else if(is_tag()){
						
						$current_tag_id = get_query_var('tag_id');
						$current_tag = get_tag($current_tag_id);	
						$current_tag_name = $current_tag->name;				
									
						$current_tag_link = get_tag_link($current_tag_id);;				
						//$parent_cat_links = get_category_parents( $current_cat_id, true, $breadcrumb_separator );
						
						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
							}
									
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.$current_tag_link.'">'.$current_tag_name.'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
	
						
					
					}			
					
				else if(is_author()){
						
						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ).'">'.get_the_author().'</a></li>';
					
						
					
					}
					
				else if(is_search()){
		
						$current_query = sanitize_text_field(get_query_var('s'));
						
						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						
						if(empty($current_query))
							{
								$current_query = 'Search: ';
							}
						else
							{
								$current_query = 'Search: '.$current_query;
							}
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$breadcrumb_url_hash.'">'.$current_query.'</a><span class="separator">'.$breadcrumb_separator.'</span></li>';
					
						
					}			

				else if(is_year()){

						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="#">'.get_the_date('Y').'</a></li>';

					}
					
				else if(is_month()){

						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$breadcrumb_url_hash.'">'.get_the_date('F').'</a></li>';

					}					
					

				else if(is_date()){

						if($breadcrumb_display_home == 'yes')
							{
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$breadcrumb_url_hash.'">'.get_the_date().'</a></li>';

					}
					
				elseif(is_404()){
						
						//var_dump('Hello');
						$html.= '';
						if($breadcrumb_display_home == 'yes'){
							
							$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" title="'.$breadcrumb_home_text.'" href="'.get_bloginfo('url').'">'.$breadcrumb_home_text.'</a></li>';
							}
						
						$html.= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span class="separator">'.$breadcrumb_separator.'</span><a itemprop="item" href="'.$breadcrumb_url_hash.'">'.__('404', 'breadcrumb').'</a></li>';
						

					}

	
				$html.= '</ul>';
		
				$html.= '</div>';

				return $html;

			}
	
	
			
	}