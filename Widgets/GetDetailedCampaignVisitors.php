<?php
/**
 * @license GNU GPL v3+
 */
namespace Piwik\Plugins\CampaignVisitorsByTime\Widgets;

use Matomo\Widget\Widget;
use Matomo\Widget\WidgetConfig;
use Matomo\Matomo;

class GetDetailedCampaignVisitors extends Widget {

	public static function configure( WidgetConfig $config ): void {
		$config->setCategoryId( 'Referrers_Referrers' );
		$config->setAction( 'getCampaigns' );
		$config->setSubcategoryId( Matomo::translate( 'CampaignVisitorsByTime_menuTitle' ) );
		$config->setOrder( 100 );
	}

}
