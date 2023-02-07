<?php
if(!class_exists('ChessGames-Pgn-Viewer')) {
	class ChessGames_Pgn_Viewer {

		public $pgnDB;
		public $templateView;
		CONST DEFAULT_CSS = "/caissa/pvdw/board-min.css";
		CONST DEFAUL_TEMPLATE = "chess-tempo.phtml";
		CONST MOVES_FORMAT = "default";
		CONST DIAGRAM_ONLY = "false";
		CONST PIECE_SIZE = "35";
		CONST SHOW_HEADER = "TRUE";
		CONST MOVETEXT_ALIGN = "left";
		CONST NAVBAR_ENABLED = "TRUE";
 		public $defaultCssPath;

		public function __construct() {

			require_once 'pgn-db.php';
			require_once 'chessgames-template-view.php';
			$this->templateView = new ChessgamesTemplateView(dirname(__FILE__) . '/../templates/');
			$this->pgnDB = new PgnDB();
			$this->defaultCssPath = site_url() . $this::DEFAULT_CSS;
			add_shortcode('game', array(&$this, 'view_game'));
			add_shortcode('chessgame', array(&$this, 'view_game'));
		}

		function renderContent($gameid,$content,$css,$game,$message,
			$template,$owner,$diagram_only,$showHeader,$piece_size,
			$movetext_align,$moves_format,$navbar_enabled){
			$this->templateView->gameReference= $gameid . "_" . rand(0,30000);
			$this->templateView->gameid = $gameid;
			$this->templateView->pgn = $content;
			$this->templateView->game = $game;
			$this->templateView->css = $css;
			$this->templateView->message = $message;
			$this->templateView->owner = $owner;
			$this->templateView->setDiagramOnly( strtolower($diagram_only) );
			$this->templateView->setShowHeader( $showHeader );
			$this->templateView->setPieceSize( $piece_size );
			$this->templateView->setMovesFormat( $moves_format );
			$this->templateView->setMovetextAlign( $movetext_align );
			$this->templateView->setNavbarEnabled( $navbar_enabled );
			ob_start();
			$this->templateView->render( $template );
			$i_out = ob_get_contents();
			ob_end_clean();
			return $i_out;
		}

		public function view_game( $atts ) {
			extract( shortcode_atts( array(
				'id' => 'notset',
				'scope' => 'public',
				'template' => $this::DEFAUL_TEMPLATE,
				'css' => $this->defaultCssPath,
				'diagram_only' => $this::DIAGRAM_ONLY,
				'show_header' => $this::SHOW_HEADER,
				'piece_size' => $this::PIECE_SIZE,
				'moves_format' => $this::MOVES_FORMAT,
				'movetext_align' => $this::MOVETEXT_ALIGN,
				'navbar_enabled' => $this::NAVBAR_ENABLED,
				'message' => '',
			), $atts ) );

			if ( $scope != 'public' ) {
				if ( ! is_user_logged_in() ) {
					return 'this game is only visible when you are logged in';
				}
			}

			if ( $id != 'notset' ) {
				$pgnId = absint($id);
				$games = $this->pgnDB->getGame( $pgnId );
				if(empty($games[0])) {
					error_log('no game found by id: ' . id );
					$this->templateView->render('game-not-found.phtml');
				}
				//error_log('looks like i found game by id: ' . $editId );
				$pgn = $games[0];
				if( ! is_null($pgn) ) {
					$owner = get_user_by( 'id' , $pgn->wp_uid );
					$gameid = "gameid_".$pgn->id;
					$content = trim(preg_replace('/\s\s+/', ' ', $pgn->pgn));
					$content = trim(preg_replace('/\n+/', ' ', $content));
					$content = trim(preg_replace('/\r+/', ' ', $content));
					$content = str_replace("'","\\'", $content);
					//error_log($board_align);
					$data = $this->renderContent($gameid, $content, $css, $pgn,
						$message, $template,$owner,$diagram_only,$show_header,
						$piece_size,$movetext_align,$moves_format,$navbar_enabled);
					return $data;
				} else {
					error_log("atts == null");
					return 'game not found';
				}
			}
			return '';
		}
	}
}
?>
