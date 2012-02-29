<?php

/**
 * Find function and class dependencies in PHP source code
 *
 * This class can determine all classes and functions used by one or more PHP scripts.  This is useful
 * to determine if scripts can be run in certain environments.
 *
 * @author Glen Scott <glen@glenscott.co.uk>
 */
class CodeDependency {
    const php_file_match = '/^.+\.php$/i';

    /**
     * record ALL function calls found
     *
     * @var array
     */
    private $found_functions;

    /**
     * record ALL function definitions found
     *
     * @var array
     */
    private $defined_custom_funcs;

    /**
     * record ALL classes instantiated
     *
     * @var array
     */
    private $found_classes;

    private $buffer_start;
    private $current_func;
    private $function_start;
    private $new_operator;
    private $method_call;

    public function __construct() {
        $this->found_functions = array();
        $this->defined_custom_funcs = array();
        $this->found_classes = array();
    }

    public function findDependenciesByDirectory( $all_functions, $dir ) {
        $Regex = new RegexIterator( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) ), 
                                    self::php_file_match, 
                                    RecursiveRegexIterator::GET_MATCH );

        foreach ( $Regex as $r ) {
            $this->buffer_start = false;
            $this->current_func = '';
            $this->function_start = false;
            $this->new_operator   = false;
            $this->method_call    = false;

            foreach ( token_get_all( file_get_contents( $r[0] ) ) as $token ) {
                if ( ! is_string( $token ) ) {
                    $this->processTokenArray( $token );
                }
                else {
                    $this->processTokenString( $token );
                }
            }
        }
    }

    public function getFoundFunctions() {
        return $this->found_functions;
    }

    public function getDefinedCustomFuncs() {
        return $this->defined_custom_funcs;
    }

    public function getFoundClasses() {
        return $this->found_classes;
    }

    private function normalise_function_name( $func ) {
        return strtolower( $func );
    }

    private function processTokenArray( $token ) {
        list ( $id, $text ) = $token;

        if ( $id == T_STRING ) {
            $this->buffer_start = true;
            $this->current_func = $text;

            if ( $this->function_start ) {
                $this->defined_custom_funcs[] = $this->normalise_function_name( $this->current_func );
                $this->function_start = false;
            }
        }
        elseif ( $id == T_FUNCTION ) {
            $this->function_start = true;
            $this->new_operator   = false;
        }
        elseif ( $id == T_NEW ) {
            $this->new_operator = true;
        }
        elseif ( $id == T_WHITESPACE ) {
            // ignore whitespace
        }
        elseif ( $id == T_OBJECT_OPERATOR ) {
            $this->method_call = true;
        }
        else {
            $this->buffer_start = false;
            $this->new_operator = false;
            $this->method_call  = false;
        }
    }

    private function processTokenString( $token ) {
        if ( $token == '(' ) {
            if ( $this->buffer_start ) {
                if ( ! $this->new_operator && ! $this->method_call ) {
                    // got function
                    if ( ! in_array( $this->normalise_function_name( $this->current_func ), $this->found_functions ) ) {
                        $this->found_functions[] = $this->normalise_function_name( $this->current_func );
                    }
                }
                else {
                    // got object instantiation
                    if ( ! in_array( $this->current_func , $this->found_classes ) ) {
                        $this->found_classes[] = $this->current_func;
                    }
                }
                
                $this->buffer_start = false;
            }
        }
        elseif ( $token == ')' ) {
            $this->buffer_start = false;
        }
    }
}
