<?php
namespace FluidTYPO3\Flux\ViewHelpers\Grid;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 *****************************************************************/

use FluidTYPO3\Flux\ViewHelpers\AbstractFormViewHelper;

/**
 * Flexform Grid Row ViewHelper
 *
 * @package Flux
 * @subpackage ViewHelpers/Grid
 */
class RowViewHelper extends AbstractFormViewHelper {

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Optional name of this row - defaults to "row"', FALSE, 'row');
		$this->registerArgument('label', 'string', 'Optional label for this row - defaults to an LLL value (reported if it is missing)', FALSE, NULL);
		$this->registerArgument('variables', 'array', 'Freestyle variables which become assigned to the resulting Component - ' .
			'can then be read from that Component outside this Fluid template and in other templates using the Form object from this template', FALSE, array());
		$this->registerArgument('extensionName', 'string', 'If provided, enables overriding the extension context for this and all child nodes. The extension name is otherwise automatically detected from rendering context.');
	}

	/**
	 * Render method
	 * @return string
	 */
	public function render() {
		$name = ('row' === $this->arguments['name'] ? uniqid('row', TRUE) : $this->arguments['name']);
		$row = $this->getForm()->createContainer('Row', $name, $this->arguments['label']);
		$row->setExtensionName($this->getExtensionName());
		$row->setVariables($this->arguments['variables']);
		$container = $this->getContainer();
		$container->add($row);
		$this->setContainer($row);
		$this->renderChildren();
		$this->setContainer($container);
	}

}
