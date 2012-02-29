<?php

/**
 * @author Glen Scott <glen@glenscott.co.uk>
 */

define( 'CONFIG_PATH', '/tmp/php-functions.inc' );

$extensions          = get_loaded_extensions();
$extension_functions = array();
$all_functions       = array();

foreach ( $extensions as $ext ) {
    $functions = get_extension_funcs( $ext );

    if ( $functions ) {
        $all_functions = array_merge( $all_functions, $functions );
    }
}

echo "Found " . count( $all_functions ) . " total functions in " . count( $extensions ) . " extensions available in PHP.\n";

// write out config file
file_put_contents( CONFIG_PATH, serialize( $all_functions ) );
