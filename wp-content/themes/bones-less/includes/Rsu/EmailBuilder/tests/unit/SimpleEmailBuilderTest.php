<?php

namespace Rsu\EmailBuilder\tests\unit;

use Rsu\EmailBuilder\SimpleEmailBuilder;


/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */
class SimpleEmailBuilderTest extends \PHPUnit_Framework_TestCase {

	protected $obj = null;

	protected function setUp()
	{
		$this->obj = new SimpleEmailBuilder([
			'name' => 'Raymond Usbal',
			'email' => 'raymond@philippinedev.com',
			'sameAsBilling' => 1,
			'preisrahamen' => 1,
		]);
	}

	function testCanProcessHeaderWithLineBreak()
	{
		$expected = "<html><head><title>Ray</title></head><body><br></body></html>";
		$this->assertEquals($expected,
			$this->obj->header('Ray')
				->addLineBreak()
				->render()
		);
	}

	public function testCanProcessSectionTitle()
	{
		$expected = '<html><head></head><body><strong>Name</strong><br><br></body></html>';
		$this->assertEquals($expected, $this->obj->sectionTitle('Name')->render());
	}

	public function testCanProcessLine()
	{
		$expected = '<html><head></head><body><strong>Name:</strong> Raymond Usbal<br></body></html>';
		$this->assertEquals($expected, $this->obj->line('Name', 'name')->render());
	}

	public function testCanProcessLineWithCheckboxTrue()
	{
		$expected = '<html><head></head><body><strong>Same As Billing:</strong> Ja<br></body></html>';
		$this->assertEquals($expected,
			$this->obj->line('Same As Billing', [
				'sameAsBilling', 'Ja', 'Nein'
			])->render()
		);
	}

	public function testCanProcessLineWithCheckboxFalse()
	{
		$expected = '<html><head></head><body><strong>Same As Billing:</strong> Nein<br></body></html>';
		$this->assertEquals($expected,
			$this->obj->line('Same As Billing', [
				'notSameAsBilling', 'Ja', 'Nein'
			])->render()
		);
	}

	public function testCanAddLineBreaks()
	{
		$expected = '<html><head></head><body><br><br><br><br><br></body></html>';
		$this->assertEquals($expected,
			$this->obj->addLineBreak(5)->render()
		);
	}

	public function testLineWithConditionSatisfied()
	{
		$expected = '<html><head></head><body><strong>Preisrahamen:</strong> 1<br></body></html>';
		$this->assertEquals($expected,
			$this->obj->line('Preisrahamen', 'preisrahamen', ['!=' => 0])->render()
		);
	}

	public function testLineWithConditionNotSatisfied()
	{
		$this->expectException(\Exception::class);
		$this->obj->line('Preisrahamen', 'preisrahamen', ['==' => 0])->render();
	}

	public function testExceptionWhenNoBody()
	{
		$this->expectException(\Exception::class);
		$this->obj->render();
	}

	public function testSucceedsWhenMultipleConditionAllIsTrue()
	{
		$expected = '<html><head></head><body><strong>Preisrahamen:</strong> 1<br></body></html>';
		$this->assertEquals($expected,
			$this->obj->line('Preisrahamen', 'preisrahamen', [['>' => 0], ['==' => 1]])->render()
		);
	}

	public function testFailsWhenMultipleConditionOneIsFalse()
	{
		$this->expectException(\Exception::class);
		$this->obj->line('Preisrahamen', 'preisrahamen', [['==' => 1], ['<' => 0], ['==' => 1]])->render();
	}
}
