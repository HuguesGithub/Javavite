<?php
namespace src\Exception;

class TemplateException extends \Exception
{
    public function __construct(string $tpl)
    {
        parent::__construct("Fichier $tpl introuvable.<br>Vérifier le chemin ou la présence.", 0);
    }
}
