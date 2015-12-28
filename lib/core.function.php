<?php

function uses( $m )
{
	load( 'lib/' . basename($m)  );
}

function load( $file_path ) 
{
	$file = AROOT . $file_path;
	if( file_exists( $file ) )
	{
		//echo $file;
		require( $file );
	
	}
	else
	{
		//echo CROOT . $file_path;
		require( CROOT . $file_path );
	}
	
}

// ===========================================
// load db functions
// ===========================================

include_once( CROOT .  'lib/db.function.php' );

if (!function_exists('_'))
{
	function _( $string , $data = null )
	{
			if( !is_array( $data ) ) $data = array( $data );
			return vsprintf( $to , $data );

	}
}