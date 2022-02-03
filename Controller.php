<?php
/**
 * @license GNU GPL v3+
 */

namespace Matomo\Plugins\CampaignVisitorsByTime;

use Matomo\ViewDataTable\Factory;

class Controller extends \Matomo\Plugin\Controller {

	/**
	 * @throws \Exception
	 */
	public function getCampaigns(): string {
		$view = Factory::build(
			'table',
			'CampaignVisitorsByTime.getCampaigns'
		);
		$view->config->subtable_controller_action = 'getKeywordsFromCampaignId';
		$view->config->show_search = true;
		$view->config->show_footer_icons = false;

		return $view->render();
	}

	/**
	 * @throws \Exception
	 */
	public function getKeywordsFromCampaignId(): string {
		$view = Factory::build(
			'table',
			'CampaignVisitorsByTime.getKeywordsFromCampaignId'
		);
		$view->config->show_search = false;
		$view->config->show_footer_icons = false;

		return $view->render();
	}
}
