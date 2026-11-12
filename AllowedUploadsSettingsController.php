<?php

/**
 * @file plugins/generic/allowedUploads/AllowedUploadsSettingsController.php
 *
 * @class AllowedUploadsSettingsController
 *
 * @brief API controller for AllowedUploads plugin settings
 */

namespace APP\plugins\generic\allowedUploads;

use APP\plugins\generic\allowedUploads\formRequests\EditAllowedUploadsSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PKP\plugins\PluginSettingsController;

class AllowedUploadsSettingsController extends PluginSettingsController
{

    public function get(Request $illuminateRequest): JsonResponse
    {
        $contextId = $this->getRequest()->getContext()->getId();

        return response()->json(
            ['allowedExtensions' => $this->plugin->getSetting($contextId, 'allowedExtensions') ?? ''],
            Response::HTTP_OK
        );
    }

    public function edit(EditAllowedUploadsSettings $illuminateRequest): JsonResponse
    {
        $contextId = $this->getRequest()->getContext()->getId();
        $allowedExtensions = $illuminateRequest->validated()['allowedExtensions'];

        $this->plugin->updateSetting($contextId, 'allowedExtensions', $allowedExtensions);

        return response()->json(
            ['allowedExtensions' => $allowedExtensions],
            Response::HTTP_OK
        );
    }

}
