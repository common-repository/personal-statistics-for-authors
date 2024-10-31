<?php if ( $data ) : ?>
	<div class="wrap">
		<div id="icon-32" class="icon32">
			<img src="<?php echo DIR_IMG; ?>/icon32.png" width="32" height="32" />
		</div>
		<h2><?php _e( 'My Dashboard', PLUGIN_SLUG ); ?></h2>
		<h3><?php _e( 'Authors', PLUGIN_SLUG ); ?></h3>
		<?php if ( $data['count_posts_db'] < $data['published_posts'] ) : ?>
			<div class="message">
				<p><strong>Information</strong> : <?php echo $data['count_posts_db']; ?> articles récupérés sur <?php echo $data['published_posts']; ?></p>
			</div>
		<?php endif; ?>
		<?php include 'promo.php'; ?>
		<table class="widefat" id="table2">
			<thead>
				<tr>
					<th><span><?php _e( 'Authors', PLUGIN_SLUG ); ?></span></th>
					<th><span><?php _e( 'Posts', PLUGIN_SLUG ); ?></span></th>
					<th><span><?php _e( 'Comments', PLUGIN_SLUG ); ?></span></th>
					<th><span><?php _e( 'Visits', PLUGIN_SLUG ); ?></span></th>
					<th><span><?php _e( 'Facebook', PLUGIN_SLUG ); ?></span></th>
					<th><span><?php _e( 'Twitter', PLUGIN_SLUG ); ?></span></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( count( $data['authors'] ) > 0 ) : ?>
				<?php foreach ($data['authors'] as $d) : ?>
					<tr valign="top">
						<th><?php echo $d['name']; ?></th>
						<th><?php echo $d['count_posts']; ?></th>
						<td><?php echo ($d['comments'] > 0) ? $d['comments'] : '0'; ?></td>
						<td><?php echo ($d['visits'] > 0) ? $d['visits'] : '0'; ?></td>
						<td><?php echo ($d['facebook'] > 0) ? $d['facebook'] : '0'; ?></td>
						<td><?php echo ($d['twitter'] > 0) ? $d['twitter'] : '0'; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr valign="top">
					<th colspan="6"><?php _e( 'No datas', PLUGIN_SLUG ); ?></th>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<p><?php _e( 'No datas', PLUGIN_SLUG ); ?></p>
<?php endif; ?>