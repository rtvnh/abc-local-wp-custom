<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/rtvnh/abc-local-wp-custom
 * @since             0.1.0
 * @package           Plugin_ABC_Manager_Local_Partner
 *
 * @wordpress-plugin
 * Plugin Name:         ABC Manager - Custom Local Partner
 * Plugin URI:          https://github.com/rtvnh/abc-local-wp-custom
 * Description:         WordPress Plugin to post new updates to the ABC Manager of NH/AT5
 * Version:             0.1.5
 * Author:              AngryBytes B.V.
 * Author URI:          https://angrybytes.com
 * License:             GPL-2.0+
 * Text Domain:         abclocalpartner
 * Requires at least:   5.7
 * Requires PHP:        7.3
 */

// Configure our plugin updater.
require_once plugin_dir_path( __FILE__ ) . '/class-abclocalpartnerwp-updater.php';

$updater = new AbcLocalPartnerWp_Updater( __FILE__ );
$updater->set_username( 'rtvnh' );
$updater->set_repository( 'abc-local-wp-custom' );
$updater->initialize();

$abc_post_status = false;

/**
 * Register our plugin settings.
 */
function abclocalpartner_register_settings(): void {
	add_option( 'abclocalpartner_option_abc_url' );
	add_option( 'abclocalpartner_option_partner_name' );
	add_option( 'abclocalpartner_option_partner_client_id' );
	add_option( 'abclocalpartner_option_partner_client_secret' );
	add_option( 'abclocalpartner_option_access_token' );
	add_option( 'abclocalpartner_option_region_name' );

	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_abc_url'
	);
	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_partner_name'
	);
	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_partner_client_id'
	);
	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_partner_client_secret'
	);
	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_access_token'
	);
	register_setting(
		'abclocalpartner_options_group',
		'abclocalpartner_option_region_name'
	);
}

add_action( 'admin_init', 'abclocalpartner_register_settings' );

/**
 * Register the options page for our plugin.
 */
function abclocalpartner_register_options_page(): void {
	add_options_page( 'ABC Manager - Settings', 'ABC Manager', 'manage_options', 'abclocalpartner', 'abclocalpartner_options_page' );
}

add_action( 'admin_menu', 'abclocalpartner_register_options_page' );

/**
 * Generate the options page for our plugin.
 */
