<?php
/**
 * @license GNU GPL v3+
 */
namespace Matomo\Plugins\CampaignVisitorsByTime;

use Matomo\Archive;
use Matomo\DataTable;

/**
 * API for plugin CampaignVisitorsByTime
 *
 * @method static \Matomo\Plugins\CampaignVisitorsByTime\API getInstance()
 */
class API extends \Matomo\Plugin\API {

	public function getCampaigns( $idSite, $period, $date, $segment = false, $expanded = false ): DataTable {
		return Archive::createDataTableFromArchive(
			'CampaignVisitorsByTime_visitorsByTime', $idSite, $period, $date, $segment, $expanded
		);
	}

	public function getKeywordsFromCampaignId( $idSite, $period, $date, $idSubtable, $segment = false ): DataTable {
		return Archive::createDataTableFromArchive(
			'CampaignVisitorsByTime_visitorsByTime', $idSite, $period, $date, $segment, false, false, $idSubtable
		);
	}
}
