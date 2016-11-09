<?php
use Rsu\Validator\Validator;

/**
 * Author: Raymond Usbal
 * Date: 08/11/2016
 */
class ValidatorTest extends PHPUnit_Framework_TestCase {
	protected $obj = null;

	function testValidateGoodInput()
	{
		$this->obj = new Validator(
			[ 'E-mail' => 'required' ],
			[ 'E-mail' => 'raymond@philippinedev.com' ]
		);
		$this->assertTrue($this->obj->success());
	}

	function itestValidateBadInput()
	{
		$this->obj = new Validator(
			[ 'E-mail' => 'required' ],
			[ 'E-mail' => '' ]
		);
		$this->assertFalse($this->obj->success());
	}

	function testIfnotsetWithGoodInput()
	{
		$this->obj = new Validator(
			[
				'liefeVorname' => 'required|ifnotset:sameAsBilling',
			],
			[
				'sameAsBilling'	=> 1,
				'liefeVorname' => 'TestValue'
			]
		);
		$this->assertTrue($this->obj->success());
	}

	function testIfnotsetIsRequiredButNotGiven()
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

	function testSetErrorMessages()
	{
		$this->obj = new Validator(
			[ 'name' => 'required' ],
			[ 'name' => '' ]
		);
		$expected = 'Name is required.';
		$this->assertContains($expected, $this->obj->error('name'));
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
		$expect = 'Name is required.';
		$this->assertContains($expect, $this->obj->error('name'));
	}

	function testSetNoErrorBecauseIfnotsetConditionIsNotMet()
	{
		$this->obj = new Validator(
			[ 'name' => 'required|ifnotset:sameAsBilling' ],
			[ 'sameAsBilling' => 1 ]
		);
		$this->assertNull($this->obj->error('name'));
	}
}
