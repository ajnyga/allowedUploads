{**
 * plugins/generic/allowedUploads/settingsForm.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Allowed Uploads plugin settings
 *
 *}
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#allowedUploadsSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="allowedUploadsSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="allowedUploadsSettingsFormNotification"}

	<div id="description">{translate key="plugins.generic.allowedUploads.manager.settings.description"}</div>

	{fbvFormArea id="allowedUploadsSettingsFormArea"}
		{fbvElement type="text" id="allowedExtensions" name="allowedExtensions" value=$allowedExtensions label="plugins.generic.allowedUploads.manager.settings.allowedExtensions"}
	{/fbvFormArea}

	{fbvFormButtons}

	<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</form>
