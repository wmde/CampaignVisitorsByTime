<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\CampaignVisitorsByTime\Widgets;

use Piwik\Widget\Widget;
use Piwik\Widget\WidgetConfig;
use Piwik\Piwik;

class GetDetailedCampaignVisitors extends Widget
{
    public static function configure( WidgetConfig $config )
    {
		$config->setCategoryId( 'Referrers_Referrers' );
		$config->setAction( 'getCampaigns' );
		$config->setSubcategoryId( Piwik::translate( 'CampaignVisitorsByTime_menuTitle' ) );
		$config->setOrder( 100 );
    }

}
