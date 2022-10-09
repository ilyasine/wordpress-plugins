<?php
/** 
* Plugin Name: benefit-calculator
* Plugin URI: https://wordpress.org/plugins/maintenance-coming-soon-redirect-animation/
* Author: Yassine Idrissi
* Author URI: https:\\profiles.wordpress.org/yasinedr
* Description: this plugin calculate the benefit for customers
* Version: 1.0.0
* License: GPLv3
* License URL: https://www.gnu.org/licenses/gpl-3.0.html
* text-domain: benefit-calculator
  
 */

// exit if accessed to file directly
defined('ABSPATH') || wp_die('You do not have acces');

// if class does not exist then create it
if( !class_exists("benefit_calculator") ) {


	class benefit_calculator {

        /**
		 * (php) initialize & create db_table
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */

        function init() {
			global $wpdb;
			
			// create target001 table 
			$tbl = "target001";
            // if there is no table with this name then create it
			if( $wpdb->get_var( "SHOW TABLES LIKE '". esc_sql($tbl) ."'" ) != esc_sql($tbl) ) {
				$sql = "create table $tbl ( id int auto_increment primary key, title varchar(100), type varchar(100) , amount varchar(20), modify_date datetime not null default '0000-00-00 00:00:00' )";
				$wpdb->query($sql);
			}
					
		}

        /**
		 * (php) add top-level administrative menu
		 *
		 * @since 1.0.0
		 * @return void
		 */
	
		function benefit_calculator_menu() {
	
			add_menu_page(
				'Benefit calculator Settings',
				'Benefit calculator',
				'manage_options',
				'Benefit calculator-settings',
				[$this,'print_admin_page'],
                'dashicons-database-add',
				2
			);			
		} 

        /**
		 * (php)  genereate table data
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */		
		
		function print_table_data(){
			global $wpdb ;
			$tadarab_ajax_nonce = wp_create_nonce( "tadarab_nonce" ); 
			?>

			<!-- transaction table -->

			<table class="widefat fixed">
				<tbody>				
					<tr id="tadarab-add-transaction" valign="middle"  class="<?php echo esc_attr($td_row_class); ?>">
                        <!-- Name of customer -->
                        <td class="column-tadarab-title">
							<input class="tadarab_mr_disabled_field" type="text" id="tadarab_title" name="tadarab_title" placeholder="<?php _e( "Enter Name of customer:" ); ?>">
						</td>

						<!-- amount -->
						<td class="column-tadarab-amount">
							<input class="tadarab_mr_disabled_field" type="number" id="tadarab_amount" name="tadarab_amount" placeholder="<?php _e( "Enter the amount:" ); ?>">
						</td>

						<td class="column-tadarab-actions">
							<span class='edit' <?php echo $tadarab_ajax_nonce; ?> id="tadarab_mr_add_ak_link">
								<a href="javascript:add_new_transaction( );"><?php _e( "Add a new transaction" ); ?></a>
							</span>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
                        <th class="column-tadarab-date"><?php _e( "Date" ); ?></th>
						<th class="column-tadarab-profit-loss"><?php _e( "Profit/Loss" ); ?></th>
						<th class="column-tadarab-amount"><?php _e( "Amount" ); ?></th>					
						<th class="column-tadarab-actions"><?php _e( "Actions ( Add / Edit / Delete )" ); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
                        <th class="column-tadarab-date">&nbsp;</th>
						<th class="column-tadarab-profit-loss">&nbsp;</th>
						<th class="column-tadarab-amount">&nbsp;</th>					
						<th class="column-tadarab-actions">&nbsp;</th>
					</tr>
				</tfoot>
				
				<tbody>
					<?php
					
					$sql   = "select * from target001";
					$fields = $wpdb->get_results($sql, OBJECT);
					$td_row_class = 'alternate';
					if( $fields ){
						foreach( $fields as $field ) : 
							$td_id = sanitize_text_field($field->id);
							$td_title = sanitize_text_field($field->title);
							$td_type = sanitize_text_field($field->type);
							$td_amount = sanitize_text_field($field->amount);
							$modify_date = sanitize_text_field($field->modify_date);				
						?>
							<tr id="tadarab-field-id-<?php echo esc_attr($td_id) ?>" valign="middle"  class="<?php echo esc_attr($td_row_class) ?>">
								<td class="column-tadarab-Date"><?php echo esc_html($modify_date); ?></td>
								<td class="column-tadarab-profit-loss"> <?php echo esc_attr($td_type) ?></td>
								<td class="column-tadarab-amount"><?php echo esc_html($td_amount); ?></td>
								
								<td class="column-tadarab-actions">
									
									<span class='edit'>
										<a class='submitedit' href="javascript:js_tadarab_edit_transaction( <?php echo esc_attr($td_id) ?>, '<?php echo addslashes( esc_attr($td_id) ) ?>' );" ><?php _e( "Edit" ); ?></a>
									</span>
									|
									<span class='delete'>
										<a class='submitdelete' href="javascript:tadarab_delete_transaction( <?php echo esc_attr($td_id) ?>, '<?php echo addslashes( esc_attr($td_id) ) ?>' );" ><?php _e( "Delete" ); ?></a>
									</span>

								</td>
							</tr>
							<?php
							$td_row_class = ( $td_row_class == '' ) ? 'alternate' : '';
						endforeach;
					}
					?>
				
				</tbody>
			</table>
		
			<?php
		}


		/**
		 * (php)  show update inputs
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */	

		function show_update_inputs(){
			?>
			<!-- update transaction  -->
			<table class="widefat fixed transaction_update">
				<tbody>			
				<?php
					global $wpdb;
	
					$td_id    = esc_sql( $_POST['tadarab_td_id'] );
					
					$sql   = "select * from target001 where id= '$td_id'";
					
					$fields = $wpdb->get_results($sql, OBJECT);

					
					foreach( $fields as $field ) : 
						
						$td_title = sanitize_text_field($field->title);
						$td_type = sanitize_text_field($field->type);
						$td_amount = sanitize_text_field($field->amount);
						$modify_date = sanitize_text_field($field->modify_date);
		
					?>
					<tr id="tadarab-add-transaction" valign="middle"  class="<?php echo esc_attr($td_row_class); ?>">
                        <!-- Name of customer -->
                        <td class="column-tadarab-title">
							<input class="tadarab_mr_disabled_field" type="text" id="updated_tadarab_title" value="<?php echo esc_attr($td_title)?>" name="updated_tadarab_title" placeholder="<?php _e( "Enter Name of customer:" ); ?>">
						</td>

						<!-- amount -->
						<td class="column-tadarab-amount">
							<input class="tadarab_mr_disabled_field" type="number" id="updated_tadarab_amount" value="<?php echo esc_attr($td_amount)?>" name="updated_tadarab_amount" placeholder="<?php _e( "Enter the amount:" ); ?>">
						</td>

						<td class="column-tadarab-actions">
							<span class='edit tadarab_edit' id="<?php echo $td_id; ?>">
								<a href="javascript:js_update_transaction( );"><?php _e( "Update transaction with ID :" )?> <?php echo esc_html($td_id); ?> </a>
							</span>
						</td>
					</tr>
					<?php 
					endforeach;
					?>
					
				</tbody>
			</table>
			<?php
		}

		/**
		 * (php)  add new transaction
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		
		function add_new_transaction() {

			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			
			global $wpdb ;
			$tadarab_ajax_nonce = wp_create_nonce('tadarab_nonce');

			check_ajax_referer( 'tadarab_nonce', 'security' );

			// get customer name and amount from input
			$title      = esc_sql( stripslashes( $_POST['tadarab_title'] ) );
			$amount 	= esc_sql( stripslashes( trim( $_POST['tadarab_amount'] ) ) );
			$type = '';
			// calculate profit/loss
			if ( $amount <= 0 ) {
				$type = 'expense';
			}
			else {
				$type = 'income';
			}
			$sql        = "insert into target001 ( id , title, type , amount, modify_date ) values ( NULL , '$title', '$type' , '$amount', NOW() )";
			$rs         = $wpdb->query( $sql );

			if( $rs ){
				// send table data
				$this->print_table_data();

			}else{
				echo __( 'Unable to add transaction because of a database error. Please reload the page.' );
			}
			die();
		}


		/**
		 * (php)  update transaction
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */

		function update_transaction(){

			global $wpdb , $tadarab_ajax_nonce;

			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'tadarab_nonce', 'security' );
			
			// id to be updated
			$updated_td_id    =  esc_sql( stripslashes( $_POST['updated_tadarab_td_id'] ));
			// get customer name and amount from input
			$updated_title      = esc_sql( stripslashes( $_POST['updated_tadarab_title'] ) );
			$updated_amount 	= esc_sql( stripslashes( trim( $_POST['updated_tadarab_amount'] ) ) );
			$updated_type = '';
			// recalculate profit/loss
			if ( $updated_amount <= 0 ) {
				$updated_type = 'expense';
			}
			else {
				$updated_type = 'income';
			}

			
			// Update SQL Data
			$sql       = "UPDATE target001  SET  title= '$updated_title' , type= '$updated_type' , amount= '$updated_amount' , modify_date= NOW()   where id= '$updated_td_id'";
			$rs         = $wpdb->query( $sql ); 

			if( $rs ){
				// send data to table
				$this->print_table_data();

			}else{
				
				echo __( 'Unable to update Transaction because of a database error. Please reload the page.' );

			}
			wp_die();
			
		}

		/**
		 * (php)  delete Transaction
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */

		function delete_transaction(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			
			global $wpdb;
			
			$td_id    = esc_sql( $_POST['tadarab_td_id'] );
			$sql       = "delete from target001 where id = '$td_id'";
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				$this->print_table_data();
			}else{
				echo __( 'Unable to delete Transaction because of a database error. Please reload the page.' );
			}
			die();
		}

		/**
		 * (php)  edit Transaction
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		
		function edit_transaction(){

			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			
			global $wpdb;

			$td_id    = esc_sql( $_POST['tadarab_td_id'] );

			$sql   = "select * from target001 where id= '$td_id'";
					
			$fields = $wpdb->get_results($sql, OBJECT);

			$rs     = $wpdb->query( $sql );

			//echo 'td_id : '. $td_id ;

			if( $rs ){
				$this->show_update_inputs();
				
			}else{
				echo __( 'Unable to edit Transaction because of a database error. Please reload the page.' );
			}

			wp_die();
		}
	

        /**
		 * (php)  create the admin page
		 *
		 * @since 1.0.0
		 * @access public
		 * @return array
		 */
		
		function print_admin_page() {
			global $wpdb;
			global $tadarab_ajax_nonce;
			$tadarab_ajax_nonce = wp_create_nonce('tadarab_nonce','security');

			// display update notice 
			echo '<div class="updated" style="display: none" ><p><strong>Settings Saved</strong></p></div>';

			 ?>
			 <script type="text/javascript" charset="utf-8">
				/**
				 * (js) add new transaction
				 *
				 * @since 1.0.0
				 * @return void
				 */
				
				function add_new_transaction () {
					// validate entries before posting ajax call
					var error_msg = '';
					if ( jQuery('#tadarab_title').val() == '' ) 
						error_msg += '<?php echo esc_js( __( "You must enter a Customer Name" ) ); ?>.\n';
					
					if ( jQuery('#tadarab_amount').val() == '' ) 
						error_msg += '<?php echo esc_js( __( "You must enter an Amount" ) ); ?>.\n';
					
					/* Allow only positive and negative numbers */
					if(!/^-?\d*\.?\d{0,9}$/.test(jQuery('#tadarab_amount').val())){
						alert("Please only enter numeric characters only for Amount!")
					}

					if ( error_msg != '' ) {
						alert( '<?php echo esc_js( __( "There is a problem with the information you have entered" ) ); ?>.\n\n' + error_msg );
					} else {

						// prepare ajax data
						
						jQuery.ajax({
							url: ajaxurl,
							data: {
								action: 'tadarab_add_new_transaction',
								security:		'<?php echo $tadarab_ajax_nonce; ?>',
								tadarab_title:	jQuery('#tadarab_title').val(),
								tadarab_amount:	jQuery('#tadarab_amount').val() 
							},
							type: 'post',

							success: function (response) {
								// set section to loading img
								var img_url = '<?php echo plugins_url( '/ajax_loader_16x16.gif', __FILE__ ); ?>';
								jQuery( '#tadarab_mr_ak_tbl_container' ).html('<img src="' + img_url + '">'); 
								
								// update front table
								jQuery('#tadarab_mr_ak_tbl_container').html( response );
								
							},
							error: function (result) {
								console.log(result);
								console.log('fail');
							},
						})
				
					}
				}

				/**
				 * (js) delete Transaction
				 *
				 * @since 1.0.0
				 * @return void
				 */
				
				function tadarab_delete_transaction ( td_id ) {
				if ( confirm('<?php echo esc_js( __( "You are about to delete the Transaction Id:" ) ); ?>\n\n\'' + td_id + '\'\n\n') ) {
					// prepare ajax data
					jQuery.ajax({
						url: ajaxurl,
						data: {
							action: 'tadarab_delete_transaction',
							security:		'<?php echo $tadarab_ajax_nonce; ?>',
							tadarab_td_id:   td_id
						},
						type: 'post',

						success: function (response) {
							// set section to loading img
							var img_url = '<?php echo plugins_url( '/ajax_loader_16x16.gif', __FILE__ ); ?>';
							jQuery( '#tadarab_mr_ak_tbl_container' ).html('<img src="' + img_url + '">'); 
							
							// update front table
							jQuery('#tadarab_mr_ak_tbl_container').html( response );
							
						},
						error: function (result) {
							console.log(result);
							console.log('fail');
						},
					})
				}
			}

			/**
			 * (js) edit transaction
			 *
			 * @since 1.0.0
			 * @return void
			 */

			function js_tadarab_edit_transaction( td_id ) {

			// when click on edit button we can edit name of customer and amount

			jQuery.ajax({
						url: ajaxurl,
						data: {
							action: 'tadarab_edit_transaction',
							security:		'<?php echo $tadarab_ajax_nonce; ?>',
							tadarab_td_id:   td_id
						},
						type: 'post',

						success: function (response) {
							// set section to loading img
							var img_url = '<?php echo plugins_url( '/ajax_loader_16x16.gif', __FILE__ ); ?>';
							jQuery( '#tadarab_mr_updated_tbl_container' ).html('<img src="' + img_url + '">'); 
							
							// show upadte input table
							jQuery('#tadarab_mr_updated_tbl_container').html( response );
						
							//console.log(response);

						},
						error: function (result) {
							console.log(result);
							console.log('fail');
						},
					})

			}

			/**
			 * (js) Update transaction
			 *
			 * @since 1.0.0
			 * @return void
			 */
			
			function js_update_transaction ( td_id ) {
				// validate entries before posting ajax call
				var error_msg = '';
				if ( jQuery('#updated_tadarab_title').val() == '' ) 
					error_msg += '<?php echo esc_js( __( "You must enter a Customer Name" ) ); ?>.\n';
				
				if ( jQuery('#updated_tadarab_amount').val() == '' ) 
					error_msg += '<?php echo esc_js( __( "You must enter an Amount" ) ); ?>.\n';
				
				/* Allow only positive and negative numbers */
				if(!/^-?\d*\.?\d{0,9}$/.test(jQuery('#updated_tadarab_amount').val())){
					alert("Please only enter numeric characters only for Amount!")
				}

				if ( error_msg != '' ) {
					alert( '<?php echo esc_js( __( "There is a problem with the information you have entered" ) ); ?>.\n\n' + error_msg );
				} else {

					//console.log(jQuery('.tadarab_edit').attr('id'));
					// prepare ajax data
					
					jQuery.ajax({
						url: ajaxurl,
						data: {
							action: 'tadarab_update_transaction',
							security:		'<?php echo $tadarab_ajax_nonce; ?>',
							updated_tadarab_title:	jQuery('#updated_tadarab_title').val(),
							updated_tadarab_amount:	jQuery('#updated_tadarab_amount').val(),
							updated_tadarab_td_id: jQuery('.tadarab_edit').attr('id'),
						},
						type: 'post',


						success: function (response ) {
							
							// update front table
							jQuery('#tadarab_mr_ak_tbl_container').html( response );

							jQuery('.transaction_update').delay(3000).fadeOut("slow");
							//console.log(response);
							
						},
						error: function (result) {
							console.log(result);
							console.log('fail');
						},
					})
			
				}
			}
			</script> <!-- end of script tag -->


			<div class="wrap">
				
				<h1 class="big-title">Benefit calculator Settings</h1>
				
				<p><?php _e( "This plugin is for Tadarab Benefit calculator." ); ?></p>
												
				<div id="tadarab_main_options">				
					
                    <div class="tadarab_mr_admin_section">
						<h3 class="big-title"><?php _e( "Table Data:" ); ?></h3>
							
						<div id="tadarab_mr_ak_tbl_container">
							<?php $this->print_table_data(); ?>
						</div>

						<div id="tadarab_mr_updated_tbl_container">
							<?php $this->show_update_inputs(); ?>
						</div>

					</div>

				</div>
					
			</div>
				
			<?php
	
			
		} // end function print_admin_page()

    }

}// end of class 

