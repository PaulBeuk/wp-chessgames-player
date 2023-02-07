<?php
if(!class_exists('ChessGames-Games')) {
	class ChessGames_Games {

		const PAGERSIZE = 50;
		public $templateView;
		public $chessGames_Pgn_Viewer;
		public $pgnDB;

		/* Construct the plugin object */
		public function __construct() {

			include_once "chessgames-template-view.php";
			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once "pgn-parser.php";
			require_once "pgn-db.php";
			require_once "pgn.php";
			require_once "chessgames-pgn-viewer.php";

			$this->chessGames_Pgn_Viewer = new ChessGames_Pgn_Viewer();
			$this->templateView = new ChessgamesTemplateView(dirname(__FILE__) . '/../templates/');
			$this->pgnDB = new PgnDB();

			// register actions
			add_action('admin_menu', array(&$this, 'add_menu'));
			add_action('admin_head', array(&$this, 'mygames_help_tab') , 0 );
			add_filter('upload_mimes', array(&$this, 'my_myme_types'), 1, 1);
		}

		function my_myme_types($mime_types){
			$mime_types['pgn'] = 'text/plain';
			return $mime_types;
		}

		public function mygames_help_tab() {
			if( isset($_GET['page'] ) ) {
				$page = $_GET['page'];
				if ( $page === 'chessgames_games' || $page === 'chessgames_pgn' ) {
					$helpContent =
						'<p>Hier komt help text voor het toevoegen van caissa partijen</p>' .
						'<ul>' .
							'<li>over hoe je partijen invoert</li>' .
							'<li>over hoe je partijen kan uploaden</li>' .
							'<li>over dat je kan zoeken naar partijen</li>' .
							'<li>over wat pgn is</li>' .
							'<li>Hoe publiceer je een partij? Plak [game id="&lt;gameID&gt;"] in een artikel</li>' .
							'<li>over de parameters die er zijn als je een partij post</li>' .
						'</ul>' .
						'<p><strong>For more information:</strong></p>' .
						'<p>' .
							'<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">' .
								'Edit Posts Documentation' .
							'</a>' .
							'<a target="_blank" href="http://en.wikipedia.org/wiki/Portable_Game_Notation">' .
								'Wat is een PGN bestand' .
							'</a>' .
						'</p>';
				  $screen = get_current_screen();

				  $args = array(
					'id'      => 'mygames_help',
					'title'   => 'Mijn partijen help',
					'content' => $helpContent );

				  $screen->add_help_tab( $args );
				}
			}
		}
		/* add a menu */
		public function add_menu() {
			add_menu_page('Mijn partijen', 'Mijn partijen', 'edit_posts', 'chessgames_games',
				array(&$this, 'pac_to_mygames'), plugins_url( '../images/knight.png' , __FILE__ ), 3);

			add_submenu_page(
				'chessgames_games', 'Partij toevoegen', 'Partij toevoegen', 'edit_posts',
				'chessgames_pgn', array(&$this, 'pac_add_pgn') );
		}

		/* Add PGN Callback */
		function pac_add_pgn(){
			if(!current_user_can('edit_posts')) {
				//wp_die(__('You do not have sufficient permissions to access this page.'));
				$this->pac_to_mygames( "you do not have sufficient permissions to execute this, sorry!");
			}

			/* Delete the data if the variable "delete" is set */
			if( isset($_GET['view']) ) {
				$viewId = absint($_GET['view']);
				$attributes = array('id' => $viewId ,'template' => 'view-pgn.phtml');
				echo $this->chessGames_Pgn_Viewer->view_game( $attributes );
				return;
			}
			if( isset($_GET['delete']) ) {
				$deleteId = absint($_GET['delete']);
				$this->pgnDB->deleteGame( $deleteId );
				$this->pac_to_mygames( "partij <" . $deleteId . "> is verwijderd");
				return;
			}
			if( isset($_FILES['mypgn']) ) {
				$uploadedfile = $_FILES['mypgn'];
				$upload_overrides = array( 'test_form' => false );
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				if ( $movefile ) {
					$fileData = file_get_contents($movefile["file"] , true);
					$pgn_parser = new PgnParser();
					$lines = $pgn_parser->contentToLines( $fileData );
					$pgn_parser = new PgnParser();
					$pgns = $pgn_parser->contentToPgns( $fileData );
					foreach( $pgns as $part ) {
						//echo $part . "\n---------------------------------------\n";
						$pgn = new Pgn("PGN");
						$part = str_replace("'","\\'", $part);
						$pgn->setPGN($part);
						$pgn->setUID( get_current_user_id() );
						$pgn->setUrl( $movefile["url"] );
						$lines = $pgn_parser->contentToLines( $part );
						$pgn = $pgn_parser->parse( $pgn , $lines );
						$this->pgnDB->insertPgn( $pgn );
						echo "inserted: White: ".$pgn->White." Black: ".$pgn->Black." At: ".$pgn->Date." Event: ".$pgn->Event."</BR>";
					}
					$this->pac_to_mygames();
				} else {
					echo "Possible file upload attack!\n";
				}
			}
			if( isset($_GET['edit']) ) {
				$editId = absint($_GET['id']);

				$users = $this->pgnDB->get_all_users();
				$this->templateView->users = $users;
/*
				$users = get_users();
				$this->templateView->users = $users;
*/
				$title = __('Edit PGN');
				$this->templateView->formTitle = __('Edit PGN');
				$games = $this->pgnDB->getGame( $editId );
				if(!empty($games[0])) {
					error_log('looks like i found game by id: ' . $editId );
					$this->templateView->game = $games[0];
					$this->templateView->render('edit-pgn.phtml');
				} else {
					error_log('no game found by id: ' . id );
					$this->templateView->render('game-not-found.phtml');
				}
				//$this->pac_to_mygames( "partij <" . $editId . "> gaan we aanpassen");
				return;
			}
			if( isset($_POST['editid']) ) {
				$editId = absint($_POST['editid']);
				$this->pgnDB->updatePGN( $_POST );
				$this->pac_to_mygames( "partij <" . $editId . "> is aangepast");
				return;
			}
			/* new pgn */
			if( isset($_POST[ 'chessgames_pgntext' ] )  ) {
				$chessgames_pgntext = $_POST[ 'chessgames_pgntext' ];
				error_log('found new chessgame to insert ' . $chessgames_pgntext );
				$pgn_parser = new PgnParser();
				$lines = $pgn_parser->contentToLines( $chessgames_pgntext );
				$pgn = new Pgn("PGN");
				$pgn->setPGN($chessgames_pgntext);
				$pgn->setUID( get_current_user_id() );
				$pgn = $pgn_parser->parse( $pgn , $lines );
				$this->pgnDB->insertPgn( $pgn );
				$this->pac_to_mygames("partij is toegevoegd");
				return;
			}
			$this->templateView->render('add-pgn.phtml');
		}

		/* Menu Callback */
		function pac_to_mygames( $message ){
			$back = false;
			if( isset($_GET['back']) ) {
				$back = true;
			}
			if( isset($_GET['offset']) ) {
				$offset = absint($_GET['offset']);
			} else {
				$offset = 0;
			}
			if( isset($_GET['size']) ) {
				$size = absint($_GET['size']);
			} else {
				$size = self::PAGERSIZE;
			}
			if( $back ) {
				$offset = $offset - ( $size * 2 );
				if ( $offset < 0 )
					$offset = 0;
			}
			if( isset($_GET['search']) ) {
				if( isset($_GET['id']) ) {
					$searchId = $_GET['id'];
					$games = $this->pgnDB->getGame($searchId);
					if(!empty($games[0])) {
						$this->templateView->games = $games;
						$this->templateView->render('my-games.phtml');
					} else {
						$this->templateView->render('game-not-found.phtml');
					}
					return;
				}
				if( isset($_GET['pgn']) ) {
					$searchStr = $_GET['pgn'];
					$this->templateView->games = $this->pgnDB->searchPgnsByContent($searchStr,$offset,$size);
					$this->templateView->offset = $offset + $size;
						//error_log( "offset nowww: " . $this->templateView->offset  );
					$this->templateView->render('my-games.phtml');
					return;
				}
			}
			if(!current_user_can('edit_posts')) {
				//wp_die(__('You do not have sufficient permissions to access this page.'));
				$message = "you do not have sufficient permissions to execute this, sorry!";
			}

			$this->templateView->games = $this->pgnDB->getPgnsByUID( get_current_user_id() , $offset , $size );
			$this->templateView->offset = $offset + $size;
			if( isset( $message ) ) {
				$this->templateView->setMessage($message);
			}
			//error_log( "offset nowww: " . $this->templateView->offset  );
			$this->templateView->render('my-games.phtml');
		}
	}
}
?>
