<?php
namespace src\Utils;

use src\Collection\Collection;

class RepertoireUtils
{
    protected $handle;
    protected $strChemin;
    protected $files;
    protected $rootDirectory;
    
    public function __construct(string $strChemin)
    {
        $this->strChemin = $strChemin;
        $this->files = new Collection();
        $this->rootDirectory = SessionUtils::fromServer('DOCUMENT_ROOT').'/'.PLUGIN_URL;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function purgerFichiers(): self
    {

        return $this;
    }

    public function recupererFichiers(): self
    {
        if ($this->openDir()) {
            $this->listerFichiers()
                ->closeDir();
        }
        return $this;
    }

    protected function listerFichiers(): self
    {
        while (false!==($file = readdir($this->handle))) {
            if ($file=='.' || $file=='..') {
                continue;
            }
            $objFile = new FichierUtils($this->strChemin, $file);
            $this->files->addItem($objFile);
        }
        return $this;
    }

    protected function openDir(): bool
    {
        $this->handle = opendir($this->rootDirectory.$this->strChemin);
        return $this->handle ? true : false;
    }

    protected function closeDir(): self
    {
        closedir($this->handle);
        return $this;
    }
}
