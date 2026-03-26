<?php

/**
 * @file plugins/generic/allowedUploads/formRequests/EditAllowedUploadsSettings.php
 *
 * Copyright (c) 2014-2026 Simon Fraser University
 * Copyright (c) 2003-2026 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EditAllowedUploadsSettings
 *
 * @ingroup plugins_generic_allowedUploads
 *
 * @brief Handle validation for updating AllowedUploads plugin settings
 */

namespace APP\plugins\generic\allowedUploads\formRequests;

use Illuminate\Foundation\Http\FormRequest;

class EditAllowedUploadsSettings extends FormRequest
{
    public function rules(): array
    {
        return [
            'allowedExtensions' => [
                'required',
                'string',
                'regex:/^\s*[a-zA-Z0-9]+(\s*;\s*[a-zA-Z0-9]+)*\s*$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'allowedExtensions.regex' => __('plugins.generic.allowedUploads.manager.settings.validationError'),
        ];
    }
}
