<?php

namespace Rsu\EmailBuilder;

use Rsu\ContactForm\DbWriter\LoggerInterface;

/**
 * Class SimpleEmailBuilder
 * @package Rsu\EmailBuilder
 */
class SimpleEmailBuilder
{
	protected $head = '';
	protected $body = [];
    protected $data = [];
	protected $post;
	protected $logger;

    /**
     * SimpleEmailBuilder constructor.
     * @param $post
     * @param LoggerInterface|null $logger
     */
    public function __construct($post, LoggerInterface $logger = null) {
		$this->post = $post; // $this->post or any array
        $this->logger = $logger;
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
	public function line($caption, $data, $condition = [], $dataOptions = [])
	{
		$message = $this->lineBuilder($caption, $data, $condition);

		if ($message) {
            $value = $this->getValue($data);

            /**
             * For body (email)
             */
            if (isset($dataOptions['valueWhenNotNumeric']) && !is_numeric($value)) {
                $message .= '<i>' . $dataOptions['valueWhenNotNumeric'] . '</i>';
            }
            $this->body[] = $message;

            /**
             * For data (DB log)
             */
            if (isset($dataOptions['captionPrefix'])) {
                $caption = $dataOptions['captionPrefix'] . ' ' . $caption;
            }

            if (isset($dataOptions['valuePrefix'])) {
                $value = $dataOptions['valuePrefix'] . ' ' . $value;
            }

            $this->data[] = [ $caption => $value ];
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
		if ($this->logger) {
		    $this->logger->log($this->data);
        }
		return '<html>'
			. '<head>' . $this->head . '</head>'
		    . '<body>' . implode("\n", $this->body) . '</body>'
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
		$value = $this->getValue($data);

		if ($this->failedCheck($value, $condition)) {
			return null;
		}

		if ($value) {
			return "<strong>$caption:</strong> " . $value . '<br>';
		}

		return null;
	}

	private function getValue($data)
    {
        if (is_array($data)) {
            list($name, $trueValue, $falseValue) = $data;
            return isset($this->post[$name]) ? $trueValue : $falseValue;
        }

        if ( isset( $this->post[$data] ) ) {
            return $this->post[$data];
        }
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