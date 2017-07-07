<?php

namespace Ixolit\Dislo\Context;


use ESY\DisloClient;
use Ixolit\Dislo\Client;
use Ixolit\Dislo\Exceptions\InvalidTokenException;
use Ixolit\Dislo\WorkingObjects\AuthToken;
use Ixolit\Dislo\WorkingObjects\BillingEvent;
use Ixolit\Dislo\WorkingObjects\Flexible;
use Ixolit\Dislo\WorkingObjects\Price;
use Ixolit\Dislo\WorkingObjects\Subscription;
use Ixolit\Dislo\WorkingObjects\User;

/**
 * Class UserContext
 *
 * @package Ixolit\Dislo\Context
 */
class UserContext {

    /** @var Client */
    private $client;

    /** @var User */
    private $user;

    /** @var Subscription[] */
    private $subscriptions;

    /** @var Flexible */
    private $activeFlexible;

    /** @var BillingEvent[] */
    private $billingEvents;

    /** @var int */
    private $billingEventsTotalCount;

    /** @var Price */
    private $accountBalance;

    /** @var AuthToken[] */
    private $authTokens;

    /**
     * @param Client $client
     * @param User   $user
     *
     * @throws InvalidTokenException
     */
    public function __construct(Client $client, User $user) {
        $this->client = $client;
        $this->user = $user;

        if ($this->client->isForceTokenMode() && !$user->getAuthToken()) {
            throw new InvalidTokenException();
        }
    }

    /**
     * @return User
     *
     * @throws InvalidTokenException
     */
    public function getUser() {
        if (!isset($this->user)) {
            throw new InvalidTokenException();
        }

        return $this->user;
    }

    /**
     * @param bool $cached
     *
     * @return Subscription[]
     */
    public function getAllSubscriptions($cached = true) {
        if ($cached && isset($this->subscriptions)) {
            return $this->subscriptions;
        }

        $this->subscriptions = $this->getClient()->subscriptionGetAll(
            $this->getUserIdentifierForClient()
        )->getSubscriptions();

        return $this->subscriptions;
    }

    /**
     * @param bool $cached
     *
     * @return Subscription[]
     */
    public function getActiveSubscriptions($cached = true) {
        $subscriptions = $this->getAllSubscriptions($cached);

        $activeSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if (
                \in_array($subscription->getStatus(), [
                    Subscription::STATUS_CANCELED,
                    Subscription::STATUS_RUNNING
                ])
            ) {
                $activeSubscriptions[] = $subscription;
            }
        }

