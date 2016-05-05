<?php

namespace K2WSBundle\Controller;

use K2WSBundle\Entity\DataObject;
use K2WSBundle\Controller\CoreController as Core;

class FormationController
{
    private $core;

    public function __construct(CoreController $core)
    {
        $this->core = $core;
    }

    public function executeFormation($folder, $formation, $extension, array $parameters = [], $method = Core::HTTP_METHOD_GET)
    {
        $isGET = ($method === Core::HTTP_METHOD_GET);
        $url = $this->getFormationUrl($folder, $formation, $extension, $parameters, $isGET);

        $ch = $this->core->getCurlHandle($url);
        if ($method === Core::HTTP_METHOD_POST) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            $K2Parameters = $this->getK2Parameters($parameters);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($K2Parameters));
        }

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return json_decode($data, true);
        } else {
            throw new \Exception('Could not execute formation: ' . $httpCode . ' ' . $data);
        }
    }

    public function getFormationUrl($folder, $formation, $extension, array $parameters = [], $urlParameters = true)
    {
        $url = $this->core->getBaseUrl() . 'Formation/' . $folder . '/' . $formation . '/' . $extension;

        if ($urlParameters) {
            foreach ($parameters as $key => $value) {
                if (!$value) {
                    continue;
                }

                $url .= (strpos($url, '?') === false ? '?' : '&') . $key . '=' . urlencode($paramValue);
            }
        }

        return $url;
    }

    public function getK2Parameters(array $parameters)
    {
        $result = ['Parameters' => []];
        foreach ($parameters as $key => $value) {
            $result['Parameters'][] = ['Name' => $key, 'Value' => $value];
        }

        return $result;
    }
}
