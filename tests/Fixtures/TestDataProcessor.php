<?php
/**
 * @license GNU GPL v3+
 */

namespace Matomo\Plugins\CampaignVisitorsByTime\Test\Fixtures;

use Matomo\Plugins\CampaignVisitorsByTime\DataProcessor;

/**
 * Extend the DataProcessor to get around static access of Matomo core classes (Site::getTimezone)
 */
class TestDataProcessor extends DataProcessor {

	protected function getTimeZoneForSite( $site ) {
		return 'Europe/Berlin';
	}
}
