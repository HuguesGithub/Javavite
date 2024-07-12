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
        $errorPanel = '';
        $controller = new HomePageController();

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

}
JavasiteBase::display();
