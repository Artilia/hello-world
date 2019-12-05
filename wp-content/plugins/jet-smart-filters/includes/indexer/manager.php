<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Smart_Filters` doesn't exists yet.
if ( ! class_exists( 'Jet_Smart_Filters_Indexer_Manager' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_Smart_Filters_Indexer_Manager {

		public $data_to_save = array();
		public $data = null;
		public $controls = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			if ( filter_var( jet_smart_filters()->settings->get( 'use_indexed_filters' ), FILTER_VALIDATE_BOOLEAN ) ) {

				$this->load_files();

				$this->data     = new Jet_Smart_Filters_Indexer_Data();
				$this->controls = new Jet_Smart_Filters_Indexer_Controls();

				add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

				add_action( 'restrict_manage_posts', array( $this, 'add_index_filters_button' ), 10, 2 );

				add_action( 'wp_ajax_jet_smart_filters_admin_indexer', array( $this, 'index_filters' ) );
				add_action( 'wp_ajax_nopriv_jet_smart_filters_admin_indexer', array( $this, 'index_filters', ) );

			}

		}

		/**
		 * Reindex filters data
		 */
		public function index_filters() {

			$post_types      = $this->get_enabled_post_types();
			$post_types_args = array();

			if ( ! $post_types ) {
				return;
			}

			$filters = get_posts(
				array(
					'post_type'      => jet_smart_filters()->post_type->slug(),
					'posts_per_page' => - 1,
				)
			);

			foreach ( $post_types as $post_type ) {

				foreach ( $filters as $filter ) {

					if ( is_callable( array( $this, 'prepare_args' ) ) ) {

						$filter_args = $this->prepare_args( array(
							'filter_id'      => $filter->ID,
							'ignore_parents' => true,
							'post_type'      => $post_type,
						) );

						$options = $filter_args;

						$type = $filter_args['query_type'];
						$var  = $filter_args['query_var'];

						if ( ! empty( $options ) && ! empty( $filter_args['options'] ) ) {
							$post_types_args[ $post_type ][ $type ][ $var ] = $filter_args;
						}

					}

				}

			}

			$this->update_db_data( $post_types_args );

		}

		/**
		 * Get post ids by query
		 *
		 * @param $post_type
		 * @param $query_args
		 */
		public function parse_posts_ids( $post_type, $query_args ) {

			foreach ( $query_args as $query_type => $query_vars ) {

				switch ( $query_type ) {
					case 'tax_query' :
						$this->parse_posts_by_tax_query( $post_type, $query_type, $query_vars );
						break;
					case 'meta_query' :
						$this->parse_posts_by_meta_query( $post_type, $query_type, $query_vars );
						break;
				}

			}

		}

		/**
		 * Get posts by meta query
		 *
		 * @param $post_type
		 * @param $query_type
		 * @param $query_vars
		 */
		public function parse_posts_by_meta_query( $post_type, $query_type, $query_vars ) {

			foreach ( $query_vars as $key => $meta_args ) {

				if ( empty( $meta_args['options'] ) ) {
					continue;
				}

				$query_var = $meta_args['query_var'];

				foreach ( $meta_args['options'] as $option => $value ) {
					$current_row = array(
						'key'     => $query_var,
						'value'   => $value,
						'compare' => '=',
					);

					$args = array(
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'posts_per_page' => - 1,
						'fields'         => 'ids',
					);

					if ( is_array( $value ) ) {
						$current_row['compare'] = 'IN';
					}

					if ( filter_var( $meta_args['custom_checkbox'], FILTER_VALIDATE_BOOLEAN ) ) {
						$current_row['value']   = $value . '["]?;s:4:"true"';
						$current_row['compare'] = 'REGEXP';
					}

					if ( 'check-range' === $meta_args['filter_type'] ) {
						$current_row['compare'] = 'BETWEEN';
						$current_row['type']    = 'DECIMAL(16,4)';
						$current_row['value']   = explode( ':', $value );
					}

					$args['meta_query'][] = $current_row;

					$args = apply_filters( 'jet-smart-filters/indexer/meta-query-args', $args );

					$query = new WP_Query( $args );

					if ( ! empty( $query->posts ) ) {

						$row_key = $this->raw_key( array(
							$post_type,
							$query_type,
							$meta_args['query_var'],
							strtolower( $value ),
						) );

						$this->data_to_save[ $row_key ] = $query->posts;

					}

				}
			}

		}

		/**
		 * Get post by tax query
		 *
		 * @param $post_type
		 * @param $query_type
		 * @param $query_vars
		 */
		public function parse_posts_by_tax_query( $post_type, $query_type, $query_vars ) {

			foreach ( $query_vars as $tax => $terms ) {

				$terms_ids = array_column( $terms['options'], 'term_id' );

				foreach ( $terms_ids as $term ) {

					$args = array(
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'posts_per_page' => - 1,
						'fields'         => 'ids',
						'tax_query'      => array(
							array(
								'taxonomy' => $tax,
								'field'    => 'term_id',
								'terms'    => array( $term ),
							),
						),
					);

					$row_key = $this->raw_key( array( $post_type, $query_type, $tax, $term ) );

					$args = apply_filters( 'jet-smart-filters/indexer/tax-query-args', $args );

					$query = new WP_Query( $args );

					$this->data_to_save[ $row_key ] = $query->posts;

				}

			}

		}

		/**
		 * Prepare filter template arguments
		 *
		 * @param  [type] $args [description]
		 *
		 * @return [type]       [description]
		 */
		public function prepare_args( $args ) {

			$filter_id = $args['filter_id'];
			$post_type = $args['post_type'];
			$indexed_filters = [ 'color-image', 'select', 'checkboxes', 'check-range', 'radio' ];

			if ( ! $filter_id ) {
				return false;
			}

			$options    = array();
			$query_type = '';
			$query_var  = '';

			$source          = get_post_meta( $filter_id );
			$data_source     = ! empty( $source['_data_source'] ) ? $source['_data_source'][0] : '';
			$custom_checkbox = ! empty( $source['_is_custom_checkbox'] ) ? $source['_is_custom_checkbox'][0] : '';
			$filter_type     = get_post_meta( $filter_id, '_filter_type', true );

			if ( 'check-range' === $filter_type ){
				$data_source = 'manual_input';
			}

			if ( in_array( $filter_type, $indexed_filters ) ){
				switch ( $data_source ) {
					case 'taxonomies':
						$query_type = 'tax_query';
						$tax        = get_post_meta( $filter_id, '_source_taxonomy', true );
						$query_var  = $tax;

						$options = $this->prepare_tax_query_args( $tax, $post_type );
						break;
					case 'manual_input':
						$query_type  = 'meta_query';
						$query_var   = get_post_meta( $filter_id, '_query_var', true );
						$meta_values = $this->prepare_meta_query_args( $query_var, $post_type );

						if ( ! empty( $meta_values ) ) {
							switch ( $filter_type ) {
								case 'check-range' :
									$options = $this->get_check_range_options( $filter_id );
									break;
								case 'color-image' :
									$options = $this->get_color_image_options( $filter_id );
									break;
								default:
									$options = get_post_meta( $filter_id, '_source_manual_input', true );

									if ( ! empty( $options ) ) {
										$options = wp_list_pluck( $options, 'value' );
									}
									break;
							}

						}
						break;
					case 'posts':
						$query_type = 'meta_query';
						$query_var  = get_post_meta( $filter_id, '_query_var', true );
						$options    = $this->get_posts_options( $filter_id );
						break;
				}
			}
			
			return apply_filters( 'jet-smart-filters/indexer/final-query-args',
				array(
					'query_type'      => $query_type,
					'query_var'       => $query_var,
					'filter_type'     => $filter_type,
					'custom_checkbox' => $custom_checkbox,
					'options'         => $options,
				)
			);

		}

		/**
		 * Prepare meta query args
		 *
		 * @param $meta_key
		 * @param $post_type
		 *
		 * @return mixed
		 */
		public function prepare_meta_query_args( $meta_key, $post_type ) {

			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT pm.meta_value FROM {$wpdb->postmeta} pm
					    LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					    WHERE pm.meta_key = %s 
					    AND p.post_status = %s 
					    AND p.post_type = %s",
				$meta_key,
				'publish',
				$post_type
			);

			return $wpdb->get_results( $query );

		}

		/**
		 * Prepare tax query args
		 *
		 * @param $tax
		 * @param $post_type
		 *
		 * @return mixed
		 */
		public function prepare_tax_query_args( $tax, $post_type ) {

			global $wpdb;

			$query = $wpdb->prepare(
				"SELECT t.* from $wpdb->terms AS t
				        INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				        INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
				        INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
				        WHERE p.post_type IN('%s') AND tt.taxonomy IN('%s')
				        GROUP BY t.term_id",
				$post_type,
				$tax
			);

			return $wpdb->get_results( $query );

		}

		/**
		 * Prepare options for filter check range data type
		 *
		 * @param $filter_id
		 *
		 * @return array
		 */
		public function get_check_range_options( $filter_id ) {

			$options = get_post_meta( $filter_id, '_source_manual_input_range', true );

			foreach ( $options as $key => $option ) {
				$min             = ! empty( $option['min'] ) ? $option['min'] : 0;
				$max             = ! empty( $option['max'] ) ? $option['max'] : 100;
				$value           = $min . ':' . $max;
				$options[ $key ] = $value;
			}

			return $options;

		}

		/**
		 * Prepare options for filter color image data type
		 *
		 * @param $filter_id
		 *
		 * @return array
		 */
		public function get_color_image_options( $filter_id ) {

			$options = get_post_meta( $filter_id, '_source_color_image_input', true );
			$options = wp_list_pluck( $options, 'value' );

			return $options;

		}

		/**
		 * Prepare options for filter posts data type
		 *
		 * @param $filter_id
		 *
		 * @return array
		 */
		public function get_posts_options( $filter_id ) {

			$options   = array();
			$post_type = get_post_meta( $filter_id, '_source_post_type', true );
			$args      = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);

			$posts = get_posts( $args );

			if ( ! empty( $posts ) ) {
				$options = wp_list_pluck( $posts, 'ID' );
			}

			return $options;

		}

		/**
		 * Get post types that need index
		 *
		 * @return array
		 */
		public function get_enabled_post_types() {

			$post_types = jet_smart_filters()->settings->get( 'avaliable_post_types' );

			$args = array();

			foreach ( $post_types as $post_type => $enabled ) {
				if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) ) {
					array_push( $args, $post_type );
				}
			}

			return $args;

		}

		/**
		 * Update data in database  after indexing filters
		 *
		 * @param $post_types_args
		 */
		public function update_db_data( $post_types_args ) {

			foreach ( $post_types_args as $post_type => $args ) {
				$this->parse_posts_ids( $post_type, $args );
			}

			if ( ! jet_smart_filters()->db->is_table_exists( $this->data->table ) ) {
				jet_smart_filters()->db->create_table( $this->data->table );
			}

			jet_smart_filters()->db->clear_table( $this->data->table );

			foreach ( $this->data_to_save as $key => $value ) {
				jet_smart_filters()->db->update(
					$this->data->table,
					array( 'filter_key' => $key, 'filter_posts' => $value ),
					array( '%s', '%s' )
				);
			}

		}

		/**
		 * Add index filter button in manage post panel
		 *
		 * @param $post_type
		 * @param $which
		 */
		public function add_index_filters_button( $post_type, $which ) {

			if ( 'jet-smart-filters' !== $post_type ) {
				return;
			}

			printf( '<button type="button" id="jet-smart-filters-indexer-button" data-default-text="%1$s" data-loading-text="%2$s">%1$s</button>',
				esc_html__( 'Index Filters', 'jet-smart-filters' ),
				esc_html__( 'Indexing...', 'jet-smart-filters' )
			);

		}

		/**
		 * Return raw key
		 *
		 * @param $args
		 *
		 * @return string
		 */
		public function raw_key( $args ) {
			return implode( '/', $args );
		}

		/**
		 * Enqueue indexer scripts
		 */
		public function enqueue_scripts() {

			wp_enqueue_script(
				'jet-smart-filters-indexer',
				jet_smart_filters()->plugin_url( 'assets/js/indexer.js' ),
				array(),
				jet_smart_filters()->get_version(),
				true
			);

		}

		/**
		 * Load files
		 */
		public function load_files() {
			require jet_smart_filters()->plugin_path( 'includes/indexer/data.php' );
			require jet_smart_filters()->plugin_path( 'includes/indexer/controls.php' );
		}

	}
}