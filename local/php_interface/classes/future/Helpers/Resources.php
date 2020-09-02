<?php

namespace Future\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Page\Asset;

class Resources
{
    /**
     * @var self
     */
    private static $instance;
    /**
     * @var string|null
     */
    private $docRoot;
    /**
     * @var Asset
     */
    private $asset;

    private function __construct()
    {
        $this->docRoot = Application::getDocumentRoot();
        $this->asset = Asset::getInstance();
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function addCss(string $path): bool
    {
        if ($realPath = $this->getPath($path)) {
            return $this->asset->addCss($realPath);
        }

        return false;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function addJs(string $path): bool
    {
        if ($realPath = $this->getPath($path)) {
            return $this->asset->addJs($realPath);
        }

        return false;
    }

    /**
     * @param string $path
     * @return bool|string
     */
    public function getPath(string $path)
    {
        if (!(self::$instance instanceof self)) {
            return false;
        }

        foreach (glob($this->getDocRootPath() . $path) as $value) {
            return $this->removeDocRootPath($value);
        }

        return false;
    }

    /**
     * @return string|null
     */
    private function getDocRootPath()
    {
        return $this->docRoot;
    }

    /**
     * @param string $path
     * @return string
     */
    private function removeDocRootPath(string $path): string
    {
        return str_replace($this->getDocRootPath(), "", $path);
    }
}
