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

use DateTime;
use DateTimeZone;
use Piwik\Common;
use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Metrics;
use Piwik\Site;
use Zend_Db_Statement;

/**
 * Archiver functionality for plugin CampaignVisitorsByTime
 */
class Archiver extends \Piwik\Plugin\Archiver {
	
	private $campaigns = array();
	private $keywords = array();

	public function aggregateDayReport() {
		$this->processDataSets( $this->retrieveDataSets() );

		$dataTable = new DataTable();
		
		foreach( $this->campaigns as $campaign => $cmpValues ) {
			$parentRow = $this->createRow( $campaign, $cmpValues );

			if( isset( $this->keywords[$campaign] ) ) {
				$parentRow->setSubtable( $this->createSubtable( $this->keywords[$campaign] ) );
			}
			$dataTable->addRow( $parentRow );
		}

		$this->getProcessor()->insertBlobRecord(
			'CampaignVisitorsByTime_visitorsByTime',
			$dataTable->getSerialized()
		);
	}
	
	public function aggregateMultipleReports() {
		
	}
	
	private function retrieveDataSets() {
		$aggregator = $this->getLogAggregator();
		$query = $aggregator->generateQuery(
			$select =
				'idsite, ' .
				'referer_name, ' .
				'referer_keyword, ' .
				'FROM_UNIXTIME( FLOOR( UNIX_TIMESTAMP( visit_first_action_time ) / 900 ) * 900 ) AS timestamp_floored, ' .
				'COUNT( DISTINCT HEX( idvisitor ) ) AS numVisitors',
			$from = array( 'log_visit' ),
			$where =
				'visit_last_action_time >= ? ' .
				'AND visit_last_action_time <= ? ' .
				'AND idsite IN ( ? ) ' .
				'AND referer_type = ' . Common::REFERRER_TYPE_CAMPAIGN,
			$groupBy =
				'referer_name, referer_keyword, timestamp_floored',
			$orderBy =
				'timestamp_floored, referer_name, referer_keyword'
		);
		return $aggregator->getDb()->query( $query['sql'], $query['bind'] );
	}

	private function processDatasets( Zend_Db_Statement $dataSets ) {
		while( $row = $dataSets->fetch() ) {
			
			$campaign = $row['referer_name'];
			$keyword = $row['referer_keyword'];
			
			if( !array_key_exists( $campaign, $this->campaigns ) ) {
				$this->campaigns[$campaign] = $this->initResultArray();
				$this->keywords[$campaign] = array();
			}

			if( !array_key_exists( $keyword, $this->keywords[$campaign] ) ) {
				$this->keywords[$campaign][$keyword] = $this->initResultArray();
			}

			$timestampFloored = $this->parseTimestamp(
				$row['timestamp_floored'],
				Site::getTimezoneFor( $row['idsite'] )
			);

			$this->campaigns[$campaign][$timestampFloored] += intval( $row['numVisitors'] );
			$this->keywords[$campaign][$keyword][$timestampFloored] = intval( $row['numVisitors'] );

			// totals
			$this->campaigns[$campaign]['total'] += intval( $row['numVisitors'] );
			$this->keywords[$campaign][$keyword]['total'] += intval( $row['numVisitors'] );
		}
	}

	private function initResultArray() {
		$arr = array();

		for( $i = 0; $i < 24; $i ++ ) {
			$hour = str_pad( $i, 2, '0', STR_PAD_LEFT ); 
			$arr[$hour . '00h'] = 0;
			$arr[$hour . '15h'] = 0;
			$arr[$hour . '30h'] = 0;
			$arr[$hour . '45h'] = 0;
		}
		$arr['total'] = 0;

		return $arr;
	}
	
	private function parseTimestamp( $mysqlTime, $siteTimeZone ) {
		$date = new DateTime( $mysqlTime );
		$date->setTimezone( new DateTimeZone( $siteTimeZone ) );
		return $date->format( 'Hi' ) . 'h';
	}
	
	private function createRow( $label, $values ) {
		return new Row(
			array(
				Row::COLUMNS => array_merge( array( 'label' => $label ), $values )
			)
		);
	}
	
	private function createSubtable( $keywordArray ) {
		$subTable = new DataTable();

		foreach( $keywordArray as $keyword => $kwdValues ) {
			$subTableRow = $this->createRow( $keyword, $kwdValues );
			$subTable->addRow( $subTableRow );
		}

		return $subTable;
	}
}
