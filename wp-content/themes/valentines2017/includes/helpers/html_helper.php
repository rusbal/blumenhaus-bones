<?php
/**
 * Author: Raymond Usbal
 * Date: 07/11/2016
 */

use Snscripts\HtmlHelper\Helpers\Form;
use Snscripts\HtmlHelper\Html;
use Snscripts\HtmlHelper\Interfaces\BasicAssets;
use Snscripts\HtmlHelper\Interfaces\BasicFormData;
use Snscripts\HtmlHelper\Interfaces\BasicRouter;

function htmlHelper() {
	return new Html(
		new Form(
			new BasicFormData()
		),
		new BasicRouter(),
		new BasicAssets()
	);
}
