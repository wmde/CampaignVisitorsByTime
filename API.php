<?php
/**
 * @license GNU GPL v3+
 */
namespace Piwik\Plugins\CampaignVisitorsByTime;

use Piwik\Archive;
use Piwik\DataTable;

/**
 * API for plugin CampaignVisitorsByTime
 *
 * @method static \Piwik\Plugins\CampaignVisitorsByTime\API getInstance()
 */
class API extends \Piwik\Plugin\API {

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
