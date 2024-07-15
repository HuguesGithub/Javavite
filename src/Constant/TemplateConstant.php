<?php
namespace src\Constant;

interface TemplateConstant
{
    public const ASSETS_PATH            = 'assets/';
    public const HTML_PATH              = self::ASSETS_PATH.'html/';
    public const LOGS_PATH              = self::ASSETS_PATH.'logs/';
    public const SRC_PATH               = 'src/';

    public const TPL_CARD_DOUBLE_TABLE  = 'templates/card/cardDoubleTable.tpl';
    public const TPL_CARD_PLAYER        = 'templates/card/cardPlayer.tpl';
    public const TPL_CARD_SIMPLE_TABLE  = 'templates/card/cardSimpleTable.tpl';
    public const TPL_CHANGELOG          = 'templates/section/changelog.tpl';
    public const TPL_DASHBOARD_PANEL    = 'templates/section/dashboard.tpl';
    
    public const TPL_BASE               = 'templates/base.tpl';
    public const TPL_FOOTER             = 'templates/footer.tpl';
    public const TPL_HEADER             = 'templates/header.tpl';
}
