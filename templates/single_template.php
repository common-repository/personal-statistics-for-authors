<div class="wrap">
	<div id="icon-32" class="icon32">
		<img src="<?php echo DIR_IMG; ?>/icon32.png" width="32" height="32" />
	</div>
	<h2><?php echo get_the_title( $data['post_id'] ); ?></h2>
	<p>
		<?php _e( 'Latest update:', PLUGIN_SLUG ); ?>
		<?php echo (!empty($data['date_update'])) ? date('d/m/Y H:i', $data['date_update']) : _e('None', PLUGIN_SLUG); ?>
	</p>
	<div class="top_back">
		<a class="button-secondary" href="<?php echo $data['my_dashboard_link']; ?>">
			<?php _e( 'Back', PLUGIN_SLUG ); ?>
		</a>
	</div>
	<?php include 'promo.php'; ?>
	<h3><?php _e( 'General statistics', PLUGIN_SLUG ); ?></h3>
	<div class="cols cols4">
		<div class="col">
			<p><?php echo get_the_time( 'd/m/Y', $data['post_id'] ); ?></p>
			<p><?php _e( 'Created', PLUGIN_SLUG); ?></p>
		</div>
		<div class="col">
			<p><?php echo $data['comments']; ?></p>
			<p><?php echo ( $data['comments'] > 1 ) ? __( 'Comments', PLUGIN_SLUG ) : __( 'Comment', PLUGIN_SLUG ); ?></p>
		</div>
		<div class="col">
			<p><?php echo $this->convert_time( $data['avg_time_post'] ); ?></p>
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
	<h3><?php _e( 'Keywords', PLUGIN_SLUG ); ?></h3>
	<table class="widefat">
		<thead>
			<tr valign="top">
				<th class="row-title"><?php _e( 'Keywords', PLUGIN_SLUG ); ?></th>
				<th class="row-title"><?php _e( 'Visits', PLUGIN_SLUG ); ?></th>
			</tr>
		</thead>
		<?php if ( count( json_decode( $data['keywords'] ) ) > 0 ) : ?>
			<?php foreach (array_slice(json_decode($data['keywords']), 0, 10) as $key => $value) : ?>
				<tr valign="top">
					<th><?php echo $value->keyword; ?></th>
					<th><?php echo $value->visits; ?></th>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr valign="top">
				<th colspan="2"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
			</tr>
		<?php endif; ?>
	</table>
	<h3><?php _e( 'Traffic sources', PLUGIN_SLUG ); ?></h3>
	<table class="widefat">
		<thead>
			<tr valign="top">
				<th class="row-title"><?php _e( 'Referring site', PLUGIN_SLUG ); ?></th>
				<th class="row-title"><?php _e( 'Visits', PLUGIN_SLUG ); ?></th>
			</tr>
		</thead>
		<?php if ( count( json_decode( $data['traffic_sources'] ) ) > 0 ) : ?>
			<?php foreach (array_slice(json_decode($data['traffic_sources']), 0, 10) as $key => $value) : ?>
				<tr valign="top">
					<th><?php echo $value->referring_site; ?></th>
					<th><?php echo $value->visits; ?></th>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr valign="top">
				<th colspan="2"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
			</tr>
		<?php endif; ?>
	</table>
	<h3><?php _e( 'Destinations', PLUGIN_SLUG ); ?></h3>
	<table class="widefat">
		<thead>
			<tr valign="top">
				<th class="row-title"><?php _e( 'Path', PLUGIN_SLUG ); ?></th>
				<th class="row-title"><?php _e( 'Visits', PLUGIN_SLUG ); ?></th>
			</tr>
		</thead>
		<?php if ( count( json_decode( $data['destinations'] ) ) > 0 ) : ?>
			<?php foreach (array_slice(json_decode($data['destinations']), 0, 10) as $key => $value) : ?>
				<tr valign="top">
					<th><?php echo $value->path; ?></th>
					<th><?php echo $value->visits; ?></th>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr valign="top">
				<th colspan="2"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
			</tr>
		<?php endif; ?>
	</table>
	<h3><?php _e( 'Evolution', PLUGIN_SLUG ); ?></h3>
	<div id="chart"></div>
	<div class="bottom_back">
		<a class="button-secondary" href="<?php echo $data['my_dashboard_link']; ?>">
			<?php _e( 'Back', PLUGIN_SLUG ); ?>
		</a>
	</div>
</div>