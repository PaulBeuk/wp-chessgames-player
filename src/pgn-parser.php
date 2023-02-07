<?php
class PgnParser {
	const DELIMITER = "\n";
	const TAGDELIMITER = " ";
	const PGNDELIMITER = "[Event ";

	function __construct() {
//		require_once realpath(dirname(__FILE__).'/..').'/pgn.php';
		require_once 'pgn.php';
	}

	public function parseFromFile( $filename = null ) {
		error_log( "filename " . $filename );
		$content = file_get_contents($filename);
		return $content;
	}

	public function parse( $pgn = null , $lines ) {
		if ( count( $lines ) == 0) {
			error_log("ready parsing lines");
			if ( ! is_null( $pgn ) ) {
				error_log('new event found, store to db' );
				return $pgn;
			}
		}

		$line = trim(array_shift($lines));
		if ( ! empty($line) ) {
			if (substr($line, 0, 1) === '[') {
				$tag = substr($line, 1 , strlen($line) - 1);
				$tagLen = strpos( $tag , self::TAGDELIMITER );
				$tagName = substr( $tag , 0 , $tagLen );


				if ( $tagName == 'Event' ) {
					if ( is_null( $pgn ) ) {
						$pgn = new Pgn("PGN");
					} else {
						error_log( 'new event found, store to db' );
					}
				}

				$tagValue = substr( $tag , $tagLen + 1 , strlen($tag) - 1 );
				$tagValue = stripcslashes( $tagValue );
				$tagValue = substr( $tagValue , 1 , strlen($tagValue) );
				$tagValue = substr( $tagValue , 0 , strlen($tagValue) -2 );
				error_log( "tagName: " . $tagName . " val: --" . $tagValue."--" );
				$tagValue = str_replace("'","\\'", $tagValue);
				$pgn->$tagName = $tagValue;
			} else {
/*
				if ( $pgn->getMovetext() != null )
					$line = " " . $line;
				$pgn->setMovetext( $pgn->getMovetext() . $line );
*/
			}
		}

		return $this->parse( $pgn , $lines );
	}

	function contentToPgns( $content ) {
		$tmpPgns = explode(self::PGNDELIMITER, trim( $content ) );
		error_log("#pgns " . count($tmpPgns));
		$tel = 0;
		foreach( $tmpPgns as $part ) {
			$part = trim($part);
			if ( strlen( $part ) != 0 ) {
				$pgns[$tel++] = '[Event ' . $part;
				//echo 'pgn---- : ' . $part . "\n\n\n\n\n";
			}
		}
		return $pgns;
	}

	function contentToLines( $content ) {
		$lines = explode(self::DELIMITER, $content);
		error_log("#lines " . count($lines));
		return $lines;
	}
}
?>
