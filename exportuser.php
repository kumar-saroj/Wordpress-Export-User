<?php
// Add Button into admin side user list
add_action('admin_footer', 'eudc_export_users');
function eudc_export_users() {
	$screen = get_current_screen();
	// Only add to users.php page
	if ( $screen->id != "users" )
		return;
?>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			jQuery('.tablenav.top .clear, .tablenav.bottom .clear').before('<form action="#" method="POST"><input type="hidden" id="eudc_export_csv" name="eudc_export_csv" value="1" /><input class="button button-primary user_export_button" type="submit" value="<?php esc_attr_e('Export All as CSV', 'eudc');?>" /></form>');
		});
	</script>
<?php
}

//you can use admin_init as well
add_action('admin_init', 'export_csv');
function export_csv() {
	if (!empty($_POST['eudc_export_csv'])) {
		if (current_user_can('manage_options')) {
			// set header for CSV file
			header("Content-type: application/force-download");
			header('Content-Disposition: inline; filename="users_'.date('Y_m_d_H_i_s').'.csv"');

			$args = array (
				'order' => 'ASC',
				'orderby' => 'display_name',
				'fields' => 'all',
			);
			// The User Query
			$wp_users = get_users( $args );

			echo '" User ID "," User Name "," First Name "," Last Name "," Email ID "," Nick Name "," User Role "," Registered Date "' . "\r\n";
			// Array of WP_User objects.
			foreach ( $wp_users as $user ) {
				$user_id = $user->ID;
				$user_name = $user->user_login;
				$reg_date = $user->user_registered;
				$meta = get_user_meta($user_id);
				$role = $user->roles;
				$email = $user->user_email;

				$first_name = ( isset($meta['first_name'][0]) && $meta['first_name'][0] != '' ) ? $meta['first_name'][0] : '' ;
				$last_name  = ( isset($meta['last_name'][0]) && $meta['last_name'][0] != '' ) ? $meta['last_name'][0] : '' ;
				$nickname = ( isset($meta['nickname'][0]) && $meta['nickname'][0] != '' ) ? $meta['nickname'][0] : '' ;

				echo '"'.$user_id.'","'.$user_name.'","'.$first_name.'","'.$last_name.'","'.$email.'","'.$nickname.'","'.ucfirst($role[0]).'","'.$reg_date.'"'."\r\n";
			}
			exit();
		}
	}
}

?>