<?php

namespace Ixolit\Dislo\Response;

use Ixolit\Dislo\WorkingObjects\Subscription;

class SubscriptionExternalAddonCreateResponse {
	/**
	 * @var Subscription
	 */
	private $subscription;

	/**
	 * @var int
	 */
	private $upgradeId;

	/**
	 * @param Subscription $subscription
	 * @param int          $upgradeId
	 */
	public function __construct(Subscription $subscription, $upgradeId) {
		$this->subscription = $subscription;
		$this->upgradeId    = $upgradeId;
	}

	/**
	 * @return Subscription
	 */
	public function getSubscription() {
		return $this->subscription;
	}

	/**
	 * @return int
	 */
	public function getUpgradeId() {
		return $this->upgradeId;
	}

	public static function fromResponse($response) {
		return new SubscriptionExternalAddonCreateResponse(
			Subscription::fromResponse($response['subscription']),
			$response['upgradeId']
		);
	}
}