        return $activeSubscriptions;
    }

    /**
     * @param bool $cached
     *
     * @return Subscription|null
     */
    public function getFirstActiveSubscription($cached = true) {
        $activeSubscriptions = $this->getActiveSubscriptions($cached);

        if (empty($activeSubscriptions)) {
            return null;
        }

        return \reset($activeSubscriptions);
    }

    /**
     * @param bool $cached
     *
     * @return Subscription[]
     */
    public function getStartedSubscriptions($cached = true) {
        $subscriptions = $this->getAllSubscriptions($cached);

        $startedSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            if ($subscription->getStartedAt()) {
                $startedSubscriptions[] = $subscription;
            }
        }

        return $startedSubscriptions;
    }

    /**
     * @param bool $cached
     *
     * @return Subscription|null
     */
    public function getFirstStartedSubscription($cached = true) {
        $startedSubscriptions = $this->getStartedSubscriptions($cached);

        if (empty($startedSubscriptions)) {
            return null;
        }

        return \reset($startedSubscriptions);
    }

    /**
     * @param      $subscriptionId
     * @param bool $cached
     *
     * @return Subscription|null
     */
    public function getSubscription($subscriptionId, $cached = true) {
        $subscriptions = $this->getAllSubscriptions($cached);

        foreach ($subscriptions as $subscription) {
            if ($subscription->getSubscriptionId() == $subscriptionId) {
                return $subscription;
            }
        }

        return null;
    }

    /**
     * @param Subscription $subscription
     *
     * @return $this
     */
    public function addSubscription(Subscription $subscription) {
        $this->getAllSubscriptions(true);

        $this->subscriptions[] = $subscription;

        return $this;
    }

    /**
     * @param bool $cached
     *
     * @return Flexible
     */
    public function getActiveFlexible($cached = true) {
        if ($cached && isset($this->activeFlexible)) {
            return $this->activeFlexible;
        }

        $this->activeFlexible = $this->getClient()->billingGetFlexible(
            $this->getUserIdentifierForClient()
        )->getFlexible();

        return $this->activeFlexible;
    }

    /**
     * @param Flexible $activeFlexible
     *
     * @return $this
     */
    public function setActiveFlexible(Flexible $activeFlexible) {
        $this->activeFlexible = $activeFlexible;

        return $this;
    }

    /**
     * @param int    $limit
     * @param int    $offset
     * @param string $orderDir
     * @param bool   $cached
     *
     * @return BillingEvent[]
     */
    public function getBillingEvents($limit = 10,
                                     $offset = 0,
                                     $orderDir = DisloClient::ORDER_DIR_DESC,
                                     $cached = true
    ) {
        if ($cached && isset($this->billingEvents)) {
            return $this->billingEvents;
        }

        $billingEventsResponse = $this->getClient()->billingGetEventsForUser(
            $this->getUserIdentifierForClient(),
            $limit,
            $offset,
            $orderDir
        );

        $this->billingEvents = $billingEventsResponse->getBillingEvents();
        $this->billingEventsTotalCount = $billingEventsResponse->getTotalCount();

        return $this->billingEvents;
    }

    /**
     * @param int  $billingEventId
     * @param bool $cached
     *
     * @return BillingEvent|null
     */
    public function getBillingEvent($billingEventId, $cached = true) {
        $billingEvents = $this->getBillingEvents($cached);

        foreach ($billingEvents as $billingEvent) {
            if ($billingEvent->getBillingEventId() == $billingEventId) {
                return $billingEvent;
            }
        }

        return null;
    }

    /**
     * @param BillingEvent $billingEvent
     *
     * @return $this
     */
    public function addBillingEvent(BillingEvent $billingEvent) {
        $this->getBillingEvents(true);

        $this->billingEvents[] = $billingEvent;

        return $this;
    }

    /**
     * @param int    $limit
     * @param int    $offset
     * @param string $orderDir
     * @param bool   $cached
     *
     * @return int
     */
    public function getBillingEventsTotalCount($limit = 10,
                                               $offset = 0,
                                               $orderDir = DisloClient::ORDER_DIR_DESC,
                                               $cached = true
    ) {
        if ($cached && isset($this->billingEventsTotalCount)) {
            return $this->billingEventsTotalCount;
        }

        $this->getBillingEvents($limit, $offset, $orderDir, $cached);

        return $this->billingEventsTotalCount;
    }

    /**
     * @param bool $cached
     *
     * @return Price
     */
    public function getAccountBalance($cached = true) {
        if ($cached && isset($this->accountBalance)) {
            return $this->accountBalance;
        }

        $this->accountBalance = $this->getClient()->userGetBalance($this->getUserIdentifierForClient())->getBalance();

        return $this->accountBalance;
    }

    /**
     * @param bool $cached
     *
     * @return AuthToken[]
     */
    public function getAuthTokens($cached = true) {
        if ($cached && isset($this->authTokens)) {
            return $this->authTokens;
        }

        $this->authTokens = $this->getClient()->userGetTokens($this->getUserIdentifierForClient())->getTokens();

        return $this->authTokens;
    }

    /**
     * @param array $userMetaData
     *
     * @return $this
     */
    public function changeUserMetaData($userMetaData = []) {
        $authToken = $this->getUser()->getAuthToken();

        $changedUser = $this->getClient()->userChange(
            $this->getUserIdentifierForClient(),
            $this->getUser()->getLanguage(),
            $userMetaData
        )->getUser();

        $this->user = $this->convertFromUserWithAuthToken($changedUser, $authToken);

        return $this;
    }

    /**
     * @param string $newPassword
     *
     * @return $this
     */
    public function changeUserPassword($newPassword) {
        $authToken = $this->getUser()->getAuthToken();

        $changedUser = $this->getClient()->userChangePassword(
            $this->getUserIdentifierForClient(),
            $newPassword
        )->getUser();

        $this->user = $this->convertFromUserWithAuthToken($changedUser, $authToken);

        return $this;
    }

    /**
     * @return $this
     */
    public function deleteUser() {
        $this->getClient()->userDelete($this->getUserIdentifierForClient());

        $this->user = null;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableUserLogin() {
        $this->user = $this->getClient()->userDisableLogin($this->getUserIdentifierForClient())->getUser();

        return $this;
    }

    /**
     * @return $this
     */
    public function closeActiveFlexible() {
        $this->getClient()->billingCloseFlexible($this->getActiveFlexible(), $this->getUserIdentifierForClient());

        $this->activeFlexible = null;

        return $this;
    }

    /*
     * Protected helper functions
     */

    /**
     * @return Client
     */
    protected function getClient() {
        return $this->client;
    }

    /**
     * @param User      $user
     * @param AuthToken $authToken
     *
     * @return User
     */
    protected function convertFromUserWithAuthToken(User $user, AuthToken $authToken) {
        $changedUser = new User(
            $user->getUserId(),
            $user->getCreatedAt(),
            $user->isLoginDisabled(),
            $user->getLanguage(),
            $user->getLastLoginDate(),
            $user->getLastLoginIp(),
            $user->getMetaData(),
            $user->getCurrencyCode(),
            $user->getVerifiedData(),
            $authToken
        );

        return $changedUser;
    }

    /**
     * @return int|string
     *
     * @throws InvalidTokenException
     */
    protected function getUserIdentifierForClient() {
        if ($this->getClient()->isForceTokenMode()) {
            if (!$this->getUser()->getAuthToken()) {
                throw new InvalidTokenException();
            }

            return $this->getUser()->getAuthToken()->getToken();
        }

        return $this->getUser()->getUserId();
    }

}