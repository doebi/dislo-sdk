<?php

namespace Ixolit\Dislo\Redirector\Base;

/**
 * Interface RedirectorRequestInterface
 * @package Ixolit\Dislo\Redirector\Base
 */
interface RedirectorRequestInterface
{

    /**
     * @return string
     */
    public function getScheme();

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return string
     */
    public function getPath();


    /**
     * @return RequestParameter[]
     */
    public function getRequestParameters();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return Cookie[]
     */
    public function getCookies();

    /**
     * @return string
     */
    public function getQuery();

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @param string $key
     * @return string
     */
    public function getHeader($key);

    /**
     * @return string
     */
    public function getIpBasedCountryCode();

}