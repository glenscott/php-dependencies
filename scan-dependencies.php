<?php

ini_set( 'memory_limit', '512M' );

require_once 'config.php';
require_once 'CodeDependency.php';

if (is_file(FUNCTION_CACHE_PATH)) {
  $all_functions = unserialize( file_get_contents( FUNCTION_CACHE_PATH ) );
  $function_extensions = unserialize( file_get_contents( EXTENSION_CACHE_PATH ) );
} else {
  print 'Run get-env-functions.php first'.PHP_EOL;
  exit;
}

$extensions = array();
$cd = new CodeDependency();
$cd->findDependenciesByDirectory( $all_functions, SOURCE_PATH );

echo "Found " . count( $cd->getFoundFunctions() ) . " function calls and " . count( $cd->getFoundClasses() ) . " object instantiations in your script.\n";

foreach( $cd->getFoundFunctions() as $func ) {
    if ( ! in_array( $func, $all_functions ) &&
         ! in_array( $func, $cd->getDefinedCustomFuncs() ) ) {
        echo "Function $func not defined.\n";
    }
    if (isset($function_extensions[$func])) {
      $extensions[$func] = $function_extensions[$func];
    }
}
$required_extensions = array_unique(array_values($extensions));
print "Extensions required: ";
foreach ($required_extensions as $ext) {
  print $ext. ' ';
}
print PHP_EOL;


