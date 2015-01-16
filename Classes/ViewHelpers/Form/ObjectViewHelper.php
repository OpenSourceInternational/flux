<?php
namespace FluidTYPO3\Flux\ViewHelpers\Form;
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

use FluidTYPO3\Flux\Form\Container\Object as ObjectComponent;
use FluidTYPO3\Flux\ViewHelpers\AbstractFormViewHelper;

/**
 * FlexForm field section object ViewHelper
 *
 * Use this inside flux:form.section to name and divide the fields
 * into individual objects that can be inserted into the section.
 *
 * @package Flux
 * @subpackage ViewHelpers/Form
 */
class ObjectViewHelper extends AbstractFormViewHelper {

	/**
	 * Initialize
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the section object, FlexForm XML-valid tag name string', TRUE);
		$this->registerArgument('label', 'string', 'Label for section object, can be LLL: value. Optional - if not specified, ' .
			'Flux tries to detect an LLL label named "flux.fluxFormId.objects.foobar" based on object name, in scope of ' .
			'extension rendering the Flux form.', FALSE, NULL);
		$this->registerArgument('variables', 'array', 'Freestyle variables which become assigned to the resulting Component - ' .
			'can then be read from that Component outside this Fluid template and in other templates using the Form object from this template', FALSE, array());
		$this->registerArgument('extensionName', 'string', 'If provided, enables overriding the extension context for this and all child nodes. The extension name is otherwise automatically detected from rendering context.');
	}

	/**
	 * Render method
	 * @return void
	 */
	public function render() {
		/** @var ObjectComponent $object */
		$object = $this->getForm()->createContainer('Object', $this->arguments['name'], $this->arguments['label']);
		$object->setExtensionName($this->getExtensionName());
		$object->setVariables($this->arguments['variables']);
		$container = $this->getContainer();
		$container->add($object);
		$this->setContainer($object);
		$this->renderChildren();
		$this->setContainer($container);
	}

}
