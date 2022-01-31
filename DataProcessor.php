<?php
/**
 * @license GNU GPL v3+
 */

namespace Matomo\Plugins\CampaignVisitorsByTime;

use Matomo\Site;

/**
 * Processes visitor by grouping it by keyword and campaign
 */
class DataProcessor {

	private $campaigns = [];
	private $keywords = [];
	private $siteTimezones = [];

	public function processDatasets( \Zend_Db_Statement $dataSets ): void {
		// @codingStandardsIgnoreStart
		while ( $row = $dataSets->fetch() ) {
			// @codingStandardsIgnoreEnd
			$campaign = $row['referer_name'];
			$keyword = $row['referer_keyword'];
			$timestamp = $row['timestamp_floored'];

			if ( !array_key_exists( $campaign, $this->campaigns ) ) {
				$this->initCampaign( $campaign );
			}

			if ( !array_key_exists( $keyword, $this->keywords[$campaign] ) ) {
				$this->initKeywordForCampaign( $campaign, $keyword );
			}

			$timestampFloored = $this->parseTimestamp(
				$timestamp,
				$this->getTimeZoneForSite( $row['idsite'] )
			);

			$this->campaigns[$campaign][$timestampFloored] += intval( $row['numVisitors'] );
			$this->keywords[$campaign][$keyword][$timestampFloored] = intval( $row['numVisitors'] );

			$this->campaigns[$campaign]['total'] += intval( $row['numVisitors'] );
			$this->keywords[$campaign][$keyword]['total'] += intval( $row['numVisitors'] );
		}
	}

	public function getCampaigns(): array {
		return $this->campaigns;
	}

	public function getKeywords(): array {
		return $this->keywords;
	}

	public function getCampaignKeywordData( $campaign ): array {
		return $this->keywords[$campaign] ?? [];
	}

	private function initCampaign( $campaign ): void {
		$this->campaigns[$campaign] = $this->initResultArray();
		$this->keywords[$campaign] = [];
	}

	private function initKeywordForCampaign( $campaign, $keyword ): void {
		$this->keywords[$campaign][$keyword] = $this->initResultArray();
	}

	private function initResultArray(): array {
		$arr = [];

		for ( $i = 0; $i < 24; $i++ ) {
			$hour = str_pad( $i, 2, '0', STR_PAD_LEFT );
			$arr[$hour . '00h'] = 0;
			$arr[$hour . '15h'] = 0;
			$arr[$hour . '30h'] = 0;
			$arr[$hour . '45h'] = 0;
		}
		$arr['total'] = 0;

		return $arr;
	}

	private function parseTimestamp( $mysqlTime, $siteTimeZone ): string {
		$date = new \DateTime( $mysqlTime );
		$date->setTimezone( new \DateTimeZone( $siteTimeZone ) );
		return $date->format( 'Hi' ) . 'h';
	}

	protected function getTimeZoneForSite( $site ) {
		if ( !isset( $this->siteTimezones[$site] ) ) {
			$this->siteTimezones[$site] = Site::getTimezoneFor( $site );
		}
		return $this->siteTimezones[$site];
	}
}
