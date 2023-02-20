<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsSettingsForm.inc.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AllowedUploadsSettingsForm
 * @ingroup plugins_generic_allowedUploads
 *
 * @brief Form for managers to modify Allowed Uploads plugin settings
 */

use PKP\form\Form;

class AllowedUploadsSettingsForm extends Form {

	/** @var int */
	var $_contextId;

	/** @var object */
	var $_plugin;

	/**
	 * Constructor
	 * @param $plugin AllowedUploadsPlugin
	 * @param $contextId int
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));

	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$this->_data = array(
			'allowedExtensions' => $this->_plugin->getSetting($this->_contextId, 'allowedExtensions'),
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('allowedExtensions'));
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save settings.
	 */
	function execute(...$functionArgs) {
		$this->_plugin->updateSetting($this->_contextId, 'allowedExtensions', $this->getData('allowedExtensions'), 'string');
		parent::execute(...$functionArgs);
	}

}

?>
