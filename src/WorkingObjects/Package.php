<?php

namespace Ixolit\Dislo\WorkingObjects;

class Package implements WorkingObject {
	/**
	 * @var string
	 */
	private $packageIdentifier;
	/**
	 * @var string
	 */
	private $serviceIdentifier;
	/**
	 * @var DisplayName[]
	 */
	private $displayNames;
	/**
	 * @var bool
	 */
	private $signupAvailable;
	/**
	 * @var Package[]
	 */
	private $addonPackages;
	/**
	 * @var string[]
	 */
	private $metaData;
	/**
	 * @var PackagePeriod
	 */
	private $initialPeriod;
	/**
	 * @var PackagePeriod|null
	 */
	private $recurringPeriod;

	/**
	 * @param string             $packageIdentifier
	 * @param string             $serviceIdentifier
	 * @param DisplayName[]      $displayNames
	 * @param bool               $signupAvailable
	 * @param Package[]          $addonPackages
	 * @param string[]           $metaData
	 * @param PackagePeriod      $initialPeriod
	 * @param PackagePeriod|null $recurringPeriod
	 */
	public function __construct($packageIdentifier, $serviceIdentifier, array $displayNames, $signupAvailable,
								array $addonPackages, array $metaData, PackagePeriod $initialPeriod, $recurringPeriod) {
		$this->packageIdentifier = $packageIdentifier;
		$this->serviceIdentifier = $serviceIdentifier;
		$this->displayNames      = $displayNames;
		$this->signupAvailable   = $signupAvailable;
		$this->addonPackages     = $addonPackages;
		$this->metaData          = $metaData;
		$this->initialPeriod     = $initialPeriod;
		$this->recurringPeriod   = $recurringPeriod;
	}

	/**
	 * @return string
	 */
	public function getPackageIdentifier() {
		return $this->packageIdentifier;
	}

	/**
	 * @return string
	 */
	public function getServiceIdentifier() {
		return $this->serviceIdentifier;
	}

	/**
	 * @return DisplayName[]
	 */
	public function getDisplayNames() {
		return $this->displayNames;
	}

	/**
	 * @return boolean
	 */
	public function isSignupAvailable() {
		return $this->signupAvailable;
	}

	/**
	 * @return Package[]
	 */
	public function getAddonPackages() {
		return $this->addonPackages;
	}

	/**
	 * @return string[]
	 */
	public function getMetaData() {
		return $this->metaData;
	}

	/**
	 * @return PackagePeriod
	 */
	public function getInitialPeriod() {
		return $this->initialPeriod;
	}

	/**
	 * @return PackagePeriod|null
	 */
	public function getRecurringPeriod() {
		return $this->recurringPeriod;
	}

	/**
	 * @param array $response
	 *
	 * @return self
	 */
	public static function fromResponse(array $response) {
		$displayNames = [];
		foreach ($response['displayNames'] as $displayName) {
			$displayNames[] = DisplayName::fromResponse($displayName);
		}
		$addonPackages = [];
		foreach ($response['addonPackages'] as $addonPackage) {
			$addonPackages[] = Package::fromResponse($addonPackage);
		}
		return new Package(
			$response['packageIdentifier'],
			$response['serviceIdentifier'],
			$displayNames,
			$response['signupAvailable'],
			$addonPackages,
			$response['metaData'],
			PackagePeriod::fromResponse($response['initialPeriod']),
			($response['recurringPeriod']?PackagePeriod::fromResponse($response['recurringPeriod']):null)
		);
	}

	/**
	 * @return array
	 */
	public function toArray() {
		$displayNames = [];
		foreach ($this->displayNames as $displayName) {
			$displayNames[] = $displayName->toArray();
		}

		$addonPackages = [];
		foreach ($this->addonPackages as $package) {
			$addonPackages[] = $package->toArray();
		}

		return [
			'_type' => 'Package',
			'packageIdentifier' => $this->packageIdentifier,
			'serviceIdentifier' => $this->serviceIdentifier,
			'displayNames'      => $displayNames,
			'signupAvailable'   => $this->signupAvailable,
			'addonPackages'     => $addonPackages,
			'metaData'          => $this->metaData,
			'initialPeriod'     => $this->initialPeriod->toArray(),
			'recurringPeriod'   => ($this->recurringPeriod?$this->recurringPeriod->toArray():null)
		];
	}
}