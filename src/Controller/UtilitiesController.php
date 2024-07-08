<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;
use src\Exception\TemplateException;
use src\Utils\SessionUtils;

class UtilitiesController
{
    protected array $arrParams=[];
    protected string $title;
    protected string $breadCrumbsContent = '';

    public function __construct(array $arrUri=[])
    {
        if (isset($arrUri[2]) && !empty($arrUri[2])) {
            if (strpos($arrUri[2], '?')!==false) {
                $params = substr($arrUri[2], strpos($arrUri[2], '?')+1);
            } else {
                $params = $arrUri[2];
            }
            if (isset($arrUri[3]) && substr($arrUri[3], 0, 12)=='admin_manage') {
                $params .= '/'.$arrUri[3];
            }
            $arrParams = explode('&', $params);
            while (!empty($arrParams)) {
                $param = array_shift($arrParams);
                list($key, $value) = explode('=', $param);
                $this->arrParams[str_replace('amp;', '', $key)] = $value;
            }
        }
    }

    public function getArrParams(string $key): mixed
    {
        return $this->arrParams[$key] ?? '';
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setParams(array $params=[]): self
    {
        $this->arrParams = $params;
        return $this;
    }

    public function getRender(string $urlTemplate, array $args=[]): string
    {
        if (file_exists(PLUGIN_PATH.$urlTemplate)) {
            return vsprintf(file_get_contents(PLUGIN_PATH.$urlTemplate), $args);
        } else {
            throw new TemplateException($urlTemplate);
        }
    }

    public function getContentFooter()
    {
        return $this->getRender(TemplateConstant::TPL_FOOTER);
    }

    public function getContentHeader()
    {
        return '';
    }

    protected function addW100(): string
    {
        return '<div class="w-100"></div>';
    }

    public function getRow(array $params, bool $isTd=true, array $styles=[]): string
    {
        $tag = $isTd ? 'td' : 'th';
        $str = '<tr>';
        foreach ($params as $label) {
            if (!empty($styles)) {
                $style = array_shift($styles);
            } else {
                $style = '';
            }
            $str .= '<'.$tag.($style=='' ? '' : $style).'>'.$label.'</'.$tag.'>';
        }
        return $str . '</tr>';

    }

}
