<?php
namespace src\Controller;

use src\Collection\EventCollection;
use src\Constant\ConstantConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\BodyTest;
use src\Entity\EngineTest;
use src\Entity\MeteoTest;
use src\Entity\PitStopTest;
use src\Entity\StartTest;
use src\Entity\SuspensionTest;
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

    protected function addSection(array $elements, string $style): string
    {
        $content = '<section class="row '.$style.'">';
        foreach ($elements as $element) {
            $content .= $element;
        }
        return $content . '</section>';
    }


    protected function getThrownDiceBlock(EventCollection $eventCollection, bool $isGame=true): string
    {
        $engineEventCollection = $eventCollection->getClassEvent(EngineTest::class);
        $bodyEventCollection = $eventCollection->getClassEvent(BodyTest::class);
        $suspensionEventCollection = $eventCollection->getClassEvent(SuspensionTest::class);
        $pitStopEventCollection = $eventCollection->getClassEvent(PitStopTest::class);

        $style = ' class="bg-dark text-white"';

        // First Row Header
        $content  = $this->getRow([
            ConstantConstant::CST_NBSP,
            LabelConstant::LBL_GLOBAL,
            LabelConstant::LBL_ENGINE,
            LabelConstant::LBL_BODY,
            LabelConstant::LBL_SUSPENSION,
            ConstantConstant::CST_NBSP,
            LabelConstant::LBL_PITS,
            LabelConstant::LBL_START,
            'Météo',
            ConstantConstant::CST_NBSP,
            ],
            false,
            array_fill(0, 10, $style),
        );

        // First Row Data (Jet effectués)
        $content .= $this->getRow([
            LabelConstant::LBL_THROWN_DICE,
            $eventCollection->notFilter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->length(),
            $engineEventCollection->length(). ' ('.$engineEventCollection->filter([ConstantConstant::CST_INFLICTED=>1])->length().')',
            $bodyEventCollection->length(),
            $suspensionEventCollection->length(),
            ConstantConstant::CST_NBSP,
            $pitStopEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP])->length(),
            $eventCollection->getClassEvent(StartTest::class)->length(),
            $eventCollection->getClassEvent(MeteoTest::class)->length(),
            ConstantConstant::CST_NBSP
        ],
            true,
            array_merge(
                [ConstantConstant::CST_CLASS_BG_LIGHT],
                array_fill(0, 4, ''),
                [ConstantConstant::CST_CLASS_BG_DARK],
                array_fill(0, 3, ''),
                [ConstantConstant::CST_CLASS_BG_DARK])
        );

        // Second Row Data (Echecs)
        $content .= $this->getRow([
            LabelConstant::LBL_FAILED_DICE,
            $eventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(),
            $engineEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(). ' ('.$engineEventCollection->filter([ConstantConstant::CST_FAIL=>true, ConstantConstant::CST_INFLICTED=>true])->length().')',
            $bodyEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(). ' ('.$bodyEventCollection->filter([ConstantConstant::CST_FAIL=>true, ConstantConstant::CST_INFLICTED=>true])->length().')',
            $suspensionEventCollection->filter([ConstantConstant::CST_FAIL=>true])->length(),
            ConstantConstant::CST_NBSP,
            $pitStopEventCollection->filter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_SHORT_STOP, ConstantConstant::CST_FAIL=>true])->length(),
            ConstantConstant::CST_NBSP],
            true,
            array_merge(
                [ConstantConstant::CST_CLASS_BG_LIGHT],
                array_fill(0, 4, ''),
                [ConstantConstant::CST_CLASS_BG_DARK, '', ' colspan="3" class="bg-dark"']
            )
        );

        if ($isGame) {
            $content .= $this->getRow([
                'Détails',
                LabelConstant::LBL_GLOBAL,
                LabelConstant::LBL_ENGINE,
                LabelConstant::LBL_BODY,
                LabelConstant::LBL_SUSPENSION,
                ConstantConstant::CST_NBSP,
                LabelConstant::LBL_GLOBAL,
                LabelConstant::LBL_ENGINE,
                LabelConstant::LBL_BODY,
                LabelConstant::LBL_SUSPENSION],
                false,
                array_fill(0, 10, $style)
            );

            for ($i=1; $i<=10; $i++) {
                $content .= $this->getRow([
                    $i,
                    $eventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                    $engineEventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                    $bodyEventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                    $suspensionEventCollection->filter([ConstantConstant::CST_SCORE=>$i])->length(),
                    $i+10,
                    $eventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->notFilter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->length(),
                    $engineEventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->length(),
                    $bodyEventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->length(),
                    $suspensionEventCollection->filter([ConstantConstant::CST_SCORE=>$i+10])->length()],
                    true,
                    [ConstantConstant::CST_CLASS_BG_LIGHT, '', '', '', '', ConstantConstant::CST_CLASS_BG_LIGHT]
                );
            }

            $content .= $this->getRow([
                'Moyenne (Attendue)',
                round($eventCollection->notFilter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->sum(true)/$eventCollection->notFilter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->length(), 2).' (10.50)',
                'Unitaire',
                round($eventCollection->notFilter([ConstantConstant::CST_TYPE=>ConstantConstant::CST_LONG_STOP])->length()/20,2),
                ConstantConstant::CST_NBSP],
                false,
                array_merge([
                    $style.ConstantConstant::CST_COL_SPAN_2,
                    $style.ConstantConstant::CST_COL_SPAN_2,
                    $style,
                    $style.ConstantConstant::CST_COL_SPAN_2,
                    $style.' colspan="3"'
                ])
            );
        }
        
        return $content;
    }

}
