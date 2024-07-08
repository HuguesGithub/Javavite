<?php
namespace src\Controller;

use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\LogFile;
use src\Utils\FichierUtils;
use src\Utils\RepertoireUtils;
use src\Utils\SessionUtils;

class HomePageController extends UtilitiesController
{
    private string $targetDirectory;

    public function __construct()
    {
        parent::__construct();
        $this->title = 'Home';
        $this->targetDirectory = TemplateConstant::LOGS_PATH;
    }

    public function getContentPage(string $msgProcessError=''): string
    {
        $arrLignesNonTraitees = [];
        $logSelection = '';
        $blnOk = false;

        if (SessionUtils::isPostSubmitted()) {
            $logSelection = SessionUtils::fromPost('logSelection');
        } else {
        	$logSelection = SessionUtils::fromGet('logSelection');
        }
        
        if ($logSelection!='') {
            $dirUtils = new RepertoireUtils($this->targetDirectory);
            $files = $dirUtils->recupererFichiers()->getFiles();
            while ($files->valid()) {
                $file = $files->current();
                $fileName = $file->getFileName();
                if ($fileName==$logSelection) {
                    $blnOk = true;
                }
                $files->next();
            }
        }
        
        if ($blnOk) {
            $objLogFile = new LogFile($this->targetDirectory.$logSelection);
            $arrLignesNonTraitees = $objLogFile->parse();
            $content = $objLogFile->display();
        } else {
            $content = $this->getListing();
        }

        $attributes = [
            $msgProcessError=='' ? 'd-none' : '',
            $msgProcessError,
            $content,
            !empty($arrLignesNonTraitees) ? '<ul><li>'.implode('</li><li>', $arrLignesNonTraitees).'</li></ul>' : ''
        ];
        return $this->getRender(TemplateConstant::TPL_DASHBOARD_PANEL, $attributes);
    }

    private function getListing(): string
    {
        $dirUtils = new RepertoireUtils($this->targetDirectory);
        $files = $dirUtils->recupererFichiers()->getFiles();
        $str  = '<form method="post" action= "/">';
        $str .= '<section class="file-panel col-12 col-lg-4 offset-lg-4 my-3">';
        $cpt = 0;

        while ($files->valid()) {
            $file = $files->current();

            $fileName = $file->getFileName();
            $str .= '
            <div class="input-group">
              <div class="input-group-text">
                <input class="form-check-input mt-0" type="radio" value="'.$fileName.'" name="logSelection"'.($cpt==0 ? ' checked="checked"' : '').'>
              </div>
              <span class="form-control">'.substr($fileName, 0, -4).'</span>
            </div>        ';

            $cpt ++;
            $files->next();
        }

        $str .= '<button class="form-control mt-3 bg-info" type="submit" value="">Analyser</button>';
        $str .= '</form>';
        $str .= '</section>';

        $str .= '<section class="file-panel col-12 col-lg-4 offset-lg-4 my-3">';
        $str .= 'Reste à faire :<ul>';
        $str .= '<li>Global<ul>';
        $str .= '<li>Gérer les abandons : Blocage</li>';
        $str .= '<li>Gérer la rétrogradation 3 rapports</li>';
        $str .= '<li>Gérer l\'usage de pneus en cas de Blocage</li>';
        $str .= '<li>Gérer les freins lors Blocage</li>';
        $str .= '</ul><li>Divers<ul>';
        $str .= '<li>Pouvoir imprimer en PDF le compte-rendu</li>';
        $str .= '<li>Pouvoir uploader un fichier de log</li>';
        $str .= '</ul></ul></section>';

        $str .= '<section class="file-panel col-12 col-lg-4 offset-lg-4 my-3">';
        $str .= '<h5>Change log v 0.1</h5><ul>';
        $str .= '<li>Mettre à jour la position de départ du pilote qui hoste</li>';
        $str .= '<li>Décompter les freins lors d\'aspirations</li>';
        $str .= '<li>Gérer les abandons : Carrosserie, Moteur, Pneus</li>';
        $str .= '<li>Gérer les annulations de frein</li>';
        $str .= '<li>Gérer les tête à queue</li>';
        $str .= '<li>Ne pas tenir compte des déplacements lors des arrêts rapides</li>';
        $str .= '<li>Gérer les aspirations</li>';
        $str .= '<li>Ne pas tenir compte des déplacements lors des aspirations</li>';
        $str .= '<li>Traiter les panneaux individuels</li>';
        $str .= '</ul></section>';

        return $str;
    }

}