<?php

/**
 * @author Glen Scott <glen@glenscott.co.uk>
 */

require_once 'config.php';

$extensions          = get_loaded_extensions();
$function_extensions = array();
$all_functions       = array();

foreach ( $extensions as $ext ) {
    $functions = get_extension_funcs( $ext );

    if ( $functions ) {
        $all_functions = array_merge( $all_functions, $functions );
        foreach ( $functions as $function) {
          $function_extensions[ $function ] = $ext;
        }
    }
}

echo "Found " . count( $all_functions ) . " total functions in " . count( $extensions ) . " extensions available in PHP.\n";

file_put_contents( FUNCTION_CACHE_PATH, serialize( $all_functions ) );
file_put_contents( EXTENSION_CACHE_PATH, serialize( $function_extensions ) );
