jQuery( document ).ready( function( $ )
{
	$( "#mode" ).change( function( event )
	{
		var val = $( this ).val();

		var img_preview = $( '.formatting_preview' );
		
		img_preview.attr( 'src', img_preview.attr( 'src' ).replace( /images\/(\d+)\.png/g, 'images/' + val + '.png' ) );
	} );
} );