<?php
/*
Plugin Name: chess games player
Plugin URI: https://github.com/beuk/pac
Description: Store chess games and refer to them using a shortcode
Version: 0.6
Author: Paul van Beukering
Author URI: http://www.beukenoot.nl
License: GPL2
*/
/*
Copyright 2014  Paul van Beukering (email : paul.van.beukering@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $pac_db_version;
$pac_db_version = "0.6";

if(!class_exists('ChessGames_Player')) {
	class ChessGames_Player {

		// Construct the plugin object
		public function __construct() {
			require_once(sprintf("%s/src/chessgames-games.php", dirname(__FILE__)));
			$chessGames_Games = new ChessGames_Games();
			require_once(sprintf("%s/src/chessgames-pgn-viewer.php", dirname(__FILE__)));
			$chessGames_Pgn_Viewer = new ChessGames_Pgn_Viewer();
		}

		// Activate the plugin
		public static function activate() {
			error_log('activate');
			global $wpdb;
			global $pac_db_version;
			error_log('handle db table version: ' . $pac_db_version);

			$table_name = $wpdb->prefix . "pac_chessgames";

				//EventDate datetime DEFAULT '0000-00-00',
				//GameDate datetime DEFAULT '0000-00-00' NOT NULL,
				//id mediumint(9) NOT NULL AUTO_INCREMENT,
			$sql = "CREATE TABLE $table_name (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				name tinytext,
				wp_uid bigint(20) unsigned NOT NULL,
				InsertDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				wp_white_uid bigint(20) unsigned,
				wp_black_uid bigint(20) unsigned,
				Event tinytext NOT NULL,
				FEN VARCHAR(512),
				SetUp TINYINT,
				Site tinytext,
				Source tinytext,
				Round tinytext,
				EventDate tinytext,
				GameDate tinytext,
				White tinytext NOT NULL,
				WhiteElo SMALLINT,
				Black tinytext NOT NULL,
				BlackElo SMALLINT,
				Result ENUM('1-0', '0-1' , '1/2-1/2' , '*') NOT NULL,
				ECO tinytext,
				gametype ENUM('PGN', 'FEN') NOT NULL,
				pgn text NOT NULL,
				url tinytext,
				UNIQUE KEY id (id)
			);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			add_option( "pac_db_version", $pac_db_version );
		}

		 // Deactivate the plugin
		public static function deactivate() {
			error_log('deactivate');
		}
	}
}

if(class_exists('ChessGames_Player')) {

	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('ChessGames_Player', 'activate'));
	register_deactivation_hook(__FILE__, array('ChessGames_Player', 'deactivate'));

	// instantiate the plugin class
	$chessGames_player = new ChessGames_Player();

	// Add a link to the settings page onto the plugin page
	if(isset($chessGames_player)) {
		// Add the settings link to the plugins page
		function pac_plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=wp_plugin_template">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		function custom_toolbar() {
			global $wp_admin_bar;

			$add_pgn_href = site_url() . '/wp-admin/admin.php?page=chessgames_pgn';
			$my_games_href = site_url() . '/wp-admin/admin.php?page=chessgames_games';
			$args = array(
				'id'     => 'pgn-menu',
				'title'  => __( 'Mijn partijen', 'text_domain' ),
				'href'   => $my_games_href,
			);
			$wp_admin_bar->add_menu( $args );
			$args = array(
				'id'     => 'child-menu',
				//'parent' => 'new-content',
				'parent' => 'pgn-menu',
				'title'  => __( 'Partij toevoegen', 'text_domain' ),
				'href'   => $add_pgn_href,
			);
			$wp_admin_bar->add_menu( $args );
		}

		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_$plugin", 'pac_plugin_settings_link');
		add_action( 'wp_before_admin_bar_render', 'custom_toolbar' , 999 );
	}

}
?>
