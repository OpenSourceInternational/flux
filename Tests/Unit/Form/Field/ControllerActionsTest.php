<?php
namespace FluidTYPO3\Flux\Form\Field;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Claus Due <claus@namelesscoder.net>
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
 * ************************************************************* */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Flux
 */
class ControllerActionsTest extends AbstractFieldTest {

	/**
	 * @var array
	 */
	protected $chainProperties = array(
		'label' => 'Test field',
		'enable' => TRUE,
		'extensionName' => 'FluidTYPO3.Flux',
		'pluginName' => 'API',
		'controllerName' => 'Flux',
		'actions' => array(),
		'disableLocalLanguageLabels' => FALSE,
		'excludeActions' => array(),
		'localLanguageFileRelativePath' => '/Resources/Private/Language/locallang.xml',
		'prefixOnRequiredArguments' => '*',
		'subActions' => array()
	);

	/**
	 * @test
	 */
	public function canUseRawItems() {
		$component = $this->createInstance();
		$items = array(
			array('foo' => 'Foo'),
			array('bar' => 'Bar')
		);
		$component->setItems($items);
		$this->assertSame($items, $component->getItems());
	}

	/**
	 * @test
	 */
	public function canSetAndGetSeparator() {
		$component = $this->createInstance();
		$separator = ' :: ';
		$component->setSeparator($separator);
		$this->assertSame($separator, $component->getSeparator());
	}

	/**
	 * @test
	 */
	public function acceptsNamespacedClasses() {
		$expectedClassName = 'FluidTYPO3\Flux\Controller\ContentController';
		$component = $this->createInstance();
		$component->setExtensionName('FluidTYPO3.Flux');
		$className = $this->callInaccessibleMethod($component, 'buildExpectedAndExistingControllerClassName', 'Content');
		$this->assertSame($expectedClassName, $className);
	}

	/**
	 * @test
	 */
	public function canGenerateLabelFromLanguageFile() {
		$extensionKey = 'flux';
		$pluginName = 'Test';
		$controllerName = 'Content';
		$actionName = 'fake';
		$localLanguageFileRelativePath = '/Resources/Private/Language/locallang.xml';
		$labelPath = strtolower($pluginName . '.' . $controllerName . '.' . $actionName);
		$expectedLabel = 'LLL:EXT:' . $extensionKey . $localLanguageFileRelativePath . ':' . $labelPath;
		$label = $this->buildLabelForControllerAndAction($controllerName, $actionName, $localLanguageFileRelativePath);
		$this->assertSame($expectedLabel, $label);
	}

	/**
	 * @test
	 */
	public function canGenerateLabelFromActionMethodAnnotation() {
		$controllerName = 'Content';
		$actionName = 'fake';
		$expectedLabel = 'Fake Action';
		$label = $this->buildLabelForControllerAndAction($controllerName, $actionName);
		$this->assertSame($expectedLabel, $label);
	}

	/**
	 * @test
	 */
	public function canGenerateDefaultLabelFromActionMethodWithoutHumanReadableAnnotation() {
		$controllerName = 'Content';
		$actionName = 'fakeWithoutDescription';
		$expectedLabel = $actionName . '->' . $controllerName;
		$label = $this->buildLabelForControllerAndAction($controllerName, $actionName);
		$this->assertSame($expectedLabel, $label);
	}

	/**
	 * @test
	 */
	public function generatesDefaultLabelForControllerActionsWhichDoNotExist() {
		$controllerName = 'Content';
		$actionName = 'fictionalaction';
		$expectedLabel = $actionName . '->' . $controllerName;
		$label = $this->buildLabelForControllerAndAction($controllerName, $actionName);
		$this->assertSame($expectedLabel, $label);
	}

	/**
	 * @test
	 */
	public function prefixesLabelForActionsWithRequiredArgumentsWhenLanguageLabelsDisabled() {
		$extensionName = 'FluidTYPO3.Flux';
		$pluginName = 'Test';
		$controllerName = 'Content';
		$actionName = 'fakeWithRequiredArgument';
		$component = $this->createInstance();
		$component->setExtensionName($extensionName);
		$component->setPluginName($pluginName);
		$component->setControllerName($controllerName);
		$component->setDisableLocalLanguageLabels(TRUE);
		$label = $this->callInaccessibleMethod($component, 'getLabelForControllerAction', $controllerName, $actionName);
		$prefixedLabel = $this->callInaccessibleMethod($component, 'prefixLabel', $controllerName, $actionName, $label);
		$this->assertStringStartsWith('*', $prefixedLabel);
		$this->assertNotSame($label, $prefixedLabel);
	}

