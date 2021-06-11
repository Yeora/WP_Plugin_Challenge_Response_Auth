<?php

/**
 * Class Mb_Login_Helper loads login scripts
 */
class Mb_Login_Helper {
	public function login_script(): void {
		$path      = plugin_dir_url( __FILE__ ) . '../../public/js/';
		$path_libs = $path . 'libs/';
		wp_enqueue_script( 'base64.js', $path_libs . 'base64.js' );
		wp_enqueue_script( 'utf8.js', $path_libs . 'utf8.js' );
		wp_enqueue_script( 'impl.js', $path_libs . 'impl.js' );
		wp_enqueue_script( 'util.js', $path_libs . 'util.js' );
		wp_enqueue_script( 'bcrypt.js', $path_libs . 'bcrypt.js' );
		wp_enqueue_script( 'wrap.js', $path_libs . 'wrap.js' );
		wp_enqueue_script( 'challenge-response-auth.js', $path . 'challenge-response-auth.js' );
	}
}