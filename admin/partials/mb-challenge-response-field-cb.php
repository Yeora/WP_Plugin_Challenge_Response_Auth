<?php
defined( 'ABSPATH' ) or die( 'No direct access' );

if ( ! isset( $args ) ) {
	return;
}

// Get the value of the setting registered with register_setting()
$options = get_option( 'mb_challenge_response_options' );

?>

    <select
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['mb_challenge_response_custom_data'] ); ?>"
            name="mb_challenge_response_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
        <option value="1" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], true, false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'Ja','mb-challenge-response-authentication' ); ?>
        </option>
        <option value="0" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], false, false ) ) : ( '' ); ?>>
			<?php esc_html_e( 'Nein', 'mb-challenge-response-authentication' ); ?>
        </option>
    </select>
    <p class="description">
		<?php esc_html_e( 'Ja - erzwingt die Challenge Response Authentifizierung. Benutzer mit deaktiviertem JavaScript oder einem älteren Passwort ohne Salt können
			sich nicht mehr anmelden.', 'mb-challenge-response-authentication' ); ?>
    </p>
    <p class="description">
		<?php esc_html_e( 'Nein - erzwingt keine Challenge Response Authentifizierung, bevorzugt diese aber.', 'mb-challenge-response-authentication' ); ?>
    </p>
<?php