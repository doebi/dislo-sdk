<?php

namespace Ixolit\Dislo\Response;


/**
 * Class UserVerificationStartResponse
 *
 * @package Ixolit\Dislo\Response
 */
class UserVerificationStartResponse {

    /**
     * @param $response
     *
     * @return $this
     */
    public static function fromResponse($response) {
        return new self();
    }

}