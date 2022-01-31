<?php
/**
 * @license GNU GPL v3+
 */

namespace Matomo\Plugins\CampaignVisitorsByTime;

use DateTime;
use DateTimeZone;
use Matomo\DataTable;
use Matomo\DataTable\Row;
use Matomo\Site;
use Zend_Db_Statement;

/**
 * Archiver functionality for plugin CampaignVisitorsByTime
 */
class Archiver extends \Matomo\Plugin\Archiver {

	public function aggregateDayReport() {
		$db = new DataAccessor( $this->getLogAggregator() );

		$dataTable = new DataTable();
		$dataProcessor = new DataProcessor();

		$dataProcessor->processDatasets( $db->retrieveDataSets() );

		foreach ( $dataProcessor->getCampaigns() as $campaign => $cmpValues ) {
			$parentRow = $this->createRow( $campaign, $cmpValues );

			if ( !empty( $dataProcessor->getCampaignKeywordData( $campaign ) ) ) {
				$parentRow->setSubtable( $this->createSubtable( $dataProcessor->getCampaignKeywordData( $campaign ) ) );
			}
			$dataTable->addRow( $parentRow );
		}

		$this->getProcessor()->insertBlobRecord(
			'CampaignVisitorsByTime_visitorsByTime',
			$dataTable->getSerialized()
		);
	}

	private function createSubtable( $keywordArray ): DataTable {
		$subTable = new DataTable();

		foreach ( $keywordArray as $keyword => $kwdValues ) {
			$subTableRow = $this->createRow( $keyword, $kwdValues );
			$subTable->addRow( $subTableRow );
		}

		return $subTable;
	}

	private function createRow( $label, $values ): Row {
		return new Row(
			[
				Row::COLUMNS => array_merge( [ 'label' => $label ], $values )
			]
		);
	}

	/**
	 * Not implemented for this plugin
	 */
	public function aggregateMultipleReports() {

	}
}
