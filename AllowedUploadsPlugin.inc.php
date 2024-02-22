<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsPlugin.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AllowedUploadsPlugin
 * @ingroup plugins_generic_allowedUploads
 *
 * @brief Allowed Uploads plugin class
 */

use APP\core\Application;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\plugins\GenericPlugin;
use PKP\plugins\PluginRegistry;
use PKP\validation\ValidatorFactory;


class AllowedUploadsPlugin extends GenericPlugin {
	/**
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {

			HookRegistry::register('SubmissionFile::validate', array($this, 'checkUploadWizard'));
			HookRegistry::register('submissionfilesuploadform::validate', array($this, 'checkUpload'));
		}
		return $success;
	}

	/**
	 * Get the plugin display name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.allowedUploads.displayName');
	}

	/**
	 * Get the plugin description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.allowedUploads.description');
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
						$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
						$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $verb)
		);
	}

 	/**
	 * @copydoc Plugin::manage()
	 */
	function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$context = $request->getContext();

				$templateMgr = TemplateManager::getManager($request);
				$templateMgr->registerPlugin('function', 'plugin_url', array($this, 'smartyPluginUrl'));

				$this->import('AllowedUploadsSettingsForm');
				$form = new AllowedUploadsSettingsForm($this, $context->getId());

				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
						return new JSONMessage(true);
					}
				} else {
					$form->initData();
				}
				return new JSONMessage(true, $form->fetch($request));
		}
		return parent::manage($args, $request);
	}

	/**
	 * Check the uploaded file in wizard
	 */
	function checkUploadWizard($hookName, $params) {
		$props = $params[2];
		$locale = $params[4];

		if ($fileName = $props['name'][$locale]){
			$errors =& $params[0];
			$request = Application::get()->getRequest();
			$context = $request->getContext();
			$fileName = $props['name'][$locale];
			$tmp = explode('.',$fileName);
			$extension = strtolower(end($tmp));

			$allowedExtensions = $this->getSetting($context->getId(), 'allowedExtensions');

			if ($allowedExtensions){
				$allowedExtensionsArray = array_filter(array_map('trim', explode(';', $allowedExtensions )), 'strlen');
				if (!in_array($extension, $allowedExtensionsArray)){
					$errors[] = __('plugins.generic.allowedUploads.error', array('allowedExtensions' => $allowedExtensions));
				}
			}


		}
	}

	/**
	 * Check the uploaded file
	 */
	function checkUpload($hookName, $params) {
		$form = $params[0];
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		$userVars = $request->getUserVars();
		$fileName = $userVars['name'];
		$tmp = explode('.',$fileName);
		$extension = strtolower(end($tmp));

		$allowedExtensions = $this->getSetting($context->getId(), 'allowedExtensions');

		if ($allowedExtensions){

			$allowedExtensionsArray = array_filter(array_map('trim', explode(';', $allowedExtensions )), 'strlen');

			if (!in_array($extension, $allowedExtensionsArray)){
				$form->addError('allowedFileType', __('plugins.generic.allowedUploads.error', ['allowedExtensions' => $allowedExtensions]));
			}

		}
		return false;
	}


}
?>
