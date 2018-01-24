<?php

namespace Ixolit\Dislo\Response;


/**
 * Class SubscriptionCallSpiResponse
 *
 * @package Ixolit\Dislo\Response
 *
 * @deprecated use Ixolit\Dislo\Response\SubscriptionCallSpiResponseObject instead
 */
class SubscriptionCallSpiResponse {
	/**
	 * @var array
	 */
	private $spiResponse;

	/**
	 * @param array $spiResponse
	 */
	public function __construct(array $spiResponse) {
		$this->spiResponse = $spiResponse;
	}

	/**
	 * @return array
	 */
	public function getSpiResponse() {
		return $this->spiResponse;
	}

	/**
	 * @param array $response
	 *
	 * @return SubscriptionCallSpiResponse
	 */
	public static function fromResponse($response) {
		return new SubscriptionCallSpiResponse($response['spiResponse']);
	}
}