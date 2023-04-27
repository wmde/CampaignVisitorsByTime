<?php

namespace Piwik\Plugins\CampaignVisitorsByTime\Test\Unit;

use PHPUnit\Framework\TestCase;
// we need to extend the system-under-test to get around Matomo static access
use Matomo\Plugins\CampaignVisitorsByTime\Test\Fixtures\TestDataProcessor;

/**
 * @covers \Matomo\Plugins\CampaignVisitorsByTime\DataProcessor
 */
class DataProcessorTest extends TestCase {

	private const TEST_REFERER = 'test_referer';
	private const TEST_REFERER_ALT = 'test_referer_alt';
	// Date Parts for test fixture data, in UTC
	// May in Europe/Berlin time zone will have offset of +2 because of DST
	private const TEST_DAY = '2018-05-30';
	private const TEST_HOUR = '05';
	private const TEST_MINUTES = '00';
	private const TEST_MINUTES_ALT = '15';

	public function testWhenMultipleKeywordsAreUsed_campaignValuesAreCalculatedCorrectly() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$campaignData = $dataProcessor->getCampaigns();
		// array key in Europe/Berlin time zone, DST
		$timeSlice = '0700h';
		$this->assertEquals(
			40,
			$campaignData[self::TEST_REFERER][$timeSlice],
			'Total for given time with timezone offset should sum up all keywords for specific 15 minute block for a specific referrer'
		);
	}

	public function testWhenKeywordsAreStored_visitorNumbersAreCorrectlyAssignedToTime() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$keywordData = $dataProcessor->getCampaignKeywordData( self::TEST_REFERER );
		// array key in Europe/Berlin time zone, DST
		$timeSlice = '0715h';
		$this->assertEquals(
			33,
			$keywordData['testkeyword04'][$timeSlice],
			'Individual keywords store their own value per campaign for a specific given 15 minutes block'
		);
	}

	public function testWhenKeywordsAreStored_campaignTotalsAreCalculatedCorrectly() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$campaignData = $dataProcessor->getCampaigns();
		$this->assertEquals(
			80,
			$campaignData[self::TEST_REFERER]['total']
		);
	}

	public function testWhenKeywordsAreStored_keywordTotalsAreCalculatedCorrectly() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$keywordData = $dataProcessor->getCampaignKeywordData( self::TEST_REFERER );
		$this->assertEquals(
			12,
			$keywordData['testkeyword01']['total']
		);
	}

	public function newZendDbRowsMock( array $rows ) {
		$dbRows = $this->createMock( \Zend_Db_Statement::class );
		$dbRows->method( 'fetch' )->willReturnOnConsecutiveCalls( ...$rows );
		return $dbRows;
	}

	private function getMockDatabaseData(): array {
		return [
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 5 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES_ALT . ':00',
				'idsite' => '1',
				'numVisitors' => 7 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword02',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 15 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword03',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 20 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword04',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES_ALT . ':00',
				'idsite' => '1',
				'numVisitors' => 33 ],
			[
				'referer_name' => self::TEST_REFERER_ALT,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => self::TEST_DAY . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 123 ],
		];
	}
}
