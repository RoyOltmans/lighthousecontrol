	//<![CDATA[
	// Bind to "mobileinit" before you load jquery.mobile.js
	$( document ).on( "mobileinit", function() {
		$.mobile.listview.prototype.options.autodividersSelector = function( elt ) {
			var text = $.trim( elt.text() ) || null;
			if ( !text ) {
				return null;
			}
			if ( !isNaN(parseFloat(text)) ) {
				return "0-9";
			} else {
				text = text.slice( 0, 1 ).toUpperCase();
				return text;
			}
		};
	});
	//]]>