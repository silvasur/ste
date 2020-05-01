<?php

namespace kch42\ste;

/**
 * A StorageAccess implementation is used to access the templates from any storage.
 * This means, that you are not limited to store the Templates inside directories, you can also use a database or something else.
 */
interface StorageAccess
{
    /** @var int The template's source */
    const MODE_SOURCE        = 0;

    /** @var int The compiled template */
    const MODE_TRANSCOMPILED = 1;

    /**
     * Loading a template.
     *
     * @param string $tpl The name of the template.
     * @param string &$mode Which mode is preferred? One of the MODE_* constants.
     *                      If {@see StorageAccess::MODE_SOURCE}, the raw sourcecode is expected,
     *                      if {@see StorageAccess::MODE_TRANSCOMPILED} the compiled template
     *                      *as a callable function* (expecting an {@see STECore} instance as first parameter) is expected.
     *
     *                      If the compiled version is not available or older than the source, you can set this
     *                      parameter to {@see StorageAccess::MODE_SOURCE} and return the source.
     *
     * @throws CantLoadTemplate If the template could not be loaded.
     *
     * @return string|callable Either the sourcecode or a callable function (first, and only parameter: an {@see STECore} instance).
     */
    public function load($tpl, &$mode);

    /**
     * Saves a template.
     *
     * @param string $tpl -The name of the template.
     * @param string $data - The data to be saved.
     * @param int $mode - One of the MODE_* constants.
     *
     * @throws CantSaveTemplate If the template could not be saved.
     */
    public function save($tpl, $data, $mode);
}
