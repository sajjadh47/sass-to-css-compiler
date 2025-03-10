jQuery( document ).ready( function( $ )
{
	$( "#mode" ).change( function( event )
	{
		let val 		= $( this ).val();

		let img_preview = $( '.formatting_preview' );
		
		img_preview.attr( 'src', img_preview.attr( 'src' ).replace( /images\/(\d+)\.png/g, `images/${val}.png` ) );
	} );
} );