<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsPlugin.php
 *
 * Copyright (c) 2014-2026 Simon Fraser University
 * Copyright (c) 2003-2026 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AllowedUploadsPlugin
 * 
 * @ingroup plugins_generic_allowedUploads
 *
 * @brief Allowed Uploads plugin class
 */

namespace APP\plugins\generic\allowedUploads;

use APP\core\Application;
use PKP\core\APIRouter;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\VueModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class AllowedUploadsPlugin extends GenericPlugin
{
    private AllowedUploadsSettingsController $controller;

    /**
     * @copydoc Plugin::register()
     *
     * @param null|mixed $mainContextId
     */
	public function register($category, $path, $mainContextId = null)
	{
        $success = parent::register($category, $path, $mainContextId);
        if (Application::isUnderMaintenance()) {
            return true;
        }
        if ($success && $this->getEnabled($mainContextId)) {
			Hook::add('SubmissionFile::validate', $this->checkUploadWizard(...));
			Hook::add('submissionfilesuploadform::validate', $this->checkUpload(...));

            $this->controller = new AllowedUploadsSettingsController($this);

            Hook::add('APIHandler::endpoints::plugin', function (string $hookName, APIRouter $apiRouter): bool {
                $apiRouter->registerPluginApiControllers([
                    $this->controller,
                ]);
                return Hook::CONTINUE;
            });
        }
        return $success;
	}

    /**
     * @copydoc Plugin::getDisplayName()
     */
	public function getDisplayName()
	{
		return __('plugins.generic.allowedUploads.displayName');
	}

    /**
     * @copydoc Plugin::getDescription()
     */
	public function getDescription()
	{
		return __('plugins.generic.allowedUploads.description');
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	public function getActions($request, $verb)
	{
		$actions = parent::getActions($request, $verb);

		if (!$this->getEnabled()) {
			return $actions;
		}

		$context = $request->getContext();
		$apiUrl = $request->getDispatcher()->url(
			$request,
			Application::ROUTE_API,
			$context->getPath(),
			$this->controller->getHandlerPath()
		);

		$form = new AllowedUploadsForm($apiUrl);

		array_unshift($actions, new LinkAction(
			'settings',
			new VueModal(
				'PkpFormModal',
				[
					'title' => $this->getDisplayName(),
					'formConfig' => $form->getConfig(),
					'getApiUrl' => $apiUrl,
				]
			),
			__('manager.plugins.settings'),
			null
		));

		return $actions;
	}

	/**
     * Check the uploaded file in wizard
     *
     * @param string $hookName
     * @param array $params
     */
	public function checkUploadWizard($hookName, $params)
	{
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
     *
     * @param string $hookName
     * @param array $params
     */	
	public function checkUpload($hookName, $params)
	{
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

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\allowedUploads\AllowedUploadsPlugin', '\AllowedUploadsPlugin');
}