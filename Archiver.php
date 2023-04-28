<?php
/**
 * @license GNU GPL v3+
 */

namespace Piwik\Plugins\CampaignVisitorsByTime;

use Piwik\DataTable;
use Piwik\DataTable\Row;

/**
 * Archiver functionality for plugin CampaignVisitorsByTime
 */
class Archiver extends \Piwik\Plugin\Archiver {

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
