<?php

namespace K2WSBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CoreController
{
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
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user) {
                $username = $user->getUsername();
                $password = $user->getPassword();
            } else {
                $username = $config['username'];
                $password = $config['password'];
            }
        }

        return 'Authorization: ' . $username . ':' . base64_encode(hash_hmac('md5', mb_strtoupper(rawurldecode($url), 'UTF-8'), $password, true));
    }
}