function abclocalpartner_options_page(): void {
	?>
	<div>
		<h1>RTV NH/AT5 - ABC Manager</h1>
		<div>
			<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>/assets/images/nh-at5-logo.png" height="64" width="64" alt="NH"/>
		</div>
		<form method="post" action="options.php">
			<?php settings_fields( 'abclocalpartner_options_group' ); ?>
			<h2>ABC Manager Options</h2>
			<p>Please adjust the settings for a connection with ABC.</p>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<label for="abclocalpartner_option_abc_url">API URL</label>
						</th>
						<td>
							<input type="text" id="abclocalpartner_option_abc_url" name="abclocalpartner_option_abc_url"
								class="regular-text"
								value="<?php echo esc_url( get_option( 'abclocalpartner_option_abc_url' ) ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="abclocalpartner_option_partner_name">Partner name</label>
						</th>
						<td>
							<input type="text" id="abclocalpartner_option_partner_name"
								name="abclocalpartner_option_partner_name" class="regular-text"
								value="<?php echo esc_attr( get_option( 'abclocalpartner_option_partner_name' ) ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="abclocalpartner_option_partner_client_id">Client ID</label>
						</th>
						<td>
							<input type="text" id="abclocalpartner_option_partner_client_id"
								name="abclocalpartner_option_partner_client_id" class="regular-text"
								value="<?php echo esc_attr( get_option( 'abclocalpartner_option_partner_client_id' ) ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="abclocalpartner_option_partner_client_secret">Client Secret</label>
						</th>
						<td>
							<input type="password" id="abclocalpartner_option_partner_client_secret"
								name="abclocalpartner_option_partner_client_secret" class="regular-text"
								value="<?php echo esc_attr( get_option( 'abclocalpartner_option_partner_client_secret' ) ); ?>"/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="abclocalpartner_option_region_name">Region taxonomy name</label>
						</th>
						<td>
							<input type="text" id="abclocalpartner_option_region_name"
								name="abclocalpartner_option_region_name" class="regular-text"
								value="<?php echo esc_attr( get_option( 'abclocalpartner_option_region_name' ) ); ?>"/>
						</td>
					</tr>
					<style type="text/css">
						.status-dot {
							height: 10px;
							width: 10px;
							border-radius: 1000px;
							display: inline-block;
						}

						.status-dot.is-red {
							background-color: red;
						}

						.status-dot.is-green {
							background-color: green;
						}
					</style>
					<?php
					// Are ABC Manager and WordPress connected?
					$online       = check_abc_status();
					$status       = $online ? 'Up' : 'Down';
					$status_color = $online ? 'green' : 'red';
					?>
					<tr>
						<th>
							ABC Manager status:
						</th>
						<td>
							<span class="status-dot is-<?php echo esc_attr( $status_color ); ?>"></span> <?php echo esc_html( $status ); ?>
						</td>
					</tr>

					<?php
					// Are ABC Manager credentials valid?
					$valid        = check_credentials();
					$status       = $valid ? 'Valid' : 'Invalid';
					$status_color = $valid ? 'green' : 'red';
					?>
					<tr>
						<th>
							Credentials:
						</th>
						<td>
							<span class="status-dot is-<?php echo esc_attr( $status_color ); ?>"></span> <?php echo esc_html( $status ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Get a bearer token from ABC Manager.
 *
 * @param bool $force_new  With this option set to true, you can force to always retrieve a new bearer token.
 *
 * @return string|null
 */
function get_abc_bearer_token( $force_new = false ) {
	$bearer_token = get_option( 'abclocalpartner_option_access_token' );

	if ( ! empty( $bearer_token ) && ! $force_new ) {
		return $bearer_token;
	}

	$api_endpoint  = get_option( 'abclocalpartner_option_abc_url' );
	$client_id     = get_option( 'abclocalpartner_option_partner_client_id' );
	$client_secret = get_option( 'abclocalpartner_option_partner_client_secret' );

	$raw_response = wp_remote_post(
		$api_endpoint . '/oauth2/token',
		array(
			'headers' => array_merge( get_environment_headers(), array( 'Content-Type' => 'application/x-www-form-urlencoded' ) ),
			'body'    => array(
				'grant_type'    => 'client_credentials',
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'scope'         => 'partners',
			),
		)
	);

	if ( is_array( $raw_response ) ) {
		try {
			$data = json_decode( $raw_response['body'], true, 512, JSON_THROW_ON_ERROR );

			if ( array_key_exists( 'access_token', $data ) ) {
				update_option( 'abclocalpartner_option_access_token', $data['access_token'] );

				return $data['access_token'];
			}
		} catch ( Exception $e ) {
			return null;
		}
	}

	return null;
}

/**
 * This does a simple check if the credentials are valid.
 *
 * @param int $attempts Total of attempts.
 *
 * @return bool
 */
function check_credentials( $attempts = 0 ): bool {
	$token = get_abc_bearer_token();

	$api_endpoint = get_option( 'abclocalpartner_option_abc_url' );
	$partner_name = get_option( 'abclocalpartner_option_partner_name' );

	try {
		$response = wp_remote_get(
			sprintf( '%s/partner/article?partner=%s', $api_endpoint, $partner_name ),
			array(
				'headers' => array_merge( get_environment_headers(), array( 'Authorization' => sprintf( 'Bearer %s', $token ) ) ),
			)
		);

		if ( $response instanceof WP_Error ) {
			return false;
		}

		if ( array_key_exists( 'response', $response ) ) {
			switch ( $response['response']['code'] ) {
				case 204:
					return true;
				case 401:
					if ( 1 === $attempts ) {
						return false;
					}

					get_abc_bearer_token( true );

					return check_credentials( 1 );
				default:
					return false;
			}
		}
	} catch ( \Exception $e ) {
		return false;
	}

	return false;
}

/**
 * Used for development purposes only, adds Host header to all requests
 *
 * @return array|string[]
 */
function get_environment_headers(): array {
	return wp_get_environment_type() === 'local' ? array( 'Host' => 'partner.test' ) : array();
}

/**
 * This does a simple check if ABC Manager is available
 *
 * @return bool
 */
function check_abc_status(): bool {
	$api_endpoint = get_option( 'abclocalpartner_option_abc_url' );
	$partner_name = get_option( 'abclocalpartner_option_partner_name' );

	try {
		$response = wp_remote_get(
			sprintf( '%s/partner/article?partner=%s', $api_endpoint, $partner_name ),
			array(
				'headers' => get_environment_headers(),
			)
		);

		if ( $response instanceof WP_Error ) {
			return false;
		}

		if ( array_key_exists( 'response', $response ) ) {
			switch ( $response['response']['code'] ) {
				case 401:
					return true;
				default:
					return false;
			}
		}
	} catch ( \Exception $e ) {
		return false;
	}

	return false;
}

/**
 * Post an article to ABC Manager.
 *
 * @param WP_Post  $post             The WordPress post instance.
 * @param string[] $post_galleries   A list of galleries from the post content.
 * @param string   $post_featured    An URL for the featured image in the post.
 * @param int      $attempts         The amount of retry attempts.
 *
 * @return bool
 */
function post_article_to_abc_manager(
    WP_Post $post,
    array $post_galleries,
    string $post_featured,
    int $attempts = 0
): bool {
	$galleries_json  = wp_json_encode( $post_galleries );
	$api_endpoint    = get_option( 'abclocalpartner_option_abc_url' );
	$partner_name    = get_option( 'abclocalpartner_option_partner_name' );
	$region_taxonomy = get_option( 'abclocalpartner_option_region_name' );
	$bearer_token    = get_abc_bearer_token();
	$regions         = get_the_terms( $post, $region_taxonomy );

	if ( empty( $bearer_token ) ) {
		return false;
	}

    global $wpdb;

    $podcastMeta = $wpdb->get_results("
        SELECT post_id, meta_key, meta_value
        FROM wp_postmeta
        INNER JOIN wp_toolset_connected_elements
        ON wp_postmeta.post_id = wp_toolset_connected_elements.element_id
        INNER JOIN wp_toolset_associations
        ON wp_toolset_connected_elements.group_id = wp_toolset_associations.child_id
        WHERE wp_toolset_associations.parent_id = (
            SELECT group_id
            FROM wp_toolset_connected_elements
            WHERE wp_toolset_connected_elements.element_id = {$post->ID}
        )
        AND wp_toolset_associations.relationship_id = (
            SELECT id
            FROM wp_toolset_relationships
            WHERE wp_toolset_relationships.slug = 'audio-item'
        )
    ");

    if (count($podcastMeta) > 0) {
        $podcastData = [];

        foreach ($podcastMeta as $meta) {
            if (!empty(trim($meta->meta_value))) {
                $podcastData[$meta->post_id][$meta->meta_key] = $meta->meta_value;
            }
        }

        foreach ($podcastData as $data) {
            $date = wp_date('d F Y', $data['wpcf-datum-uitzending-fragment']);

            $post->post_content .= "<!-- wp:custom-html -->
            <custom-html>
              <h1>Uitzending - {$date}</h1>
              <p>{$data['wpcf-beschrijving-audiofragment']}</p>
            </custom-html>
            <!-- /wp:custom-html -->
            <!-- wp:audio -->
            <figure class='wp-block-audio'><audio controls src='{$data['wpcf-audiobestand']}'></audio></figure>
            <!-- /wp:audio -->
            ";
        }
    }

	$response = wp_remote_post(
		$api_endpoint . '/partner/article',
		array(
			'body'    => array(
				'partner'   => $partner_name,
				'content'   => wp_json_encode( $post ),
				'featured'  => $post_featured,
				'galleries' => $galleries_json,
				'regions'   => wp_json_encode( $regions ),
			),
			'headers' => array_merge( get_environment_headers(), array( 'Authorization' => 'Bearer ' . $bearer_token ) ),
		)
	);

	if ( $response instanceof WP_Error ) {
		return false;
	}

	if ( 'Partner not authorized.' === $response['body'] ) {
		return true;
	}
	if ( 'Unauthorized' === $response['body'] ) {
		if ( 0 === $attempts ) {
			get_abc_bearer_token( true );

			post_article_to_abc_manager( $post, $post_galleries, $post_featured, $attempts + 1 );
		}

		return false;
	}

	return true;
}

/**
 * Register a hook on "save_post".
 *
 * @param WP_Post $post The WordPress post.
 */
function abclocalpartner_post_to_abc( WP_Post $post ): void {
	if ( ! empty( get_option( 'abclocalpartner_option_abc_url' ) ) &&
		! empty( get_option( 'abclocalpartner_option_partner_client_id' ) ) &&
		! empty( get_option( 'abclocalpartner_option_partner_client_secret' ) )
	) {
		if ( get_post_status( $post ) === 'publish' && $post->post_type === 'post' ) {
			$post_galleries = get_post_galleries( $post );
			$post_featured  = get_the_post_thumbnail_url( $post );

			if ( is_bool( $post_featured ) ) {
				$post_featured = '';
			}

			global $abc_post_status;
			$abc_post_status = post_article_to_abc_manager(
				$post,
				$post_galleries,
				$post_featured
			);
		}
	}
}

/**
 * Triggers only on gutenberg save calls
 *
 * @param   WP_Post $post WordPress post.
 */
function gutenberg_post_to_abc( WP_Post $post ): void {
	// Prevent save calls from ABC Manager to be also send to ABC Manager back again.
    // phpcs:ignore
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_GET['abc'] ) ) {
		return;
	}

	abclocalpartner_post_to_abc( $post );
}

add_action( 'rest_after_insert_post', 'gutenberg_post_to_abc', 10, 1 );

/**
 * Allow iframe HTML tags.
 *
 * @param string[] $tags    A list of HTML tags.
 * @param string   $context The context to judge allowed tags by.
 *
 * @return mixed[]
 */
function prefix_add_source_tag( array $tags, string $context ): array {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'    => true,
			'srcdoc' => true,
			'width'  => true,
			'height' => true,
		);
	}
	return $tags;
}

add_filter( 'wp_kses_allowed_html', 'prefix_add_source_tag', 10, 2 );

/**
 * Add custom meta values for posts from ABC manager
 *
 * @param $response
 * @param $object
 * @param $request
 * @return mixed
 */
function default_meta_from_abc_post( $response, $object, $request) {
    if (
        isset($response[ 'type' ]) &&
        $response[ 'type' ] === 'post' &&
        defined( 'REST_REQUEST' ) &&
        REST_REQUEST &&
        isset( $_GET[ 'abc' ] )
    ) {
        $post_id = $response[ 'id' ];
        // Add citation to featured image
        if (isset($_GET[ 'imageCitation' ])) {
            update_post_meta($post_id, 'wpcf-bronvermelding-foto', $_GET[ 'imageCitation' ]);
        }

        // New post, set wpcf-laat-artikel-niet-zien-op-voorpagina to false
        if ($request->get_route() === '/wp/v2/posts') {
            update_post_meta($post_id, 'wpcf-laat-artikel-niet-zien-op-voorpagina', 0);
        }
    }

    return $response;
}

add_filter( 'rest_pre_echo_response', 'default_meta_from_abc_post', 10, 3 );
