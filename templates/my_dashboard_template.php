<?php if ( $data ) : ?>
	<div class="wrap">
		<div id="icon-32" class="icon32">
			<img src="<?php echo DIR_IMG; ?>/icon32.png" width="32" height="32" />
		</div>
		<h2><?php _e( 'My Dashboard', PLUGIN_SLUG ); ?></h2>
		<?php if ( $data['count_posts_db'] < $data['published_posts'] ) : ?>
			<div class="message">
				<p><strong>Information</strong> : <?php echo $data['count_posts_db']; ?> articles récupérés sur <?php echo $data['published_posts']; ?></p>
			</div>
		<?php endif; ?>
		<?php include 'promo.php'; ?>
		<h3><?php _e( 'General statistics', PLUGIN_SLUG ); ?></h3>
		<div class="cols cols4">
			<div class="col">
				<p><?php echo $data['count_posts']; ?></p>
				<p><?php echo ( $data['count_posts'] > 1 ) ? __( 'Posts', PLUGIN_SLUG ) : __( 'Post', PLUGIN_SLUG ); ?></p>
			</div>
			<div class="col">
				<p><?php echo $data['comments']; ?></p>
				<p><?php echo ( $data['comments'] > 1 ) ? __( 'Comments', PLUGIN_SLUG ) : __( 'Comment', PLUGIN_SLUG ); ?></p>
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
			
			<div class="col">
				<p><?php echo $data['facebook']; ?></p>
				<p><?php echo ( $data['facebook'] > 1 ) ? __( 'Facebook shares', PLUGIN_SLUG ) : __( 'Facebook share', PLUGIN_SLUG ); ?></p>
			</div>
			<div class="col no-margin">
				<p><?php echo $data['twitter']; ?></p>
				<p><?php echo ( $data['twitter'] > 1 ) ? __( 'Twitter shares', PLUGIN_SLUG ) : __( 'Twitter share', PLUGIN_SLUG ); ?></p>
			</div>
		</div>
		<h3><?php _e( 'My posts', PLUGIN_SLUG ); ?></h3>
		<table class="widefat" id="table1">
			<thead>
				<tr valign="top">
					<th class="row-title"><span><?php _e( 'Title', PLUGIN_SLUG ); ?></span></th>
					<th class="row-title"><span><?php _e( 'Date', PLUGIN_SLUG ); ?></span></th>
					<th class="row-title"><span><?php _e( 'Visits', PLUGIN_SLUG ); ?></span></th>
					<th class="row-title"><span><?php _e( 'Average time', PLUGIN_SLUG ); ?></span></th>
					<th class="row-title"><span><?php _e( 'Facebook', PLUGIN_SLUG ); ?></span></th>
					<th class="row-title"><span><?php _e( 'Twitter', PLUGIN_SLUG ); ?></span></th>
				</tr>
			</thead>
			<?php foreach ($data['posts'] as $key => $d) : ?>
				<tr valign="top">
					<th>
						<a href="<?php echo $data['my_dashboard_link']; ?>&post_id=<?php echo $d->post_id; ?>">
							<?php echo get_the_title( $d->post_id ); ?>
						</a>
					</th>
					<th><?php echo get_the_time( 'd/m/Y', $d->post_id ); ?></th>
					<th><?php echo $d->visits; ?></th>
					<th><?php echo $this->convert_time( $d->avg_time_post ); ?></th>
					<th><?php echo $d->facebook; ?></th>
					<th><?php echo $d->twitter; ?></th>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
<?php else : ?>
	<p><?php _e( 'No posts found', PLUGIN_SLUG ); ?></p>
<?php endif; ?>