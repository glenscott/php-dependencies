<?php

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'CodeDependency.php';

class CodeDependencyTest extends PHPUnit_Framework_TestCase {
    protected $code_dependency;

    protected function setUp() {
        $this->code_dependency = new CodeDependency();
    }

    public function testObjectInstantiation() {
        $this->assertTrue( is_a( $this->code_dependency, 'CodeDependency' ) );
        $this->assertInternalType( 'array', $this->code_dependency->getFoundFunctions() );
        $this->assertInternalType( 'array', $this->code_dependency->getDefinedCustomFuncs() );
        $this->assertInternalType( 'array', $this->code_dependency->getFoundClasses() );
    }
}
