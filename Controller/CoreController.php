<?php

namespace K2WSBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CoreController
{
    const HTTP_STATUS_OK = 200;
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';

    private $config;
    private $tokenStorage;

    public function __construct($config, TokenStorage $tokenStorage)
    {
        $this->config = $config;
        $this->tokenStorage = $tokenStorage;
    }

    public function getBaseUrl()
    {
        return ($this->config['secure'] ? 'https' : 'http') . '://' . $this->config['host'] . '/' . $this->config['name'] . '/';
    }

    public function getCurlHandle($url, $username = null, $password = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $this->getAuthorizationHeader($url, $username, $password),
            'Content-Type: application/json; charset=utf-8'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return $ch;
    }

    public function getAuthorizationHeader($url, $username = null, $password = null)
    {
        if (!$username || !$password) {
            $token = $this->tokenStorage->getToken();
            if ($token) {
                $username = $token->getUser()->getUsername();
                $password = $token->getUser()->getPassword();
            } else {
                $username = $this->config['username'];
                $password = $this->config['password'];
            }
        }

        return 'Authorization: ' . $username . ':' . base64_encode(hash_hmac('md5', mb_strtoupper(rawurldecode($url), 'UTF-8'), $password, true));
    }
}
