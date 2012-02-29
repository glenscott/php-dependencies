<?php

ini_set( 'memory_limit', '512M' );

define( 'CONFIG_PATH', '/tmp/php-functions.inc' );
define( 'SOURCE_PATH', 'example/' );

require_once 'CodeDependency.php';

// read config file
$all_functions = unserialize( file_get_contents( CONFIG_PATH ) );

$cd = new CodeDependency();
$cd->findDependenciesByDirectory( $all_functions, SOURCE_PATH );

echo "Found " . count( $cd->getFoundFunctions() ) . " function calls and " . count( $cd->getFoundClasses() ) . " object instantiations in your script.\n";

foreach( $cd->getFoundFunctions() as $func ) {
    if ( ! in_array( $func, $all_functions ) &&
         ! in_array( $func, $cd->getDefinedCustomFuncs() ) ) {
        echo "Function $func not defined.\n";
    }
}
