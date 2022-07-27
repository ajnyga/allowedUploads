<?php

/**
 * @defgroup plugins_generic_allowedUploads Allowed Uploads Plugin
 */

/**
 * @file plugins/generic/allowedUploads/index.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_allowedUploads
 * @brief Wrapper for Allowed Uploads plugin.
 *
 */

require_once('AllowedUploadsPlugin.inc.php');

return new AllowedUploadsPlugin();

?>
