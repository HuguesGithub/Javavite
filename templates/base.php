<?php
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\HomePageController;
use src\Entity\LogFile;
use src\Utils\SessionUtils;

if (strpos(PLUGIN_PATH, 'wamp64')!==false) {
    define('JAVASITE_URL', 'http://localhost/');
} else {
    define('JAVASITE_URL', 'https://javavite.jhugues.fr/');
}
define('PLUGIN_URL', 'wp-content/plugins/hj-javavite/');
define('PLUGINS_JAVASITE', JAVASITE_URL.PLUGIN_URL);
date_default_timezone_set('Europe/Paris');

class JavasiteBase implements ConstantConstant, LabelConstant, TemplateConstant
{
    public static function display(): void
    {
        $msgProcessError = '';
        if (SessionUtils::isPostSubmitted()) {
            // Est-on en train de se connecter ?
            //static::processForm($msgProcessError);
        }
        $controller = new HomePageController();

        $errorPanel = '';
        if ($msgProcessError!='') {
            $errorPanel = $controller->getRender(TemplateConstant::TPL_SECTION_ERROR, [$msgProcessError]);
        }

        $attributes = [
            $controller->getTitle(),
            PLUGINS_JAVASITE,
            $controller->getContentHeader(),
            $controller->getContentPage($msgProcessError),
            $controller->getContentFooter(),
            $errorPanel,
        ];
        echo $controller->getRender(TemplateConstant::TPL_BASE, $attributes);
    }
    
    public static function processForm(string &$msgProcessError=''): void
    {
        $formName = SessionUtils::fromPost(ConstantConstant::CST_FORMNAME);
        if ($formName=='replayAnalysis') {
            // Transfert du fichier
            $targetDirectory = TemplateConstant::LOGS_PATH;
            $fileName = 'race_20240704_210446.log';

            $objLogFile = new LogFile($targetDirectory.$fileName);
            $objLogFile->parse();

            $objLogFile->display();
            /*
            $uploadFile = $_FILES["fileName"]["tmp_name"];
            $targetFile = $targetDirectory . basename($_FILES["fileName"]["name"]);
            if (is_uploaded_file($uploadFile)) {
                if (rename($uploadFile, $targetFile)) {
                    echo "Le fichier temporaire " . $_FILES["fileName"]["tmp_name"] . " a été déplacé vers " . $targetFile;
                } else {
                    echo "Le déplacement du fichier temporaire a échoué vérifiez l'existence du répertoire " . $targetDirectory;
                }
            }
            */
            // Fin Transfert du fichier
        }
    }

}
JavasiteBase::display();
