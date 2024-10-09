<?php 


/**
 * summary
 */
class SEDiscount {

	public $postTypeSlug;
	public $postTypeLngTag;

	/**
	 * summary
	 */
	public function __construct($postTypeSlug) {
		$this->postTypeSlug = $postTypeSlug;
		$this->postTypeLngTag = $this->postTypeSlug;
		$this->init();
	}

	public function init()
	{
		add_action('init', array( $this, 'se_discount_register_func'), 1); 


		add_filter( 'manage_edit-'.$this->postTypeSlug.'_columns', array( $this, 'se_discount_edit_columns') );
		add_action( 'manage_posts_custom_column', array( $this, 'se_discount_column_display'), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'se_discount_meta_boxes') );

		add_action( 'save_post', array( $this, 'se_discount_save_post_meta_func') );
		
	}

	public function se_discount_register_func() {  

		$labels = array(
			'name'			   => esc_html__( 'SE Discounts', $this->postTypeLngTag ),
			'singular_name'	  => esc_html__( 'Discount', $this->postTypeLngTag ),
			'add_new'			=> esc_html__( 'Add New Discount', $this->postTypeLngTag ),
			'add_new_item'	   => esc_html__( 'Add New Discount', $this->postTypeLngTag ),
			'edit_item'		  => esc_html__( 'Edit Discount', $this->postTypeLngTag ),
			'new_item'		   => esc_html__( 'Add New Discount', $this->postTypeLngTag ),
			'view_item'		  => esc_html__( 'View Discount', $this->postTypeLngTag ),
			'search_items'	   => esc_html__( 'Search Discounts', $this->postTypeLngTag ),
			'not_found'		  => esc_html__( 'No Discounts found', $this->postTypeLngTag ),
			'not_found_in_trash' => esc_html__( 'No Discounts found in trash', $this->postTypeLngTag )
		);

		$args = array(  
			'labels'		  => $labels,
			'public'		  => true,  
			'show_ui'		 => true,  
			'capability_type' => 'post',  
			'hierarchical'	=> false,  
			'menu_icon'	   => 'dashicons-paperclip',
			'rewrite'		 => array('slug' => $this->postTypeSlug), // Permalinks format
			'supports'		=> array( 'title' ),
		);  

		register_post_type( $this->postTypeSlug , $args );  
		// remove_post_type_support( 'curated-links' , 'editor' );

	}  
  

	function se_discount_edit_columns( $columns ) {
		$columns = array(
			"cb"		  => "<input type=\"checkbox\" />",
			"title"	   => esc_html__('Title', $this->postTypeLngTag),
			"date"		=> esc_html__('Date', $this->postTypeLngTag),
		);
		return $columns;
	}


	/* ----------------------------------------------------- */

	function se_discount_column_display( $columns, $post_id ) {
		switch ( $columns ) {

			case "category":	 
				if ( $category_list = get_the_term_list( $post_id, 'category', '', ', ', '' ) ) {
					echo $category_list; // No need to escape
				} else {
					echo esc_html__('None', $this->postTypeLngTag);
				}
			break;	   
		}
	}



	/* CONTACT META BOXES */
	function se_discount_meta_boxes() {
		add_meta_box( 'se_discount_meta_box', 'Discount Settngs', array($this, 'se_discount_metabox_callback_func'), $this->postTypeSlug );
	}

	function se_discount_metabox_callback_func( $post ) {

		wp_nonce_field( 'se_discount_save_post_meta', 'se_discount_meta_box_nonce' );

		wp_enqueue_style( 'jquery-ui-custom' );
		wp_enqueue_style( 'jquery-ui-timepicker-addon' );

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-timepicker-addon'); 

		wp_enqueue_script('admin-discount-manager'); 

		$discount_data = se_get_discount_meta($post->ID);

		?>
		<div class="discount-metabox">
			<?php $data=[]; for ($i = 0; $i < 100 ; $i++) {
				$data[$i] = $i;
			} ?>
			<?php se_get_admin_select_field('Discount Type:', se_get_discount_types(), $discount_data['type'], "discount_type_field", "discount-type"); ?>
			<?php se_get_admin_select_field('Discount Percentage:', $data, $discount_data['percentage'], "discount_percentage_field", "discount-percentage"); ?>
			<?php se_get_admin_text_field('Fixed Amount:', 'discount_amount_field', $discount_data['amount'], 'discount-amount'); ?>
			<?php se_get_admin_text_field('Discount Promo code:', 'discount_promo_code_field', $discount_data['promo_code']); ?>

			<div class="se-discount-validation-time">
				
				<div class="">
					<label for="se-datetime-from">Valid From</label>
					<input type="text" name="discount_valid_from_field" value="<?php echo $discount_data['valid_from'] ? $discount_data['valid_from'] : ''; ?>" id="se-datetime-from" class="ee-datepicker" data-related-field="#se-datetime-to">
				</div>
				<div class="">
					<label for="se-datetime-to">Valid To</label>
					<input type="text" name="discount_valid_to_field" value="<?php echo $discount_data['valid_to'] ? $discount_data['valid_to'] : ''; ?>" id="se-datetime-to" class="ee-datepicker" data-related-field="#se-datetime-from">
				</div>
			</div>

		</div>
		<?php 
	}


	function se_discount_save_post_meta_func( $post_id ) {

		if( ! isset( $_POST['se_discount_meta_box_nonce'] ) )
			return;	 
		if( ! wp_verify_nonce( $_POST['se_discount_meta_box_nonce'], 'se_discount_save_post_meta') ) 
			return;	 
		if( ! current_user_can( 'edit_post', $post_id ) ) 
			return;

		update_post_meta( $post_id, 'discount_type', $_POST['discount_type_field'] );
		update_post_meta( $post_id, 'discount_percentage', $_POST['discount_percentage_field'] );
		update_post_meta( $post_id, 'discount_amount', $_POST['discount_amount_field'] );
		update_post_meta( $post_id, 'discount_promo_code', $_POST['discount_promo_code_field'] );
		update_post_meta( $post_id, 'discount_valid_from', $_POST['discount_valid_from_field'] );
		update_post_meta( $post_id, 'discount_valid_to', $_POST['discount_valid_to_field'] );

	}

	
} // end of SEDiscount


new SEDiscount(SCOUT_DISCOUNT_POST_TYPE);



