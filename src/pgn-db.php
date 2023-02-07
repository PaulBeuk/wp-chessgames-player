<?php
if(!class_exists('PgnDB')) {
	class PgnDB {
		var $table;

		function __construct() {
			global $wpdb;
			require_once 'pgn.php';
			$this->table = $wpdb->prefix ."pac_chessgames";
			$this->user_table = $wpdb->prefix ."users";
		}

		public function updatePGN( $POST ) {
			global $wpdb;
			error_log("update pgn editid: " . $POST['editid'] );
			$pgnClass = new Pgn('PGN');
			$fields= "SET ";

			/* set tag fields */
			foreach($pgnClass->getTags() as $tag){
				//error_log( 'tag: ' . $tag . " -> " . $POST[$tag]);
				$fields .= $tag . " = '" .  $POST[$tag] ."', ";
			}

			/* handle white uid */
			$whiteId = absint($_POST['white_user']);
			if( $whiteId != -1 ) {
				$fields .= " wp_white_uid = '" .  $whiteId ."', ";
			}

			/* handle black uid */
			$blackId = absint($_POST['black_user']);
			if( $blackId != -1 ) {
				$fields .= " wp_black_uid = '" .  $blackId ."', ";
			}

			/* handle FEN */
			$FEN = $_POST['FEN'];
			if( trim($FEN) != false ) {
				error_log("fen is not false: -" . $FEN . "-" );
				$fields .= " SetUp = 1, ";
			}

			/* store original PGN */
			$fields .= " pgn = '" .  $POST['pgn'] ."'";

			$query = "UPDATE " . $this->table . " " . $fields . " WHERE id = " . $POST['editid'];
			//error_log("update query: " . $query );
			$wpdb->query( $query );
		}

		public function insertPgn( $pgn ) {
			global $wpdb;

			$fields = $values = "";
			foreach(array_keys($pgn->getVars()) as $key){
				if( ! $pgn->isTag( $key ) ) {
					error_log( 'unknown key: ' . $key );
				} else {
					$fields .= ($key == "Date" ? "GameDate" : $key) . ",";
					$values .= "'".$pgn->$key."',";
				}
			}
			$now = new DateTime();
			$fields .= "InsertDate";
			$values .= "'".$now->format('Y-m-d H:i:s')."'";
			$fields .= ",gameType";
			$values .= ",'".$pgn->getGameType()."'";
			$fields .= ",wp_uid";
			$values .= ",'".$pgn->getUID()."'";
			error_log("get url url url  query: " . $pgn->getUrl() );
			if( strlen( $pgn->getUrl() ) > 0 ) {
				error_log("get url url url url url  query: " . $pgn->getUrl() );
				$fields .= ",url";
				$values .= ",'".$pgn->getUrl()."'";
			}
			$fields .= ",pgn";
			$values .= ",'".$pgn->getPGN()."'";

			$query = "INSERT INTO " . $this->table . "(" . $fields . ") VALUES(" . $values . ");";
			//error_log("insert query: " . $query );
			$wpdb->query( $query );
		}

		function searchPgnsByContent($contentPart , $offset , $size) {
			global $wpdb;
			$whereClause = "pgn LIKE '%" . $contentPart . "%'";
			$limit = " limit " . $offset . "," . $size;
			$query = "SELECT * FROM " . $this->table . " WHERE " . $whereClause. " ORDER BY id DESC" . $limit;
			error_log("search query: " . $query );
			return $wpdb->get_results( $query );
		}

		function getPgnsByUID($uid , $offset , $size) {
			global $wpdb;
			$whereClause = "wp_uid = " . $uid . " OR wp_white_uid = " . $uid . " OR wp_black_uid = " . $uid;
			$limit = " limit " . $offset . "," . $size;
			$query = "SELECT * FROM " . $this->table . " WHERE " . $whereClause. " ORDER BY id DESC" . $limit;
			return $wpdb->get_results( $query );
		}

		function getGame($id) {
			global $wpdb;
			$query = "SELECT * FROM " . $this->table . " where id='".$id."'";
			return $wpdb->get_results( $query );
			// if(!empty($pgns[0])) {
			// 	error_log('looks like i found game by id: ' . id );
			// 	return $pgns[0];
			// } else {
			// 	error_log('no game found by id: ' . id );
			// }
		}

		public function get_all_users() {
			global $wpdb;
			//$query = "SELECT ID,display_name FROM " . $this->user_table;
			$query = "SELECT ID,display_name FROM " . $this->user_table . " ORDER BY display_name";
			return $wpdb->get_results( $query );
		}


		function deleteGame($id) {
			global $wpdb;
			$query = "DELETE FROM " . $this->table . " where id='".$id."'";
			return $wpdb->query( $query );
		}
	}
}
?>
