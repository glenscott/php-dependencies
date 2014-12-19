# Compare PHP environment against PHP source code for function dependencies.

A recent post on reddit/PHP asked an interesting question: how can you determine the extension requirements of your PHP application in a programmatic way? And could you add such a check to a continuous integration environment to validate dependencies?

I've come up with a relative simple solution, albeit with some caveats (see below). The process requires two distinct stages:

1. Gather a list of extensions and functions from a PHP environment
2. Scan PHP source code for function calls and flag up any that are not either a) available in the PHP environment or b) user defined.

For stage 1, you must determine the functions that are defined in your PHP environment. This is done by running `get-env-functions.php` either on the command line, or from within your document root. This will create a config file in the directory defined by `CONFIG_PATH`. This config file will be used by the second script, `scan-dependencies.php`.

`scan-dependencies.php` will scan through PHP source code defined by `SOURCE_PATH` and use the configuration file generated previously. After it finishes scanning, it will list all function calls made that are not defined in either PHP itself, or within the source directory.

## Example Run

Create a configuration file

	$ cp config_sample.php config.php

Getting details of your PHP environment

	$ php get-env-functions.php 
	Found 1743 total functions in 61 extensions available in PHP.
	
Scanning source code for dependencies

	$ php scan-dependencies.php 
	Found 3 function calls and 1 object instantiations in your script.
	Function ps_setgray not defined.
	Extensions required: standard 

In this example, the function `ps_setgray` was called in a script but not defined anywhere.

## Caveats

* Your source code and its dependencies must lie under one directory -- included/required files outside this directory are not scanned
* As it stands, only _function_ dependencies are found. This means that class dependencies are not checked.
Final thoughts

This is by no means a complete solution, but I hope it is of some use. Please feel free to comment, or suggest improvements.
