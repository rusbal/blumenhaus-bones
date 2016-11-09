<?php

namespace Rsu\EmailBuilder;

/**
 * Class SimpleEmailBuilder
 * @package Rsu\EmailBuilder
 */
class SimpleEmailBuilder
{
	protected $head = '';
	protected $body = [];
	protected $post;

	/**
	 * SimpleEmailBuilder constructor.
	 *
	 * @param $post
	 */
	public function __construct($post) {
		$this->post = $post; // $this->post or any array
	}

	/**
	 * @param $title
	 *
	 * @return $this
	 */
	public function header($title)
	{
		$this->head = "<title>$title</title>";
		return $this;
	}

	/**
	 * @param $title
	 *
	 * @return $this
	 */
	public function sectionTitle($title)
	{
		$this->body[] = "<strong>$title</strong>";
		$this->body[] = str_repeat("<br>", 2);
		return $this;
	}

	/**
	 * @param $caption
	 * @param $data
	 * @param array $condition
	 *
	 * @return $this
	 */
	public function line($caption, $data, $condition = [])
	{
		$message = $this->lineBuilder($caption, $data, $condition);

		if ($message) {
			$this->body[] = $message;
		}
		return $this;
	}

	/**
	 * @param int $n
	 *
	 * @return $this
	 */
	public function addLineBreak($n = 1)
	{
		$this->body[] = str_repeat("<br>", $n);
		return $this;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function render()
	{
		if (count($this->body) == 0) {
			throw new \Exception("No email content to render");
		}
		return '<html>'
			. '<head>' . $this->head . '</head>'
		    . '<body>' . implode($this->body) . '</body>'
			. '</html>';
	}

	/**
	 * Private functions
	 */

	/**
	 * @param string $caption
	 * @param mixed $data
	 * @param array $condition
	 *
	 * @return null|string
	 */
	private function lineBuilder($caption, $data, $condition = [])
	{
		$value = null;

		if (is_array($data)) {
			list($name, $trueValue, $falseValue) = $data;
			$value = isset($this->post[$name]) ? $trueValue : $falseValue;

		} elseif ( isset( $this->post[$data] ) ) {
			$value = $this->post[$data];
		}

		if ($this->failedCheck($value, $condition)) {
			return null;
		}

		if ($value) {
			return "<strong>$caption:</strong> " . $value . '<br>';
		}

		return null;
	}

	/**
	 * Negates check function.
	 * @param $value
	 * @param $condition
	 *
	 * @return bool
	 */
	private function failedCheck($value, $condition)
	{
		return ! $this->check($value, $condition);
	}

	/**
	 * @param mixed $value     User input
	 * @param mixed $condition Array single condition ['!=' => 0] or multiple [['>=' => 10], ['<=' => 20]] that must all evaluate to true.
	 *
	 * @return bool
	 */
	private function check($value, $condition)
	{
		foreach ($condition as $operator => $comp) {
			if (is_array($comp)) {
				$operator = key($comp);
				$compareValue = $comp[$operator];
			} else {
				$compareValue = $comp;
			}

			if (! eval("return ('$value' $operator '$compareValue');")) {
				return false;
			}
		}
		return true;
	}
}