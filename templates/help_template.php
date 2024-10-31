<h2><?php _e( 'Help', PLUGIN_SLUG ); ?></h2>
<?php include 'promo.php'; ?>
<ol>
	<li>
		<p><?php _e( 'First, to configure the plugin, go to Google Console API and accept the terms', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/1.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Go to "Services" section and activate "Analytics API"', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/2.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Go to "API Access" section and click on "Create an OAuth 2.0 client ID..."', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/3.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Fill the "Product name" field and click "Next"', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/4.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Choose "Web application", select "http://" and type your website url', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/5.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Click on "more options" and add "/wp-admin/options-general.php?page=PSFA" at the end of your website url for the "Authorized Redirect URIs" field. Next click on "Create client ID"', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/6.jpg" /></p>
	</li>
	<li>
		<p><?php _e( 'Finaly, open the Google Analytics site and select the account you want to monitor. The 8 digits after the "p" at the end the url of your browser is your profile ID', PLUGIN_SLUG ); ?></p>
		<p><img src="<?php echo DIR_IMG; ?>help/7.jpg" /></p>
	</li>
</ol>