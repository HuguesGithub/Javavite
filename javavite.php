<?php
use src\Controller\UtilitiesController;

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_PACKAGE', 'Javavite');
session_start([]);

/**
 * Plugin Name: HJ - Javavite
 * Description: Javavite
 * @author Hugues
 * @since 1.00.01.01
 */
class Javavite
{
    public function __construct()
    {
        add_filter('template_include', array($this,'templateLoader'));
    }

    public function templateLoader()
    {
        wp_enqueue_script('jquery');
        return PLUGIN_PATH.'templates/base.php';
    }
}
$objJavavite = new Javavite();

function exceptionHandler($objException)
{
    $strHandler  = '<div class="card border-danger" style="max-width: 100%;margin-right: 15px;">';
    $strHandler .= '  <div class="card-header bg-danger text-white"><strong>';
    $strHandler .= $objException->getMessage().'</strong></div>';
    $strHandler .= '  <div class="card-body text-danger">';
    $strHandler .= '    <p>Une erreur est survenue dans le fichier <strong>'.$objException->getFile();
    $strHandler .= '</strong> à la ligne <strong>'.$objException->getLine().'</strong>.</p>';
    $strHandler .= '    <ul class="list-group">';

    $arrTraces = $objException->getTrace();
    foreach ($arrTraces as $trace) {
        $strHandler .= '<li class="list-group-item">Fichier <strong>'.$trace['file'];
        $strHandler .= '</strong> ligne <em>'.$trace['line'].'</em> :<br>';
        if (isset($trace['args'])) {
            if (is_array($trace['args'])) {
                $strHandler .= $trace['function'].'()</li>';
            } else {
                $strHandler .= $trace['class'].$trace['type'].$trace['function'];
                $strHandler .= '('.implode(', ', $trace['args']).')</li>';
            }
        }
    }

    $strHandler .= '    </ul>';
    $strHandler .= '  </div>';
    $strHandler .= '  <div class="card-footer"></div>';
    $strHandler .= '</div>';

    echo $strHandler;
}
set_exception_handler('exceptionHandler');

spl_autoload_register(PLUGIN_PACKAGE.'Autoloader');
function javaviteAutoloader(string $classname)
{
    $pattern = "/(Collection|Constant|Controller|Entity|Enum|Exception|Form|Repository|Utils)/";
    preg_match($pattern, $classname, $matches);
    if (isset($matches[1])) {
        include_once PLUGIN_PATH.str_replace('\\', '/', $classname).'.php';
    }
}

function dealWithAjaxCallback()
{
    echo AjaxActions::dealWithAjax();
    die();
}
add_action('wp_ajax_dealWithAjax', 'dealWithAjaxCallback');
add_action('wp_ajax_nopriv_dealWithAjax', 'dealWithAjaxCallback');
