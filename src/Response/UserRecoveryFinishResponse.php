<?php

namespace Ixolit\Dislo\Response;

use Ixolit\Dislo\WorkingObjects\User;

class UserRecoveryFinishResponse {
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @param User $user
	 */
	public function __construct(User $user) {
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	public static function fromResponse($response) {
		return new UserRecoveryFinishResponse(User::fromResponse($response['user']));
	}
}