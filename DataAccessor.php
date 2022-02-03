<?php
/**
 * @license GNU GPL v3+
 */

namespace Matomo\Plugins\CampaignVisitorsByTime;

use Matomo\Common;
use Matomo\DataAccess\LogAggregator;

/**
 * Database access functionality for plugin CampaignVisitorsByTime
 */
class DataAccessor {

	private $aggregator;

	public function __construct( LogAggregator $aggregator ) {
		$this->aggregator = $aggregator;
	}

	public function retrieveDataSets(): \Zend_Db_Statement {
		$query = $this->aggregator->generateQuery(
			$this->getSelect(),
			$this->getFrom(),
			$this->getWhere(),
			$this->getGroupBy(),
			$this->getOrderBy()
		);
		return $this->aggregator->getDb()->query( $query['sql'], $query['bind'] );
	}

	private function getSelect(): string {
		return 'idsite, ' .
			'referer_name, ' .
			'referer_keyword, ' .
			'FROM_UNIXTIME( FLOOR( UNIX_TIMESTAMP( visit_first_action_time ) / 900 ) * 900 ) AS timestamp_floored, ' .
			'COUNT( DISTINCT HEX( idvisitor ) ) AS numVisitors';
	}

	private function getFrom(): array {
		return [ 'log_visit' ];
	}

	private function getWhere(): string {
		return 'visit_last_action_time >= ? ' .
			'AND visit_last_action_time <= ? ' .
			'AND idsite IN ( ? ) ' .
			'AND referer_type = ' . Common::REFERRER_TYPE_CAMPAIGN;
	}

	private function getGroupBy(): string {
		return 'referer_name, referer_keyword, timestamp_floored';
	}

	private function getOrderBy(): string {
		return 'timestamp_floored, referer_name, referer_keyword';
	}
}
