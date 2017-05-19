<?php

namespace Craft;

class ElixirService extends BaseApplicationComponent
{
    /**
    * @var string
    */
    protected $manifest;

    /**
     * ElixirService constructor.
     */
    public function __construct()
    {
        $settings = craft()->plugins->getPlugin('elixir')->getSettings();
        $this->manifest = dirname(CRAFT_BASE_PATH) . $settings->manifestFile;
    }

    /**
     * Find the files version.
     *
     * @param $file
     * @return mixed
     */
    public function version($file)
    {
        try {
            $manifest = $this->readManifestFile();
        } catch (\Exception $e) {
            Craft::log(printf($e->getMessage()), LogLevel::Info, true);
            return $file;
        }

        // if no manifest, return the regular asset
        if (!$manifest) {
            return $file;
        }

        return $manifest[$file];
    }

    /**
     * Returns the assets version with the appropriate tag.
     *
     * @param $file
     * @return string
     */
    public function withTag($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        try {
            $manifest = $this->readManifestFile();
        } catch (\Exception $e) {
            Craft::log(printf($e->getMessage()), LogLevel::Info, true);
            return $file;
        }

        // if no manifest, return the regular asset
        if (!$manifest) {
            if ($extension == 'js') {
                return '<script src="' . $file . '"></script>';
            }

            return '<link rel="stylesheet" href="' . $file . '">';
        }

        if ($extension == 'js') {
            return '<script src="' . $manifest[$file] . '"></script>';
        }

        return '<link rel="stylesheet" href="' . $manifest[$file] . '">';
    }

    /**
     * Locate manifest and convert to an array.
     *
     * @return mixed
     */
    protected function readManifestFile()
    {
        if (file_exists($this->manifest)) {
            return json_decode(
                file_get_contents($this->manifest),
                true
            );
        }
        return false;
    }
}
