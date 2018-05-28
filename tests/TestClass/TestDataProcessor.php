<?php
/**
 * @license GNU GPL v3+
 */

namespace Piwik\Plugins\CampaignVisitorsByTime\Test;

use Piwik\Plugins\CampaignVisitorsByTime\DataProcessor;

/**
 * Test class to get around static access of Matomo core classes to allow for testability
 */
class TestDataProcessor extends DataProcessor {

	protected function getTimeZoneForSite( $site ) {
		return 'Europe/Berlin';
	}
}
