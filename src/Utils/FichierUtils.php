<?php
namespace src\Utils;

class FichierUtils
{
    protected $filePath;
    protected $fileName;
    protected $handle;
    protected $rootDirectory;

    public function __construct(string $filePath, string $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;

        $this->rootDirectory = SessionUtils::fromServer('DOCUMENT_ROOT').'/'.PLUGIN_URL;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    protected function openFile(string $type): bool
    {
        $this->handle = fopen($this->getFullUrl(), $type);
        return $this->handle ? true : false;
    }

    protected function closeFile(): bool
    {
        if ($this->handle) {
            return fclose($this->handle);
        }
        return false;
    }

    protected function ecrireLigne(string $line=''): self
    {
        fwrite($this->handle, $line."\r\n");
        return $this;
    }

    public function doDelete(): self
    {
        unlink($this->getFullUrl());
        return $this;
    }

    public function getFullUrl(): string
    {
        return $this->rootDirectory.$this->filePath.$this->fileName;
    }

}
