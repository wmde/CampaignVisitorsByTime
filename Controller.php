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

use Piwik\Archive;
use Piwik\ArchiveProcessor;
use Piwik\Period;
use Piwik\View;
use Piwik\ViewDataTable\Factory;

class Controller extends \Piwik\Plugin\Controller {

	public function getCampaigns() {
		$view = Factory::build(
			$defaultType = 'table',
			$apiAction = 'CampaignVisitorsByTime.getCampaigns'/*,
			$controllerAction = 'CampaignVisitorsByTime.getCampaigns'*/
		);
		$view->config->subtable_controller_action = 'getKeywordsFromCampaignId';
		$view->config->show_search = true;
		$view->config->show_footer_icons = false;

		return $view->render();
	}

	public function getKeywordsFromCampaignId() {
		$view = Factory::build(
			$defaultType = 'table',
			$apiAction = 'CampaignVisitorsByTime.getKeywordsFromCampaignId'/*,
			$controllerAction = 'CampaignVisitorsByTime.getKeywordsFromCampaignId'*/
		);
		$view->config->show_search = false;
		$view->config->show_footer_icons = false;

		return $view->render();
	}
}
