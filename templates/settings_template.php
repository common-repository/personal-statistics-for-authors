<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Personal Statistics for Authors options', PLUGIN_SLUG); ?></h2>
	<?php include 'promo.php'; ?>
	<form action="options.php" method="POST">
		<?php settings_fields( PLUGIN_SLUG ); ?>
		<?php do_settings_sections( PLUGIN_SLUG ); ?>
		<?php submit_button(); ?>
	</form>
	<?php if ( get_option( 'client_id' ) &&
			   get_option( 'client_secret' ) &&
			   get_option( 'api_key' ) &&
			   get_option( 'profile_id' ) ) : ?>
		<?php if ( ! empty( $authUrl ) ) : ?>
			<p><?php _e( 'Click the button below to access the information from your Google Account and use:', PLUGIN_SLUG ); ?></p>
			<a class="button-secondary" href="<?php echo $authUrl; ?>"><?php _e( 'Connect Me !', PLUGIN_SLUG ); ?></a>
		<?php else : ?>
		<p><?php _e( 'You can disconnect your Google account by clicking the button below:', PLUGIN_SLUG ); ?></p>
			<a class="button-secondary" href="options-general.php?page=<?php echo PLUGIN_SLUG; ?>&logout=true"><?php _e( 'Logout', PLUGIN_SLUG ); ?></a>
		<?php endif; ?>
	<?php endif; ?>
</div>