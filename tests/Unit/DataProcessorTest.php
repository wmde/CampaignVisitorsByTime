<?php

namespace WMDE\Fundraising\Frontend\Tests\Unit\Cli;

use Piwik\Plugins\CampaignVisitorsByTime\Test\TestDataProcessor;

/**
 * @covers \Piwik\Plugins\CampaignVisitorsByTime\DataProcessor
 */
class DataProcessorTest extends \PHPUnit\Framework\TestCase {

	const TEST_REFERER = 'test_referer';
	const TEST_REFERER_ALT = 'test_referer_alt';
	const TEST_HOUR = 5;
	const TEST_MINUTES = '00';
	const TEST_MINUTES_ALT = '15';

	public function testWhenMultipleKeywordsAreUsed_campaignValuesAreCalculatedCorrectly() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$this->assertEquals(
			40,
			$dataProcessor->getCampaigns()[self::TEST_REFERER][$this->getStoredHour(
				self::TEST_HOUR,
				self::TEST_MINUTES
			)],
			'Total for given time with timezone offset should sum up all keywords for specific 15 minute block for a specific referrer'
		);
	}

	public function testWhenKeywordsAreStored_visitorNumbersAreCorrectlyAssignedToTime() {
		$rows = $this->newZendDbRowsMock( $this->getMockDatabaseData() );
		$dataProcessor = new TestDataProcessor();
		$dataProcessor->processDatasets( $rows );
		$keywordData = $dataProcessor->getCampaignKeywordData( self::TEST_REFERER );
		$this->assertEquals(
			33,
			$keywordData['testkeyword04'][$this->getStoredHour( self::TEST_HOUR, self::TEST_MINUTES_ALT )],
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

	private function getHourInTimeZone( $hour ): int {
		$timezone = new \DateTimeZone( 'Europe/Berlin' );
		return $hour + $timezone->getOffset( new \DateTime() ) / 3600;
	}


	private function getStoredHour( int $hour, string $minutes ): string {
		return '0' . ( $this->getHourInTimeZone( $hour ) ) . $minutes . 'h';
	}

	private function getMockDatabaseData(): array {
		return [
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 5 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES_ALT . ':00',
				'idsite' => '1',
				'numVisitors' => 7 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword02',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 15 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword03',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 20 ],
			[
				'referer_name' => self::TEST_REFERER,
				'referer_keyword' => 'testkeyword04',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES_ALT . ':00',
				'idsite' => '1',
				'numVisitors' => 33 ],
			[
				'referer_name' => self::TEST_REFERER_ALT,
				'referer_keyword' => 'testkeyword01',
				'timestamp_floored' => '2018-05-30 0' . self::TEST_HOUR . ':' . self::TEST_MINUTES . ':00',
				'idsite' => '1',
				'numVisitors' => 123 ],
		];
	}
}
