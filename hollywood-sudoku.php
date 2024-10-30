<?php
/**
 * Plugin Name: Hollywood Sudoku
 * Description: Add a Sudoku puzzle to any of your pages by shortcode or the block editor.
 * Version: 1.0.2
 * Author: sudorku
 * Author URI: https://metapult.com/sudoku
 * License: GPLv2 or later
 * Text Domain: hollywood-sudoku
 * Domain Path: /languages/
 */
/*
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

defined( 'ABSPATH' ) or die( 'No Trespassing!' ); // Security

define( 'HOLLYWOOD_SUDOKU_PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ) );

class Hollywood_Sudoku {
	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
			$instance->init();
		}
		return $instance;
	}

	/**
	 * Construct and initialize the main plugin class
	 */

	public function __construct() {
	}

	public function init() {
		// Init the plugin.
		add_action( 'init', array( $this, 'plugin_init' ) );
		add_action( 'plugins_loaded', array( $this, 'load_translation' ) );
		add_action( 'rest_api_init', array( $this, 'api_init' ) );
	}

	public function plugin_init() {
		add_shortcode( 'sudoku', array($this,'shortcode'));
		add_filter("plugin_row_meta", array($this,'add_plugin_meta_links'), 10, 2);
		if ( function_exists( 'register_block_type' ) ) {
			wp_register_script(
				'hollywood-sudoku-board',
				plugins_url( 'views/board.js', __FILE__ ),
				array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
				filemtime( plugin_dir_path( __FILE__ )."views/board.js")
			);
			wp_register_script(
				'hollywood-sudoku-block-editor',
				plugins_url( 'views/block.js', __FILE__ ),
				array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
				filemtime( plugin_dir_path( __FILE__ )."views/block.js")
			);

			register_block_type( 'hollywood/sudoku', [
				'render_callback' => array($this,'shortcode'),
				'editor_script' => 'hollywood-sudoku-block-editor',
			] );

		}

	}

	public function add_plugin_meta_links($meta_fields, $file) {
		if ( plugin_basename(__FILE__) == $file ) {
			$plugin_url = "https://wordpress.org/support/plugin/hollywood-sudoku";
			$prefix = 'hollywood-sudoku';
			$title = __('Rate', $prefix);
			$meta_fields[] = "<a href='" . $plugin_url . "/#new-post' target='_blank'>" . __('Ask a question', $prefix) . "</a>";
			$meta_fields[] = <<<__STARS
				<a href='$plugin_url/reviews#new-post' target='_blank' title='$title'><i class='wdi-rate-stars'>
				<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>
				<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>
				<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>
				<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>
				<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>
				</i></a>
__STARS;

			$stars_color = "#ffb900";

			echo "<style>"
				. ".wdi-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
				. ".wdi-rate-stars svg{fill:" . $stars_color . ";}"
				. ".wdi-rate-stars svg:hover{fill:" . $stars_color . "}"
				. ".wdi-rate-stars svg:hover ~ svg{fill:none;}"
				. "</style>";
		}

		return $meta_fields;
	}

	public function shortcode($atts=array(),$content='',$tag=''){
		$out = 'Only one SUDOKU per page please.';
		if(!isset($this->once)){
			wp_enqueue_script(
				'hollywood-sudoku-board',
				plugins_url( 'views/board.js', __FILE__ ),
				array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
				filemtime( plugin_dir_path( __FILE__ )."views/board.js")
			);

			$options = array(
				'new_board' => rest_url('h_sudoku/v1/new'),
				'hint' => rest_url('h_sudoku/v1/hint/'),
				'solve' => rest_url('h_sudoku/v1/solve/'),
			);
			wp_localize_script('hollywood-sudoku-board','h_sudoku',$options);
			//Can only display ONE SUDOKU BOARD per page
			$this->once = true;
			ob_start();
			include_once( HOLLYWOOD_SUDOKU_PLUGIN_DIRECTORY . '/views/board.php' );
			$out = ob_get_contents();
			ob_end_clean();
		}
		return $out;
	}

	/**
	 * Localization
	 */
	public function load_translation() {
		load_plugin_textdomain( 'hollywood-sudoku', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	public function api_init(){
		$options = [
			'methods'             => WP_REST_Server::ALLMETHODS,
			'callback'            => [ $this, 'api_bass' ],
			'permission_callback' => [ $this, 'api_grant_access' ],
		];

		register_rest_route( 'h_sudoku/v1','new',$options,true);
		register_rest_route( 'h_sudoku/v1','hint/(?P<state>[0-9.]+)',$options,true);
		register_rest_route( 'h_sudoku/v1','solve/(?P<state>[0-9.]+)',$options,true);
	}

	public function api_bass(){
		include_once( HOLLYWOOD_SUDOKU_PLUGIN_DIRECTORY . '/models/Sudoku.php' );
		include_once( HOLLYWOOD_SUDOKU_PLUGIN_DIRECTORY . '/controllers/PuzzleController.php' );
		$parts = explode("/", $_SERVER["REQUEST_URI"]);
		$api = false;
		foreach ($parts as $arg) {
			if($arg=="v1")$api=array();
			elseif(is_array($api))$api[]=$arg;
		}

		switch($api[0]){
		case "new":
			do{
				$ss = new hs_Hollywood_SudokuSolver();
				$ss->random();
				while($ss->incomplete()&&$ss->progressing())$ss->apply($ss->hint());
			}while(strpos($ss->state,".")!==false); //Look for a solveable puzzle
			$out = ($ss->origin);
			break;
		case "hint":
			$ss = new hs_Hollywood_SudokuSolver();
			if($api[1]){
				$out = ($ss->import($api[1])->hint());
			}
			break;
		case "solve":
			$ss = new hs_Hollywood_SudokuSolver();
			if($api[1]){
				$ss->import($api[1]);
				while($ss->incomplete()&&$ss->progressing())$ss->apply($ss->hint());
			}
			$out = ($ss->toString());
			break;
		}
		return array('state'=>$out);
	}

	public function api_grant_access(){
		return true; 
	}

}
Hollywood_Sudoku::get_instance();
