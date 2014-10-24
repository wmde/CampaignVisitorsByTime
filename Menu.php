<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @author Kai Nissen <kai.nissen@wikimedia.de>
 *
 * @category Piwik_Plugins
 * @package Piwik_CampaignVisitorsByTime
 */
namespace Piwik\Plugins\CampaignVisitorsByTime;

use Piwik\Menu\MenuReporting;
use Piwik\Piwik;

class Menu extends \Piwik\Plugin\Menu {
	public function configureReportingMenu( MenuReporting $menu ) {
		$menu->addReferrersItem(
			Piwik::translate('CampaignVisitorsByTime_menuTitle'),
			$this->urlForAction('getCampaigns'),
			100
		);
	}
}
