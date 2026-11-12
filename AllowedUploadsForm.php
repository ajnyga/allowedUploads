<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsForm.php
 *
 *
 * @brief Form component for AllowedUploads plugin settings
 */

namespace APP\plugins\generic\allowedUploads;

use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;

class AllowedUploadsForm extends FormComponent
{
    public $id = 'allowedUploadsSettings';
    public $method = 'PUT';

    public function __construct(string $action)
    {
        $this->action = $action;

        $this->addField(new FieldText('allowedExtensions', [
            'label' => __('plugins.generic.allowedUploads.manager.settings.allowedExtensions'),
            'description' => __('plugins.generic.allowedUploads.manager.settings.description'),
            'value' => '',
        ]));
    }
}
