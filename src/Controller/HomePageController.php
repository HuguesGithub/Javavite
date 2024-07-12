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
        $cpt = 0;

        $content = '<ul class="list-group">';
        while ($files->valid()) {
            $file = $files->current();

            $fileName = $file->getFileName();
            $content .= '<li class="list-group-item">
                <input id="logSelection'.$cpt.'" class="form-check-input me-1" type="radio" value="'.$fileName.'" name="logSelection"'.($cpt==0 ? ' checked="checked"' : '').'>
                <label for="logSelection'.$cpt.'" class="form-check-label">'.substr($fileName, 0, -4).'</label>
                </li>';
            $cpt ++;
            $files->next();
        }
        $content .= '</ul>';
        return $this->getRender(TemplateConstant::TPL_CHANGELOG, [$content]);
    }

}
