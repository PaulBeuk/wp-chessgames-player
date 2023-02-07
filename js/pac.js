/*jshint unused:false*/
/*jshint maxcomplexity:50 */
function setPlayerName( color ) {
	'use strict';
	var node = window.document.getElementById( 'select'+color );
	var namePlayer  = node.options[node.selectedIndex].text;
	//window.console.log( 'set color: ' + color  + ': ' + namePlayer );
	var nameNode = window.document.getElementById( 'name'+color );
	nameNode.value = namePlayer;
}

function setPgnContainerPosition( idname , position , showHeader ) {
	'use strict';
	var pgnContainer = idname + '-container';
	var headerContainer = idname + '-diagram-header';
	if( position.toLowerCase() === 'bottom' ) {
		position = 'none';
	} else if( position.toLowerCase() === 'left' ) {
		position = 'left';
	} else if( position.toLowerCase() === 'right' ) {
		position = 'right';
	} else {
		position = 'left';
	}
	var tel = 0;
	var intervalId = window.setInterval( function () {
		var node = window.document.getElementById( pgnContainer );
		//window.console.log( pgnContainer + ' sset float to : ' + position + ' tel: ' + tel );
		tel += 1;
		if( node ) {
			node.style.cssFloat = position;
//			window.console.log( pgnContainer + ' set 2 node float to : ' + node.style.cssFloat );
			window.clearInterval(intervalId);
		}
	},100);
	if ( showHeader === 'TRUE' ) {
		var headerId = window.setInterval( function () {
			var node = window.document.getElementById( headerContainer );
			//window.console.log( showHeader + ' ' + headerId + ' nodename: ' +  headerContainer + ' set float to : ' + position + ' tel: ' + tel );
			tel += 1;
			if( node ) {
				node.style.cssFloat = position;
				//window.console.log( 'clear interval: ' + headerId + ' name: ' + headerContainer );
				window.clearInterval( headerId );
			}
		},100);
	}
}

function searchGame() {

	'use strict';
	var url = '?page=chessgames_games&search=true';

	window.console.log( 'url at beginning: ' + url );

	var pgnNode = window.document.getElementById('pgnId');
	if ( pgnNode ) {
		var pgn = pgnNode.value.trim();
		if ( pgn.length > 0 ) {
			url += '&pgn='+pgn;
		}
	}

	var idNode = window.document.getElementById('idId');
	if ( idNode ) {
		var id = idNode.value.trim();
		if ( id.length > 0 ) {
			url += '&id='+id;
		}
	}

	var whiteNode = window.document.getElementById('whiteId');
	if ( whiteNode ) {
		var white = whiteNode.value.trim();
		if ( white.length > 0 ) {
			url += '&white='+white;
		}
	}

	var blackNode = window.document.getElementById('blackId');
	if ( blackNode ) {
		var black = blackNode.value.trim();
		if ( black.length > 0 ) {
			url += '&black='+black;
		}
	}
	window.console.log( 'url af end: ' + url );
	window.location = url;
}

function setMovesContainerHeight( idname , pieceSize , diagramOnly ) {
	'use strict';
	//window.console.log('diagramOnly == ' + diagramOnly );
	var elementId = idname + '-moves';
	if ( diagramOnly !== 'false' ) {
		window.console.log('diagramOnly == , ' + diagramOnly + ' ignore');
		return;
	}

	//window.console.log( diagramOnly + ' set piece size: ' + elementId + ' pieceSize: ' + pieceSize );
	var movesHeightIntervalId = window.setInterval( function () {
		var node = window.document.getElementById( elementId );
		window.console.log( 'setHeight: ' + elementId );
		if( node ) {

			if( pieceSize === '20' ) {
				node.style.height = '200px';
			} else if( pieceSize === '24' ) {
				node.style.height = '230px';
			} else if( pieceSize === '29' ) {
				node.style.height = '300px';
			} else if( pieceSize === '35' ) {
				node.style.height = '320px';
			} else if( pieceSize === '40' ) {
				node.style.height = '360px';
			} else if( pieceSize === '46' ) {
				node.style.height = '400px';
			} else {
				node.style.height = '360px';

			}
			window.clearInterval(movesHeightIntervalId);
		}
	},100);
}

function setPgnNavButtons( idname , enabled ) {
	'use strict';
	if( enabled.toLowerCase() === 'true') {
		return;
	}
	var elementId = idname + '-ct-nav-container';

	window.console.log( 'disable: it ' + elementId + ' enable: ' + enabled );
	var navIntervalId = window.setInterval( function () {
		var node = window.document.getElementById( elementId );
		window.console.log( 'disable: ' + elementId );
		if( node ) {
			node.style.display = 'none';
			window.console.log( 'disabled it now: ' + elementId );
			window.clearInterval(navIntervalId);
		}
	},100);
}
