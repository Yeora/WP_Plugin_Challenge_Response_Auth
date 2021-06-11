<?php
defined( 'ABSPATH' ) or die( 'No direct access' );

// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

// show error/update messages
settings_errors( 'mb_challenge_response_section_developers' );
?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
			<?php
			// output security fields for the registered setting
			settings_fields( 'mb_challenge_response' );
			// output setting sections and their fields
			do_settings_sections( 'mb_challenge_response' );
			// output save settings button
			submit_button();
			?>
        </form>
    </div>
<?php