if (class_exists("benefit_calculator")) {
	$my_benefit_calculator = new benefit_calculator();
}

if (!function_exists("benefit_calculator_menu")) {

	/**
	 * (php) initialize the admin and users panel
	 *
	 * @since 1.0.0
	 * @return void
	 */

	function benefit_calculator_menu() {
        // check if the user has right access
		if( current_user_can('manage_options') ) {
			
			global $tadarab_ajax_nonce; 
				 $tadarab_ajax_nonce = wp_create_nonce( "tadarab_nonce" ); 
			
			// if the $my_benefit_calculator not exist then exit .
			if( !isset($my_benefit_calculator) ) return;
		
			if (function_exists('add_options_page')) {
				add_options_page( 
					__("Benefit calculator Options" ),
					__("Benefit calculator" ), 
						'manage_options', 
						'Benefit8calculator-settings', 
						array( $my_benefit_calculator, 'print_admin_page' ));
			}
		}
	}
}

// Actions & filters

add_action( 'admin_menu',   array( $my_benefit_calculator, 'benefit_calculator_menu' ));
// hook will run after plugin activation
register_activation_hook( __FILE__, array( $my_benefit_calculator, 'init' ) );
//ajax_actions
add_action( 'wp_ajax_tadarab_add_new_transaction', array( $my_benefit_calculator ,'add_new_transaction' ) );
add_action( 'wp_ajax_tadarab_delete_transaction', array( $my_benefit_calculator ,'delete_transaction' ) );
add_action( 'wp_ajax_tadarab_edit_transaction', array( $my_benefit_calculator ,'edit_transaction' ) );
add_action( 'wp_ajax_tadarab_update_transaction', array( $my_benefit_calculator ,'update_transaction' ) );
