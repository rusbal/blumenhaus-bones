<?php
use Rsu\Validator\Validator;

/**
 * Author: Raymond Usbal
 * Date: 08/11/2016
 */
class ValidatorTest extends PHPUnit_Framework_TestCase {
	protected $obj = null;

	function testRequiredAndSupplied()
	{
		$this->obj = new Validator(
			[ 'E-mail' => 'required' ],
			[ 'E-mail' => 'raymond@philippinedev.com' ]
		);
		$this->assertTrue($this->obj->success());
	}

	function testRequiredButBlank()
	{
		$this->obj = new Validator(
			[ 'E-mail' => 'required' ],
			[ 'E-mail' => '' ]
		);
		$this->assertFalse($this->obj->success());
	}

    function testSetErrorMessages()
    {
        $this->obj = new Validator(
            [ 'name' => 'required' ],
            [ 'name' => '' ]
        );
        $expected = 'Name ' . Validator::LANG_IS_REQUIRED;
        $this->assertContains($expected, $this->obj->error('name'));
    }

	function testIfnotsetWithGoodInput()
	{
		$this->obj = new Validator(
			[
				'liefeVorname' => 'required|ifnotset:sameAsBilling',
			],
			[
				'liefeVorname' => 'TestValue'
			]
		);
		$this->assertTrue($this->obj->success());
	}

	function testSetNoErrorMessages()
	{
		$this->obj = new Validator(
			[ 'name' => 'required' ],
			[ 'name' => 'Raymond' ]
		);
		$this->assertNull($this->obj->error('name'));
	}

	function testSetErrorBecauseIfnotsetConditionIsMet()
	{
		$this->obj = new Validator(
			[ 'name' => 'required|ifnotset:sameAsBilling' ], []
		);
		$expect = 'Name ' . Validator::LANG_IS_REQUIRED;
		$this->assertContains($expect, $this->obj->error('name'));
	}

    function testSetErrorBecauseIfsetConditionIsMet()
    {
        $this->obj = new Validator(
            [ 'name' => 'required|ifset:sameAsBilling' ],
            [ 'sameAsBilling' => 1 ]
        );
        $expected = 'Name ' . Validator::LANG_IS_REQUIRED;
        $this->assertContains($expected, $this->obj->error('name'));
    }

    function testSetNoErrorBecauseIfnotsetConditionIsNotMet()
    {
        $this->obj = new Validator(
            [ 'name' => 'required|ifset:sameAsBilling' ],
            []
        );
        $this->assertNull($this->obj->error('name'));
    }

    function testRequireIfeqAndSupplied()
    {
        $this->obj = new Validator(
            [ 'name' => 'required|ifeq:karte=Yes' ],
            [
                'name' => 'Raymond',
                'karte' => 'Yes',
            ]
        );
        $this->assertNull($this->obj->error('name'));
    }

    function testRequireIfeqButNotSupplied()
    {
        $this->obj = new Validator(
            [ 'name' => 'required|ifeq:karte=Yes' ],
            [ 'karte' => 'Yes' ]
        );
        $expected = 'Name ' . Validator::LANG_IS_REQUIRED;
        $this->assertContains($expected, $this->obj->error('name'));
    }

	function testRequireIfeqIsFalse()
	{
		$this->obj = new Validator(
			[ 'name' => 'required|ifeq:karte=Yes' ],
			[ 'karte' => 'No' ]
		);
		$this->assertNull($this->obj->error('name'));
	}
}
