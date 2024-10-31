<?php if ( $data ) : ?>
	<?php if ( $data['count_posts_db'] < $data['published_posts'] ) : ?>
		<div class="message">
			<p><strong>Information</strong> : <?php echo $data['count_posts_db']; ?> articles récupérés sur <?php echo $data['published_posts']; ?></p>
		</div>
	<?php endif; ?>
	<div class="cols cols3">
		<div class="col">
			<p><?php echo $data['count_posts']; ?></p>
			<p><?php echo ( $data['count_posts'] > 1 ) ? __( 'Posts', PLUGIN_SLUG ) : __( 'Post', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['avg_time_post']; ?></p>
			<p><?php _e( 'Average time on post', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col no-margin">
			<p><?php echo $data['visits']; ?></p>
			<p><?php echo ( $data['visits'] > 1 ) ? __( 'Visits', PLUGIN_SLUG ) : __( 'Visit', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['mobile_visits']; ?>%</p>
			<p><?php echo ( $data['mobile_visits'] > 1 ) ? __( 'Visits from mobile device', PLUGIN_SLUG ) : __( 'Visit from mobile device', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['bounce_rate']; ?>%</p>
			<p><?php _e( 'Bounce rate', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col no-margin">
			<p><?php echo $data['comments']; ?></p>
			<p><?php echo ( $data['comments'] > 1 ) ? __( 'Comments', PLUGIN_SLUG ) : __( 'Comment', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['facebook']; ?></p>
			<p><?php echo ( $data['facebook'] > 1 ) ? __( 'Facebook shares', PLUGIN_SLUG ) : __( 'Facebook share', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['twitter']; ?></p>
			<p><?php echo ( $data['twitter'] > 1 ) ? __( 'Twitter shares', PLUGIN_SLUG ) : __( 'Twitter share', PLUGIN_SLUG ); ?></p>
		</div>
	</div>
	<h2><?php _e( 'Activity evolution', PLUGIN_SLUG ); ?></h2>
	<div id="chart"></div>
	<center>
		<h1><?php _e( 'More...', PLUGIN_SLUG ); ?></h1>
		<a class="button-primary" href="<?php echo $data['my_dashboard_link']; ?>"><?php _e( 'My Dashboard', PLUGIN_SLUG ); ?></a>
		<a class="button-primary" href="<?php echo $data['averages_link']; ?>"><?php _e( 'Averages', PLUGIN_SLUG ); ?></a>
	</center>
<?php else : ?>
	<p><?php _e( 'No posts found', PLUGIN_SLUG ); ?></p>
<?php endif; ?>