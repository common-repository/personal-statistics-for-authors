<?php if ( $data ) : ?>
	<div class="wrap">
		<div id="icon-32" class="icon32">
			<img src="<?php echo DIR_IMG; ?>/icon32.png" width="32" height="32" />
		</div>
		<h2><?php _e( 'Average', PLUGIN_SLUG ); ?></h2>
		<?php if ( $data['count_posts_db'] < $data['published_posts'] ) : ?>
			<div class="message">
				<p><strong>Information</strong> : <?php echo $data['count_posts_db']; ?> articles récupérés sur <?php echo $data['published_posts']; ?></p>
			</div>
		<?php endif; ?>
		<?php include 'promo.php'; ?>
		<h3><?php _e( 'General statistics', PLUGIN_SLUG ); ?></h3>
		<div class="cols cols4">
			<div class="col">
				<p><?php echo $data['posts_per_author']; ?></p>
				<p><?php _e( 'Posts by authors', PLUGIN_SLUG); ?></p>
			</div>
			<div class="col">
				<p><?php echo $data['comments']; ?></p>
				<p><?php echo ( $data['comments'] > 1 ) ? __( 'Comments', PLUGIN_SLUG ) : __( 'Comment', PLUGIN_SLUG ); ?></p>
			</div>
			<div class="col">
				<p><?php echo $data['avg_time_post']; ?></p>
				<p><?php _e( 'Average time', PLUGIN_SLUG); ?></p>
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
				<p><?php echo $data['facebook']; ?></p>
				<p><?php echo ( $data['facebook'] > 1 ) ? __( 'Facebook shares', PLUGIN_SLUG ) : __( 'Facebook share', PLUGIN_SLUG ); ?></p>
			</div>
			<div class="col">
				<p><?php echo $data['twitter']; ?></p>
				<p><?php echo ( $data['twitter'] > 1 ) ? __( 'Twitter shares', PLUGIN_SLUG ) : __( 'Twitter share', PLUGIN_SLUG ); ?></p>
			</div>
		</div>
		<h3><?php _e( 'More read posts', PLUGIN_SLUG ); ?></h3>
		<table class="widefat">
			<thead>
				<tr valign="top">
					<th class="row-title"><?php _e( 'Title', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Date', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Author name', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Visits', PLUGIN_SLUG ); ?></th>
				</tr>
			</thead>
			<?php if ( count( $data['more_read'] ) > 0 ) : ?>
				<?php foreach ( $data['more_read'] as $key => $value) : ?>
					<tr valign="top">
						<th>
							<a href="<?php echo $data['my_dashboard_link']; ?>&post_id=<?php echo $value->post_id; ?>">
								<?php echo get_the_title( $value->post_id ); ?>
							</a>
						</th>
						<th><?php echo get_the_time( 'd/m/Y', $value->post_id ); ?></th>
						<td><?php $userdata = get_userdata( $value->user_id ); echo $userdata->display_name; ?></td>
						<td><?php echo $value->visits; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr valign="top">
					<th colspan="2"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
				</tr>
			<?php endif; ?>
		</table>
		<h3><?php _e( 'More commented posts', PLUGIN_SLUG ); ?></h3>
		<table class="widefat">
			<thead>
				<tr valign="top">
					<th class="row-title"><?php _e( 'Title', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Date', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Author name', PLUGIN_SLUG ); ?></th>
					<th class="row-title"><?php _e( 'Comments', PLUGIN_SLUG ); ?></th>
				</tr>
			</thead>
			<?php if ( count( $data['more_commented'] ) > 0 ) : ?>
				<?php foreach ( $data['more_commented'] as $key => $value) : ?>
					<tr valign="top">
						<th>
							<a href="<?php echo $data['my_dashboard_link']; ?>&post_id=<?php echo $value->post_id; ?>">
								<?php echo get_the_title( $value->post_id ); ?>
							</a>
						</th>
						<th><?php echo get_the_time( 'd/m/Y', $value->post_id ); ?></th>
						<td><?php $userdata = get_userdata( $value->user_id ); echo $userdata->display_name; ?></td>
						<td><?php echo $value->comments; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr valign="top">
					<th colspan="2"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
				</tr>
			<?php endif; ?>
		</table>
	</div>
<?php else : ?>
	<p><?php _e( 'No datas', PLUGIN_SLUG ); ?></p>
<?php endif; ?>