	/**
	 * @test
	 */
	public function respectsExcludedActions() {
		$actions = array(
			'Content' => 'render,fake'
		);
		$excludedActions = array(
			'Content' => 'fake',
		);
		/** @var ControllerActions $component */
		$component = $this->createInstance();
		$component->setExcludeActions($excludedActions);
		$component->setActions($actions);
		$component->setExtensionName('FluidTYPO3.Flux');
		$items = $this->buildActions($component, FALSE);
		foreach ($items as $item) {
			$this->assertArrayNotHasKey('Content->fake', $item);
		}
	}

	/**
	 * @test
	 */
	public function skipsOtherControllersInActionsIfControllerSpecifiedInBothPropertyAndActions() {
		$actions = array(
			'Content' => 'fake',
			'Other' => 'fake'
		);
		class_alias('FluidTYPO3\Flux\Controller\ContentController', 'FluidTYPO3\Flux\Controller\OtherController');
		/** @var ControllerActions $component */
		$component = $this->createInstance();
		$component->setActions($actions);
		$component->setControllerName('Content');
		$component->setExtensionName('FluidTYPO3.Flux');
		$items = $this->buildActions($component, FALSE);
		foreach ($items as $item) {
			$this->assertArrayNotHasKey('Other->fake', $item);
		}
	}

	/**
	 * @test
	 */
	public function skipsActionsWhichDoNotHaveAssociatedControllerMethods() {
		$actions = array(
			'Content' => 'fake,doesNotExist'
		);
		/** @var ControllerActions $component */
		$component = $this->createInstance();
		$component->setActions($actions);
		$component->setControllerName('Content');
		$component->setExtensionName('FluidTYPO3.Flux');
		$items = $this->buildActions($component, FALSE);
		foreach ($items as $item) {
			$this->assertArrayNotHasKey('Other->doesNotExist', $item);
		}
	}

	/**
	 * @test
	 */
	public function supportsSubActions() {
		$actions = array(
			'Content' => 'fake'
		);
		$subActions = array(
			'Content' => array(
				'fake' => 'render'
			)
		);
		$expected = array(
			array('LLL:EXT:flux/Resources/Private/Language/locallang.xml:.content.fake', 'Content->fake;Content->render')
		);
		/** @var ControllerActions $component */
		$component = $this->createInstance();
		$component->setActions($actions);
		$component->setSubActions($subActions);
		$component->setExtensionName('FluidTYPO3.Flux');
		$component->setControllerName('Content');
		$items = $this->buildActions($component, FALSE);
		$this->assertSame($expected, $items);
	}

	/**
	 * @param ControllerActions $component
	 * @param boolean $useDefaults
	 * @return array
	 */
	protected function buildActions(ControllerActions $component, $useDefaults = TRUE) {
		$actions = $component->getActions();
		if (TRUE === $useDefaults) {
			$component->setExtensionName('FluidTYPO3.Flux');
			$component->setPluginName('Test');
			$component->setControllerName('Content');
			$component->setLocalLanguageFileRelativePath('/Resources/Private/Language/locallang.xml');
		}
		$items = $this->callInaccessibleMethod($component, 'buildItemsForActions', $actions);
		return $items;

	}

	/**
	 * @param string $controllerName
	 * @param string $actionName
	 * @param string $languageFileRelativeLocation
	 * @return string
	 */
	protected function buildLabelForControllerAndAction($controllerName, $actionName, $languageFileRelativeLocation = NULL) {
		$component = $this->createInstance();
		$component->setControllerName($controllerName);
		$component->setExtensionName('FluidTYPO3.Flux');
		$component->setPluginName('Test');
		if (NULL !== $languageFileRelativeLocation) {
			$component->setLocalLanguageFileRelativePath($languageFileRelativeLocation);
		} else {
			$component->setDisableLocalLanguageLabels(TRUE);
		}
		$label = $this->callInaccessibleMethod($component, 'getLabelForControllerAction', $controllerName, $actionName);
		return $label;
	}

}