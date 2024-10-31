<?php
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();
	
// Delete option from option table
delete_option('client_id');
delete_option('client_secret');
delete_option('api_key');
delete_option( 'token' );
?>