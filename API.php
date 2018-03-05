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
use Piwik\DataTable;
use Piwik\DataTable\Row;

/**
 * API for plugin CampaignVisitorsByTime
 *
 * @method static \Piwik\Plugins\CampaignVisitorsByTime\API getInstance()
 */
class API extends \Piwik\Plugin\API {

	public function getCampaigns( $idSite, $period, $date, $segment = false, $expanded = false ) {
		return Archive::createDataTableFromArchive( 'CampaignVisitorsByTime_visitorsByTime', $idSite, $period, $date, $segment, $expanded );
	}

	public function getKeywordsFromCampaignId( $idSite, $period, $date, $idSubtable, $segment = false ) {
		return Archive::createDataTableFromArchive( 'CampaignVisitorsByTime_visitorsByTime', $idSite, $period, $date, $segment, $expanded = false, false, $idSubtable );
	}
}
