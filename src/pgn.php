<?php
class Pgn {
	protected $vars = array();
	protected $tags = array(
		"White" => "White",
		"WhiteElo" => "WhiteElo",
		"Black"  => "Black",
		"BlackElo" => "BlackElo",
		"Result" => "Result",
		"ECO" => "ECO",
		"Event" => "Event",
		"EventDate" => "EventDate",
		"Round" => "Round",
		"Date" => "GameDate",
		"Site" => "Site",
		"Source" => "Source",
		"FEN" => "FEN"
	 );
	var $uid;
	var $gameType;
	var $valid;
	var $url;
	var $pgn;

	public function getTags() {
		return $this->tags;
	}

	public function getVars() {
		return $this->vars;
	}

	public function isTag($tag) {
		//error_log( 'checking: -' . $tag . '-');
		//error_log( 'checking: -' . $tag . '-: ' . isset($this->tags["'".$tag."'"]));
		return array_key_exists($tag,$this->tags);
	}

	// uid
	public function setUID($uid) {
		$this->uid = $uid;
	}
	public function getUID() {
		return $this->uid;
	}

	// url
	public function setUrl($url) {
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}
	// gametype
	public function setPGN($pgn) {
		$this->pgn = $pgn;
	}

	public function getPGN() {
		return $this->pgn;
	}
	// gametype
	public function setGameType($gameType) {
		$this->gameType = $gameType;
	}
	public function getGameType() {
		return $this->gameType;
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}

	public function __get($name) {
		return $this->vars[$name];
	}


	function __construct($gameType) {
		$this->gameType = $gameType;
		error_log("create PGN type: " . $this->gameType );
	}
}
?>
