=== MB Challenge response authentication ===
Contributors: mabipress
Donate link: -
Tags: hash, security, challenge response
Requires at least: 5.7.0
Tested up to: 5.7.2
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This plugin implements challenge response authentication. In addition, the WordPress hasher is replaced by native PHP libraries.

== Description ==

The "MB Challenge response authentication" plugin extends the
default WordPress authentication with a challenge response authentication.
This ensures that passwords during login are no longer stored in the
clear text during the login process.

Via a menu item in the administration you can also set whether the challenge response authentication should be enforced or not. If challenge response authentication is not enforced
the default WordPress authentication is allowed as fallback.
This is the case if a user cannot hash on the client side.

Furthermore, the default WordPress hasher is overridden and PHP native functions like password_hash and password_verify are used.

== Installation ==

Upload the plugin to your Website, Activate it.
If you like, you can disable the challenge response authentication enforcement
under the settings to not exclude users without JavaScript.
That's it. You're done!

== Screenshots ==

1. Challenge Response Authentication
2. Backend Menu

== Credits ==
Special thanks to the developers of the
bcrypt.js library https://github.com/dcodeIO/bcrypt.js.
The library is used for client-side hashing.

== Changelog ==

= 1.0 =
* Init Version