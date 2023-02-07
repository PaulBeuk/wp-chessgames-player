<?php
class ChessgamesTemplateView {

	protected $template_dir = 'templates/';
	protected $vars = array();
	protected $message;
	protected $diagramOnly = "false";
	protected $showHeader = "TRUE";
	protected $pieceSize = "40";
	protected $movesFormat = "default";
	protected $movetextAlign = "left";
	protected $navbarEnabled = "TRUE";

	public function setNavbarEnabled($navbarEnabled) {
		$this->navbarEnabled= $navbarEnabled;
	}
	public function setMovetextAlign($movetextAlign) {
		$this->movetextAlign = $movetextAlign;
	}
	public function setMovesFormat($movesFormat) {
		$this->movesFormat= $movesFormat;
	}
	public function setPieceSize($pieceSize) {
		$this->pieceSize = $pieceSize;
	}
	public function setShowHeader($showHeader) {
		$this->showHeader = $showHeader;
	}
	public function setDiagramOnly($diagramOnly) {
		$this->diagramOnly = $diagramOnly;
	}
	public function setMessage($message) {
		$this->message = $message;
	}
	public function getMessage() {
		return $this->message;
	}
	public function __construct($template_dir = null) {
		//error_log('message template dir: ' . $this->template_dir);
		if ($template_dir !== null) {
			// you should check here if this dir really exists
			$this->template_dir = $template_dir;
		}
	}

	public function render($template_file) {
		if (file_exists($this->template_dir.$template_file)) {
			include $this->template_dir.$template_file;
		} else {
			throw new Exception('no template file ' . $template_file . ' present in directory ' . $this->template_dir);
		}
	}

	public function __set($name, $value) {
		//error_log('setting: ' . $name . ': -> ' . $value);
		//error_log('setting: ' . $name );
		$this->vars[$name] = $value;
	}

	public function __get($name) {
		//error_log('getting: ' . $name);
		//error_log('getting: ' . $name . ': -> ' . $this->vars[$name]);
		return $this->vars[$name];
	}
}
?>
