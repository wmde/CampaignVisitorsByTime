<?php
/**
 * @license GNU GPL v3+
 */
namespace Piwik\Plugins\CampaignVisitorsByTime\Widgets;

use Piwik\Widget\Widget;
use Piwik\Widget\WidgetConfig;
use Piwik\Piwik;

class GetDetailedCampaignVisitors extends Widget {

	public static function configure( WidgetConfig $config ): void {
		$config->setCategoryId( 'Referrers_Referrers' );
		$config->setAction( 'getCampaigns' );
		$config->setSubcategoryId( Piwik::translate( 'CampaignVisitorsByTime_menuTitle' ) );
		$config->setOrder( 100 );
	}

}
