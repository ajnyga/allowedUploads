<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsSettingsForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AllowedUploadsSettingsForm
 * @ingroup plugins_generic_allowedUploads
 *
 * @brief Form for managers to modify Allowed Uploads plugin settings
 */

import('lib.pkp.classes.form.Form');

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

		parent::__construct($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
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
	function fetch($request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request);
	}

	/**
	 * Save settings.
	 */
	function execute() {
		$this->_plugin->updateSetting($this->_contextId, 'allowedExtensions', $this->getData('allowedExtensions'), 'string');
	}

}

?>
