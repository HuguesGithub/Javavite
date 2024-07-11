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
        $fileSelection = '';
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
                    $fileSelection = $fileName;
                    $blnOk = true;
                }
                $files->next();
            }
        }
        
        if ($blnOk) {
            $objLogFile = new LogFile($this->targetDirectory.$fileSelection);
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
        $cpt = 0;

        $content = '';
        while ($files->valid()) {
            $file = $files->current();

            $fileName = $file->getFileName();
            $content .= '
            <div class="input-group">
              <div class="input-group-text">
                <input class="form-check-input mt-0" type="radio" value="'.$fileName.'" name="logSelection"'.($cpt==0 ? ' checked="checked"' : '').'>
              </div>
              <span class="form-control">'.substr($fileName, 0, -4).'</span>
            </div>        ';

            $cpt ++;
            $files->next();
        }
        $content .= '<button class="form-control mt-3 bg-info" type="submit" value="">Analyser</button>';
        $content .= '</form>';
        $str .= $this->addSection([$content], 'file-panel col-12 col-lg-4 offset-lg-4 my-3');

        $content  = 'Reste à faire :<ul>';
        $content .= '<li>Global<ul>';
        $content .= '<li>Gérer les abandons : Blocage</li>';
        $content .= '<li>Gérer la rétrogradation 3 rapports</li>';
        $content .= '<li>Gérer l\'usage de pneus en cas de Blocage</li>';
        $content .= '<li>Gérer les freins lors Blocage</li>';
        $content .= '</ul><li>Divers<ul>';
        $content .= '<li>Pouvoir imprimer en PDF le compte-rendu</li>';
        $content .= '<li>Pouvoir uploader un fichier de log</li>';
        $content .= '</ul></ul>';
        $str .= $this->addSection([$content], 'file-panel col-12 col-lg-4 offset-lg-4 my-3');

        $content  = '<h5>Change log v 0.1</h5><ul>';
        $content .= '<li>Mettre à jour la position de départ du pilote qui hoste</li>';
        $content .= '<li>Décompter les freins lors d\'aspirations</li>';
        $content .= '<li>Gérer les abandons : Carrosserie, Moteur, Pneus</li>';
        $content .= '<li>Gérer les annulations de frein</li>';
        $content .= '<li>Gérer les tête à queue</li>';
        $content .= '<li>Ne pas tenir compte des déplacements lors des arrêts rapides</li>';
        $content .= '<li>Gérer les aspirations</li>';
        $content .= '<li>Ne pas tenir compte des déplacements lors des aspirations</li>';
        $content .= '<li>Traiter les panneaux individuels</li>';
        $content .= '</ul>';
        $str .= $this->addSection([$content], 'file-panel col-12 col-lg-4 offset-lg-4 my-3');

        return $str;
    }

}
