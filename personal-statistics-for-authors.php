<?php
/*
Plugin Name: Personal Statistics for Authors
Plugin URI: http://peexeo.com
Description: Personnal Statistics for Authors is the plugin required for every blog. It uses the Facebook, Twitter and Google Analytics API to get the data of your posts (shares, tweets, keywords used to find it, visits from smartphones, rebound...). You don't need to switch between your Analytics dashboard and everything else : PSfA is here to do that job for you.
Version: 1.0.1
Author: Agence Peexeo
Author URI: http://peexeo.com
License:
Copyright (C) 2013  Peexeo & Florent Desjardins

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! class_exists( 'PSFA' ) ) {
	
	global $wpdb;

	// The plugin name
	define( 'PLUGIN_NAME', 'Personal Statistics for Authors' );
	
	// The plugin slug
	define( 'PLUGIN_SLUG', 'PSFA' );
	
	// Plugin version
	define( 'PSFA_VERSION', '1.0.1' );

	// Directory separator
	define( 'DS', '/' );
	
	// Templates directory name
	define( 'DIR_TPL', 'templates' );
	
	// Languages directory name
	define( 'DIR_LANG', 'languages' );

	// Images directory
	define( 'DIR_IMG', plugins_url( '/images/', __FILE__) );

	// Javascript directory path
	define( 'JS', plugins_url('/js/', __FILE__) );
	
	// Table name
	define( 'TBL_NAME', $wpdb->base_prefix . 'psfa' );
	
	// Google SDK
	require_once plugin_dir_path( __FILE__ ) . 'libraries/src/Google_Client.php';
	require_once plugin_dir_path( __FILE__ ) . 'libraries/src/contrib/Google_AnalyticsService.php';
	
	// Start Session
	if( ! session_id() ) {
		session_start();
	}

	/**
	 * PSFA class
	 *
	 * @version 1.0.0
	 */
	class PSFA {
		
		/**
		 * Template variable
		 *
		 * @var vars
		 */
		var $vars = array();

		/**
		 * Constructor
		 *
		 * @param null
		 */
		public function __construct() {

			//add_option( 'psfa_version', PSFA_VERSION );
			
			// Plugin translation
			add_action( 'plugins_loaded', array( $this, 'translation' ) );
			
			// Plugin activation
			register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
			
			// Plugin desactivation
			register_deactivation_hook( __FILE__, array( $this, 'plugin_desactivation' ) );
			
			// Creates a new item in the WordPress admin settings menu 
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			
			// Permits to use the Settings API
			add_action( 'admin_init', array( $this, 'settings_registration' ) );
			
			// Styles and Javascripts
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			
			// Add settings link to plugin page
			add_filter( 'plugin_action_links', array( $this, 'settings_plugin_link' ), 10, 2 );
			
			// Display notice error if settings are empty
			if ( ! get_option( 'client_id' ) ||
				 ! get_option( 'client_secret' ) ||
				 ! get_option( 'api_key' ) ||
				 ! get_option( 'profile_id' ) ) {
				add_action( 'admin_notices', array( $this, 'display_notice' ) );
			}
			
			// Add Dashboard Widgets
			add_action( 'wp_dashboard_setup' , array( $this, 'add_dashboard_widgets' ) );
			
			// Cron exec
			add_action( 'psfa_cron', array( $this, 'cron_exec' ) );

			// Add cron intervals
			add_filter( 'cron_schedules', array( $this, 'add_cron_intervals' ) );

			// Add Admin Pages
			add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );

			// Plugin upgrade
			add_action( 'plugins_loaded', array( $this, 'plugin_db_upgrade' ) );
			
		}

		/**
		 * Plugin activation
		 *
		 * @param null
		 */
		public function plugin_db_upgrade() {

			if ( get_option( 'psfa_version' ) != PSFA_VERSION ) {
				global $wpdb;

				$sql = "
					CREATE TABLE " . TBL_NAME . " (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`post_id` int(11) DEFAULT NULL,
						`ga_url` varchar(255) DEFAULT NULL,
						`avg_time_post` int(11) DEFAULT NULL,
						`bounce_rate` int(11) DEFAULT NULL,
						`mobile_visits` int(11) DEFAULT NULL,
						`visits` int(11) DEFAULT NULL,
						`comments` int(11) DEFAULT NULL,
						`facebook` int(11) DEFAULT NULL,
						`twitter` int(11) DEFAULT NULL,
						`keywords` longtext,
						`traffic_sources` longtext,
						`destinations` longtext,
						`evolution` longtext,
						`user_id` int(11) DEFAULT NULL,
						`date_update` bigint(11) unsigned DEFAULT 0,
						PRIMARY KEY id (id)
					);
				";
				
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				dbDelta( $sql );

				if ( ! get_option( 'psfa_version' ) ) {
					add_option( 'psfa_version', PSFA_VERSION );
				} else {
					update_option( 'psfa_version', PSFA_VERSION );
				}
			}
		}
		
		/**
		 * Plugin activation
		 *
		 * @param null
		 */
		public function plugin_activation() {
			// Create tables in database
			global $wpdb;

			$sql = "
				CREATE TABLE " . TBL_NAME . " (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`post_id` int(11) DEFAULT NULL,
					`ga_url` varchar(255) DEFAULT NULL,
					`avg_time_post` int(11) DEFAULT NULL,
					`bounce_rate` int(11) DEFAULT NULL,
					`mobile_visits` int(11) DEFAULT NULL,
					`visits` int(11) DEFAULT NULL,
					`comments` int(11) DEFAULT NULL,
					`facebook` int(11) DEFAULT NULL,
					`twitter` int(11) DEFAULT NULL,
					`keywords` longtext,
					`traffic_sources` longtext,
					`destinations` longtext,
					`evolution` longtext,
					`user_id` int(11) DEFAULT NULL,
					`num_package` int(11) DEFAULT NULL,
					PRIMARY KEY id (id)
				);
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			// Create Cron
			if ( ! wp_next_scheduled( 'psfa_cron' ) ) {
				wp_schedule_event( time(), 'psfa_5minutes', 'psfa_cron' );
			}
		}
		
		/**
		 * Display notice error if settings are empty 
		 *
		 * @param null
		 */
		public function display_notice() {
			$this->render('notice.php');
		}
		
		/**
		 * Plugin desactivation
		 *
		 * @param null
		 */
		public function plugin_desactivation() {
			// Delete option from option table
			delete_option( 'client_id' );
			delete_option( 'client_secret' );
			delete_option( 'api_key' );
			delete_option( 'profile_id' );
			delete_option( 'psfa_token' );

			if ( ! empty( $_SESSION['token'] ) )
				unset( $_SESSION['token'] );
			
			// Drop the table
			global $wpdb;
			$wpdb->query( "DROP TABLE IF EXISTS " . TBL_NAME );

			// Delete cron
			if ( wp_next_scheduled( 'psfa_cron' ) ) {
				wp_clear_scheduled_hook( 'psfa_cron' );
			}
		}
		
		/**
		* Add cron intervals
		*
		* @param null
		*/
		public function add_cron_intervals( $schedules ) {
			$schedules['psfa_5minutes'] = array(
				'interval' => 60 * 5,
				'display' => '[PSFA] Get datas (every 5 Minutes)'
			);
			return $schedules;
		}

		/**
		 * Cron execution
		 *
		 * @param null
		 */
		public function cron_exec() {
			
			// Check if settings are not empty
			if ( get_option( 'client_id' ) &&
				 get_option( 'client_secret' ) &&
				 get_option( 'api_key' ) &&
				 get_option( 'profile_id' ) ) {
				
				global $wpdb;
				
				// Instantiate Google Client
				$Google_Client = new Google_Client();
				$Google_Client->setClientId( get_option( 'client_id' ) );
				$Google_Client->setClientSecret( get_option( 'client_secret' ) );
				$Google_Client->setRedirectUri( get_bloginfo( 'url' ) . '/wp-admin/options-general.php?page=PSFA' );
				$Google_Client->setDeveloperKey( get_option( 'api_key' ) );
				$Google_Client->setAccessType('offline');
				$Google_Service = new Google_AnalyticsService($Google_Client);
				
				// Get the storage Google token
				$storage = get_option( 'psfa_token' );
				
				// Refresh the Google token
				if ( ! empty( $storage ) ) {
					$Google_Client->refreshToken( get_option( 'psfa_token' ) );
				}
				
				// If Google token is ok
				if ( $Google_Client->getAccessToken() ) {
					
					// Get 5 oldest updated posts
					$oldest_updated = $wpdb->get_results( "SELECT * FROM " . TBL_NAME . " ORDER BY date_update ASC LIMIT 5" );
					
					// Today (end_date)
					$today = mysql2date( 'Y-m-d', current_time( 'timestamp' ) );
					
					// Oldest updated loop
					if (count($oldest_updated) > 0) {
						foreach ($oldest_updated as $key => $post) {
						
							// Post date creation (start_date)
							$created = get_the_time('Y-m-d', $post->post_id);
							
							// URL for GA and permalink for Facebook and Twitter requests
							$site_url = get_bloginfo( 'siteurl' );
							$permalink = get_permalink( $post->post_id );
							$url = str_replace( $site_url, '', $permalink );
							
							// Comments
							$wp_count_comments = wp_count_comments( $post->post_id );

							// Average time on post,
							// Bounce rate and
							// Visits
							$metrics = 'ga:avgTimeOnSite,ga:visitBounceRate,ga:visitors';
							$options = array( 'filters' => 'ga:pagePath==' . $url );
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options );
							
							$avg_time_post = floor( $data_ga['rows'][0][0] );
							$bounce_rate = floor( $data_ga['rows'][0][1] );
							$visits = $data_ga['rows'][0][2];
							
							// Mobile percentage
							$metrics = 'ga:visitors';
							$options = array('dimensions' => 'ga:isMobile', 'filters' => 'ga:pagePath==' . $url);
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options);
							$mobile_visits = $data_ga['rows'][1][1];
							$mobile_visits_percentage = ( $visits > 0 ) ? floor ( ( $mobile_visits / $visits ) * 100 ) : 0;
							
							// Facebook count
							$ch = curl_init();
							curl_setopt ($ch, CURLOPT_URL, "http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=$permalink");
							curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
							$contents = curl_exec($ch);
							curl_close($ch);
							$json = json_decode($contents);
							$facebook_count = $json[0]->share_count;

							// Twitter count
							$ch = curl_init();
							curl_setopt ($ch, CURLOPT_URL, "http://urls.api.twitter.com/1/urls/count.json?url=$permalink");
							curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
							$contents = curl_exec($ch);
							curl_close($ch);
							$json = json_decode($contents);
							$twitter_count = $json->count;
							
							// Keywords
							$metrics = 'ga:visitors';
							$options = array( 'sort' => '-ga:visitors', 'dimensions' => 'ga:keyword', 'filters' => 'ga:pagePath==' . $url );
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options );
							$keywords = array();
							if ( ! empty( $data_ga['rows'] ) ) {
								foreach ( $data_ga['rows'] as $key => $row ) {
									$a = array(
										'keyword' => $row[0],
										'visits' => $row[1]
									);
									array_push( $keywords, $a );
								}
							}
							$keywords = json_encode( $keywords );
							
							// Traffic sources
							$metrics = 'ga:visitors';
							$options = array(
								'sort' => '-ga:visitors',
								'dimensions' => 'ga:referralPath,ga:source',
								'filters' => 'ga:pagePath==' . $url
							);
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options);
							$traffic_sources = array();
							if ( ! empty( $data_ga['rows'] ) ) {
								foreach ( $data_ga['rows'] as $key => $row ) {
									$a = array(
										//'referring_site' => $row[1] . $row[0],
										'referring_site' => $row[1],
										'visits' => $row[2]
									);
									array_push($traffic_sources, $a);
								}
							}
							$traffic_sources = json_encode( $traffic_sources );
							
							// Destinations
							$metrics = 'ga:visitors';
							$options = array(
								'sort' => '-ga:visitors',
								'dimensions' => 'ga:previousPagePath,ga:nextPagePath',
								'filters' => 'ga:previousPagePath==' . $url
							);
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options);
							$destinations = array();
							if ( ! empty( $data_ga['rows'] ) ) {
								foreach ( $data_ga['rows'] as $key => $row ) {
									$a = array(
										'path' => $row[1],
										'visits' => $row[2]
									);
									array_push( $destinations, $a );
								}
							}
							$destinations = json_encode( $destinations );
							
							// Evolution
							$metrics = 'ga:visitors,ga:visitBounceRate';
							$options = array(
								'dimensions' => 'ga:date',
								'filters' => 'ga:pagePath==' . $url
							);
							$data_ga = $Google_Service->data_ga->get( get_option( 'profile_id' ), $created, $today, $metrics, $options);
							$evolution = array();
							foreach ($data_ga['rows'] as $key => $row) {
								$a = array(
									'date' => date('d/m/y', strtotime($row[0])),
									'visits' => (string)$row[1],
									'bounce_rate' => floor($row[2])
								);
								array_push($evolution, $a);
							}
							$evolution = json_encode($evolution);
							
							// User id
							$post_tmp = get_post( $post->post_id );
							$user_id = $post_tmp->post_author;

							$wpdb->update(
								TBL_NAME,
								array(
									'ga_url' => $url,
									'avg_time_post' => $avg_time_post,
									'bounce_rate' => $bounce_rate,
									'mobile_visits' => $mobile_visits_percentage,
									'visits' => $visits,
									'comments' => $wp_count_comments->approved,
									'facebook' => $facebook_count,
									'twitter' => $twitter_count,
									'keywords' => $keywords,
									'traffic_sources' => $traffic_sources,
									'destinations' => $destinations,
									'evolution' => $evolution,
									'user_id' => $user_id,
									'num_package' => 0,
									'date_update' => current_time( 'timestamp' )
								),
								array( 'post_id' => $post->post_id )
							);
						} // end foreach
					} // end if

					// Published posts
					$posts_array = get_posts( array(
						'showposts' => -1,
						'post_status' => 'publish',
						'orderby' => 'post_date',
						'order' => 'ASC',
					) );
					$wp_posts = array();
					foreach ( $posts_array as $obj ) {
						array_push( $wp_posts, $obj->ID );
					}

					// Posts in database
					$db_posts_object = $wpdb->get_results( "SELECT * FROM " . TBL_NAME );
					$db_posts = array();
					foreach ( $db_posts_object as $obj ) {
						array_push( $db_posts, $obj->post_id );
					}
					
					// New posts to insert into database
					$new = array_diff( $wp_posts, $db_posts );

					if ( count( $new ) > 0 ) {
						foreach ( $new as $key => $id ) {
							$data = array(
								'post_id' => $id,
								'ga_url' => "",
								'avg_time_post' => 0,
								'bounce_rate' => 0,
								'mobile_visits' => 0,
								'visits' => 0,
								'comments' => 0,
								'facebook' => 0,
								'twitter' => 0,
								'keywords' => "[]",
								'traffic_sources' => "[]",
								'destinations' => "[]",
								'evolution' => "[]",
								'user_id' => 0,
								'num_package' => 0,
								'date_update' => 0
							);
							$wpdb->insert( TBL_NAME, $data );
						}
					}

					// Posts to delete from database
					$to_delete = array_diff( $db_posts, $wp_posts );

					if ( count( $to_delete ) > 0 ) {
						foreach ( $to_delete as $key => $id ) {
							
							$wpdb->query(
								"DELETE FROM " . TBL_NAME . " WHERE post_id = " . $id
							);
						}
					}

				} // end if
			} // end if
			
		}
		
		/**
		 * Plugin translation
		 *
		 * @param null
		 */
		public function translation() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( PLUGIN_SLUG, false, dirname( plugin_basename( __FILE__ ) ) . DS . DIR_LANG . DS );
			}
		}
		
		/**
		 * Add settings link to plugin page
		 *
		 * @param $links
		 * @param $file
		 */
		public function settings_plugin_link( $links, $file ) {
			if ( $file == plugin_basename( dirname( __FILE__ ) . '/personal-statistics-for-authors.php' ) ) {
				$links[] = '<a href="options-general.php?page=PSFA">' . __( 'Settings' , PLUGIN_SLUG) . '</a>';
			}
			return $links;
		}
		
		/**
		 * Creates a new item in the WordPress admin settings menu 
		 *
		 * @param null
		 */
		public function add_settings_page() {
			add_options_page( PLUGIN_NAME, PLUGIN_NAME, 'manage_options', PLUGIN_SLUG, array( $this, 'settings_action' ) );
		}
		
		/**
		 * Permits to use the Settings API
		 *
		 * @param null
		 */
		public function settings_registration() {
			// Google Analytics Section
			add_settings_section(
				'google_console_section',
				__( 'Google Console API', PLUGIN_SLUG ),
				array( $this, 'google_console_section_callback' ),
				PLUGIN_SLUG
			);
			add_settings_field(
				'client_id',
				'<label for="client_id">Client ID</label>',
				array( $this, 'client_id_callback' ),
				PLUGIN_SLUG,
				'google_console_section'
			);
			add_settings_field(
				'client_secret',
				'<label for="client_secret">Client secret</label>',
				array( $this, 'client_secret_callback' ),
				PLUGIN_SLUG,
				'google_console_section'
			);
			add_settings_field(
				'api_key',
				'<label for="api_key">API key</label>',
				array( $this, 'api_key_callback' ),
				PLUGIN_SLUG,
				'google_console_section'
			);
			add_settings_section(
				'google_analytics_section',
				__( 'Google Analytics', PLUGIN_SLUG ),
				array( $this, 'google_analytics_section_callback' ),
				PLUGIN_SLUG
			);
			add_settings_field(
				'profile_id',
				'<label for="profile_id">Profile ID</label><br /><code>ex: ga:12345678</code>',
				array( $this, 'profile_id_callback' ),
				PLUGIN_SLUG,
				'google_analytics_section'
			);
			register_setting( PLUGIN_SLUG, 'client_id' );
			register_setting( PLUGIN_SLUG, 'client_secret' );
			register_setting( PLUGIN_SLUG, 'api_key' );
			register_setting( PLUGIN_SLUG, 'profile_id' );
		}
		
		/**
		 * Render settings page template
		 *
		 * @param null
		 */
		public function settings_action() {
			
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', PLUGIN_SLUG ) );
			}
			
			// Instantiate the Google Client
			$Google_Client = new Google_Client();
			$Google_Client->setClientId( get_option( 'client_id' ) );
			$Google_Client->setClientSecret( get_option( 'client_secret' ) );
			$Google_Client->setRedirectUri( get_bloginfo( 'url' ) . '/wp-admin/options-general.php?page=PSFA' );
			$Google_Client->setDeveloperKey( get_option( 'api_key' ) );
			$Google_Client->setAccessType('offline');
			$Google_Service = new Google_AnalyticsService($Google_Client);
			
			// Google access logout
			if ( isset ($_GET['logout'] ) ) {
				unset( $_SESSION['token'] );
				delete_option( 'psfa_token' );
			}
			
			// Validate Google access callback
			if ( isset( $_GET['code'] ) ) {
				$Google_Client->authenticate();
				$_SESSION['token'] = $Google_Client->getAccessToken();
				$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				//header( 'Location: ' . filter_var( $redirect, FILTER_SANITIZE_URL ) );
				echo "<script>location.href='" . filter_var( $redirect, FILTER_SANITIZE_URL ) . "?page=" . PLUGIN_SLUG . "'</script>";
			}
			
			// Set the Google token and store it into database
			if ( isset( $_SESSION['token'] ) ) {
				$Google_Client->setAccessToken( $_SESSION['token'] );
				$sessionToken = json_decode( $_SESSION['token'] );
				add_option( 'psfa_token', $sessionToken->refresh_token );
			}
			
			// Get the Google token store in database
			$storage = get_option( 'psfa_token' );
			
			// Resfresh the Google token if is stored it
			if ( ! empty( $storage ) ) {
				$Google_Client->refreshToken( get_option( 'psfa_token' ) );
			}
			
			// Generate link to connect google authorised access
			if ( ! $Google_Client->getAccessToken() ) {
				$authUrl = $Google_Client->createAuthUrl();
			}
			
			// Set authUrl and render the settings view
			if ( ! empty( $authUrl ) ) {
				$this->set( 'authUrl', $authUrl );
			}
			$this->render( 'settings_template.php' );
		}
		/**
		 * Google Console API Section Callback
		 *
		 * @param null
		 */
		public function google_console_section_callback() {
			echo '<p>' . __( 'Tape your google console api datas', PLUGIN_SLUG ) . '</p>';
		}
		
		/**
		 * Google Analytics Section Callback
		 *
		 * @param null
		 */
		public function google_analytics_section_callback() {
			echo '<p>' . __( 'Tape your google api access datas', PLUGIN_SLUG ) . '</p>';
		}
		
		/**
		 * Client ID Callback
		 *
		 * @param null
		 */
		public function client_id_callback( $args ) {
			$this->render('inputs/client_id.php');
		}
		
		/**
		 * Client secret Callback
		 *
		 * @param null
		 */
		public function client_secret_callback() {
			$this->render('inputs/client_secret.php');
		}
		
		/**
		 * API key Callback
		 *
		 * @param null
		 */
		public function api_key_callback() {
			$this->render('inputs/api_key.php');
		}
		
		/**
		 * Profile ID Callback
		 *
		 * @param null
		 */
		public function profile_id_callback( $args ) {
			$this->render('inputs/profile_id.php');
		}
		
		/**
		 * Load Styles and Javascripts
		 *
		 * @param null
		 */
		public function load_admin_scripts() {
			wp_register_style( 'style', plugins_url( '/css/style.css' , __FILE__ ) );
			wp_enqueue_style( 'style' );

			wp_register_script( 'googlechart', 'https://www.google.com/jsapi' );
			wp_enqueue_script( 'googlechart' );

			wp_register_script( 'datatable', plugins_url( '/js/jquery.dataTables.min.js' , __FILE__ ) );
			wp_enqueue_script( 'datatable' );

			wp_register_script( 'app', plugins_url( '/js/app.js' , __FILE__ ) );
			wp_enqueue_script( 'app' );
		}
		
		/**
		 * Add dashboard widget
		 *
		 * @param null
		 */
		public function add_dashboard_widgets() {
			global $wp_meta_boxes;
			global $wpdb;
			
			$posts = get_posts( array(
				'showposts' => -1,
				'post_status' => 'publish',
				'orderby' => 'post_date',
				'order' => 'ASC'
			) );

			$count_posts = $wpdb->get_var( "SELECT COUNT(*) FROM " . TBL_NAME );
			
			if ( $count_posts > 0 ) {
				$title = __( 'Your activity since', PLUGIN_SLUG ) . ' ' . mysql2date( 'd M Y', $posts[0]->post_date, true );
			} else {
				$title = __( 'No activity', PLUGIN_SLUG );
			}

			wp_add_dashboard_widget(
				'widget-dashboard',
				$title,
				array( $this, 'display_content_dashboard_widget' )
			);
		}
		
		/**
		 * Get generals statistiques for the dashboard and dashboard widget
		 *
		 * @param null
		 */
		public function get_general_statistics() {

			global $wpdb;
			global $current_user;

			get_currentuserinfo();

			// All author's posts
			$posts_array = get_posts(array(
				'showposts' => -1,
				'author' => $current_user->ID,
				'post_status' => 'publish'
			));

			if ( empty( $posts_array ) ) {
				return false;
			}

			// Get posts on database
			$posts = $wpdb->get_results( "
				SELECT *
				FROM " . TBL_NAME . "
				WHERE user_id = $current_user->ID
			" );

			if ( empty( $posts ) ) {
				return false;
			}

			// Counters init
			$count_studies = 0;
			$count_views = 0;
			$count_viewpoints = 0;
			$count_webreviews = 0;
			$count_time = 0;
			$count_bounce_rate = 0;
			$count_mobile_visits = 0;
			$count_visits = 0;
			$count_comments = 0;
			$count_facebook = 0;
			$count_twitter = 0;

			// Posts loop
			foreach ($posts as $key => $post) {
				// Total time
				$count_time += $post->avg_time_post;

				// Total bounce rate
				$count_bounce_rate += $post->bounce_rate;

				// Total mobile visits
				$count_mobile_visits += $post->mobile_visits;

				// Total visits
				$count_visits += $post->visits;

				// Total comments
				$count_comments += $post->comments;

				// Total Facebook
				$count_facebook += $post->facebook;

				// Total Twitter
				$count_twitter += $post->twitter;
			}

			// Average time
			$total = floor($count_time / count($posts));
			$avg_time_post = $this->convert_time($total);

			// Average bounce rate
			$bounce_rate = floor($count_bounce_rate / count($posts));

			// Average mobile visits
			$mobile_visits = floor($count_mobile_visits / count($posts));

			return array(
				'count_posts' => count($posts),
				'count_studies' => $count_studies,
				'count_views' => $count_views,
				'count_viewpoints' => $count_viewpoints,
				'count_webreviews' => $count_webreviews,
				'avg_time_post' => $avg_time_post,
				'bounce_rate' => $bounce_rate,
				'mobile_visits' => $mobile_visits,
				'visits' => $count_visits,
				'comments' => $count_comments,
				'facebook' => $count_facebook,
				'twitter' => $count_twitter,
				'my_dashboard_link' => 'admin.php?page=' . PLUGIN_SLUG . '/my-dashboard.php',
				'averages_link' => 'admin.php?page=' . PLUGIN_SLUG . '/averages.php',
				'posts' => $posts
			);

		}

		/**
		 * Display the content dashboard widget
		 *
		 * @param null
		 */
		public function display_content_dashboard_widget() {
			
			global $current_user;
			global $wpdb;

			$data = $this->get_general_statistics();

			// Get posts on database
			$evo_jsons = $wpdb->get_results( "
				SELECT evolution as json
				FROM " . TBL_NAME . "
				WHERE user_id = $current_user->ID
			" );

			// Datas for chart
			$count_visits = 0;
			$count_bounce_rate = 0;
			$array_counts = array();
			$counter = array();

			foreach ($evo_jsons as $key => $evo_json) {
				$evo_array = json_decode($evo_json->json);

				foreach ($evo_array as $key => $value) {

					list($d, $m, $y) = explode("/", $value->date);

					$index = mktime(0, 0, 0, $m, $d, $y);

					if ( ! isset($array_counts[$index]) ) {
						// add
						$counter[$index] = 1;
						$array_counts[$index] = array(
							'date' => $value->date,
							'visits' => intval($value->visits),
							'bounce_rate' => intval($value->bounce_rate)
						);
					} else {
						// update counter
						$counter[$index]++;
						$array_counts[$index]['visits'] += intval($value->visits);
						$array_counts[$index]['bounce_rate'] += intval($value->bounce_rate);
						
					}
				}
			}

			// sort array by asc date
			ksort($array_counts);

			// bounce rate average calculation
			$chart_datas = array();
			foreach ($array_counts as $key => $d) {
				$chart_datas[$key] = array(
					'date' => $d['date'],
					'visits' => $d['visits'],
					'bounce_rate' => round($d['bounce_rate'] / $counter[$key])
				);
			}

			// Load the javascript google chart
			wp_register_script( 'widget_dashboard_chart', JS . 'widget_dashboard_chart.js' );
			wp_enqueue_script( 'widget_dashboard_chart' );
			wp_localize_script( 'widget_dashboard_chart', 'data', $chart_datas );

			if ( $data ) {
				// For notice message if all posts are not recovered
				$count_posts = wp_count_posts();
				$data['published_posts'] =  $count_posts->publish;
				$data['count_posts_db'] = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );
			}

			$this->set( 'data', $data );
			$this->render( 'dashboard_widget_template.php', PLUGIN_SLUG );
		}

		/**
		 * Add menu pages
		 *
		 * @param null
		 */
		public function add_menu_pages() {

			// Add My Dashboard page
			add_menu_page(
				__( 'My Dashboard', PLUGIN_SLUG ),
				__( 'My Dashboard', PLUGIN_SLUG ),
				'publish_posts',
				PLUGIN_SLUG . DS . 'my-dashboard.php',
				array($this, 'my_dashboard_action'),
				plugins_url('/images/icon16.png', __FILE__),
				100
			);

			// Dashboard Ristretto
			add_submenu_page(
				PLUGIN_SLUG . DS . 'my-dashboard.php',
				__( 'Averages', PLUGIN_SLUG ),
				__( 'Averages', PLUGIN_SLUG ),
				'publish_posts',
				PLUGIN_SLUG . DS . 'averages.php',
				array($this, 'averages_action')
			);

			// Authors
			add_submenu_page(
				PLUGIN_SLUG . DS . 'my-dashboard.php',
				__( 'Authors', PLUGIN_SLUG ),
				__( 'Authors', PLUGIN_SLUG ),
				'publish_posts',
				PLUGIN_SLUG . DS . 'authors.php',
				array($this, 'authors_action')
			);
			
			// Help
			add_submenu_page(
				PLUGIN_SLUG . DS . 'my-dashboard.php',
				__( 'Help', PLUGIN_SLUG ),
				__( 'Help', PLUGIN_SLUG ),
				'publish_posts',
				PLUGIN_SLUG . DS . 'help.php',
				array($this, 'help_action')
			);
			
		}

		/**
		 * My Dashboard action
		 *
		 * @param null
		 */
		public function my_dashboard_action() {

			global $wpdb;

			if ( empty ( $_GET['post_id'] ) ) {
				$data = $this->get_general_statistics();
				$this->set( 'data', $data );
				$this->render( 'my_dashboard_template.php', PLUGIN_SLUG );
			} else {

				$data = $wpdb->get_row("
					SELECT *
					FROM " . TBL_NAME . "
					WHERE post_id = " . $_GET['post_id'] . "
				");

				$data = get_object_vars($data);

				wp_register_script( 'single_chart', JS . 'single_chart.js' );
				wp_enqueue_script( 'single_chart' );
				wp_localize_script( 'single_chart', 'data', $data );

				$data['my_dashboard_link'] = 'admin.php?page=' . PLUGIN_SLUG . '/my-dashboard.php';

				// For notice message if all posts are not recovered
				$count_posts = wp_count_posts();
				$data['published_posts'] =  $count_posts->publish;
				$data['count_posts_db'] = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );

				$this->set( 'data', $data );
				$this->render( 'single_template.php' );
			}
		}

		/**
		 * Averages action
		 *
		 * @param null
		 */
		public function averages_action() {
			
			global $wpdb;

			$count_posts_db = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );

			if ( intval( $count_posts_db ) < 1 ) {

				// For notice message if all posts are not recovered
				$count_posts = wp_count_posts();
				$data['published_posts'] =  $count_posts->publish;
				$data['count_posts_db'] = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );

				$this->set( 'data', false );
				$this->render( 'averages_template.php' );
				return false;
			}
			
			/*
			$posts_per_author = $wpdb->get_var(
			$wpdb->prepare("
			SELECT AVG(user_id) as avg_user_id
			FROM " . TBL_NAME . " GROUP BY user_id
			",
			NULL
			) );
			*/
			$count_posts_obj = wp_count_posts();
			$posts_per_author = $count_posts_obj->publish / count( get_users() );

			$results = $wpdb->get_row(
				$wpdb->prepare("
					SELECT AVG(avg_time_post) as avg_time_post,
						   AVG(visits) as avg_visits,
						   AVG(mobile_visits) as avg_mobile_visits,
						   AVG(bounce_rate) as avg_bounce_rate,
						   AVG(comments) as avg_comments,
						   AVG(facebook) as avg_facebook,
						   AVG(twitter) as avg_twitter
					FROM ".TBL_NAME."
				",
				NULL
			) );

			// More read
			$more_read = $wpdb->get_results(
				$wpdb->prepare("
					SELECT *
					FROM ".TBL_NAME."
					ORDER BY visits DESC
					LIMIT 0, 5
				", NULL)
			);

			// More commented
			$more_commented = $wpdb->get_results(
				$wpdb->prepare("
					SELECT *
					FROM ".TBL_NAME."
					ORDER BY comments DESC
					LIMIT 0, 5
				", NULL)
			);

			$data = array(
				'posts_per_author' => round($posts_per_author),
				'avg_time_post' => $this->convert_time(ceil($results->avg_time_post)),
				'visits' => ceil($results->avg_visits),
				'mobile_visits' => ceil($results->avg_mobile_visits),
				'bounce_rate' => ceil($results->avg_bounce_rate),
				'comments' => ceil($results->avg_comments),
				'facebook' => ceil($results->avg_facebook),
				'twitter' => ceil($results->avg_twitter),
				'more_read' => $more_read,
				'more_commented' => $more_commented,
				'my_dashboard_link' => 'admin.php?page=' . PLUGIN_SLUG . '/my-dashboard.php'
			);

			// For notice message if all posts are not recovered
			$count_posts = wp_count_posts();
			$data['published_posts'] =  $count_posts->publish;
			$data['count_posts_db'] = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );

			$this->set( 'data', $data );
			$this->render( 'averages_template.php' );
		}

		/**
		 * Authors action
		 *
		 * @param null
		 */
		public function authors_action() {
			
			global $wpdb;

			$count_posts_db = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );
			
			if ( intval( $count_posts_db ) < 1 ) {
				$this->set( 'data', false );
				$this->render('authors_template.php');
				return false;
			}

			$authors = get_users();

			$data_authors = array();

			foreach ($authors as $author) {
				
				$count_posts = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . TBL_NAME . ' WHERE user_id = %d', $author->ID));
				$comments = $wpdb->get_var($wpdb->prepare('SELECT SUM(comments) FROM ' . TBL_NAME . ' WHERE user_id = %d', $author->ID));
				$visits = $wpdb->get_var($wpdb->prepare('SELECT SUM(visits) FROM ' . TBL_NAME . ' WHERE user_id = %d', $author->ID));
				$facebook = $wpdb->get_var($wpdb->prepare('SELECT SUM(facebook) FROM ' . TBL_NAME . ' WHERE user_id = %d', $author->ID));
				$twitter = $wpdb->get_var($wpdb->prepare('SELECT SUM(twitter) FROM ' . TBL_NAME . ' WHERE user_id = %d', $author->ID));

				array_push($data_authors, array(
					'name' => $author->display_name,
					'count_posts' => $count_posts,
					'comments' => $comments,
					'visits' => $visits,
					'facebook' => $facebook,
					'twitter' => $twitter
				));
			}

			$data['authors'] = $data_authors;

			// For notice message if all posts are not recovered
			$count_posts = wp_count_posts();
			$data['published_posts'] =  $count_posts->publish;
			$data['count_posts_db'] = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . TBL_NAME, NULL ) );


			$this->set('data', $data);
			$this->render('authors_template.php');
		}
		
		/**
		 * Help action
		 *
		 * @param null
		 */
		public function help_action() {
			$this->render( 'help_template.php' );
		}
		
		/**
		 * Render a template
		 *
		 * @param string $tpl
		 */
		public function render( $tpl ) {
			ob_start();
			if ( isset ( $this->vars ) ) {
				extract( $this->vars );
			}
			ob_get_clean();
			include DIR_TPL . DS . $tpl;
		}

		/**
		 * Set a template variable
		 *
		 * @param string $key
		 * @param mixed $val
		 */
		public function set( $key, $val ) {
			$this->vars[$key] = $val;
		}

		/**
		 * Convert time in seconds to min and sec
		 *
		 * @param null
		 */
		public function convert_time($sec_time)
		{
			$total = $sec_time;
			$hours = intval(abs($total / 3600));
			$total = $total - ($hours * 3600);
			$minute = intval(abs($total / 60));
			$total = $total - ($minute * 60);
			$seconde = $total;
			return $minute.'m '.$seconde.'s';
		}
		
	}

	// Run
	new PSFA();
}