<?php

namespace K2\K2WSBundle\Controller;

use K2\K2WSBundle\Entity\DataObject;
use K2\K2WSBundle\Controller\CoreController as Core;

class DataController
{
    private $core;

    public function __construct(CoreController $core)
    {
        $this->core = $core;
    }

    public function getDataObjectList($className, array $params = [])
    {
        $url = $this->getDataUrl($className, null, $params);

        $ch = $this->core->getCurlHandle($url);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return new DataObject($data);
        } else {
            throw new \Exception('Could not get data object list: ' . $httpCode . ' ' . $data);
        }
    }

    public function getDataObject($className, $primaryKey, array $params = [])
    {
        $url = $this->getDataUrl($className, $primaryKey, $params);

        $ch = $this->core->getCurlHandle($url);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return new DataObject($data);
        } else {
            throw new \Exception('Could not get data object: ' . $httpCode . ' ' . $data);
        }
    }

    public function postDataObject(DataObject $dataObject)
    {
        $url = $this->getDataUrl($dataObject['DOClassName']);

        $ch = $this->core->getCurlHandle($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Core::HTTP_METHOD_POST);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataObject->getJSON());
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return new DataObject($data);
        } else {
            throw new \Exception('Could not post data object: ' . $httpCode . ' ' . $data);
        }
    }

    public function putDataObject(DataObject $dataObject, $primaryKey)
    {
        $url = $this->getDataUrl($dataObject['DOClassName'], $primaryKey);

        $ch = $this->core->getCurlHandle($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Core::HTTP_METHOD_PUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataObject->getJSON());
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return new DataObject($data);
        } else {
            throw new \Exception('Could not put data object: ' . $httpCode . ' ' . $data);
        }
    }

    public function getDataUrl($className, $primaryKey = null, array $params = [])
    {
        $url = $this->core->getBaseUrl() . 'Data/' . $className;
        if ($primaryKey) {
            $url .= '/' . $primaryKey;
        }

        foreach ($params as $key => $value) {
            if (!$value) {
                continue;
            }

            if (is_array($value)) {
                $paramValue = implode(',', $value);
            } else {
                $paramValue = $value;
            }

            $url .= (strpos($url, '?') === false ? '?' : '&') . $key . '=' . urlencode($paramValue);
        }

        return $url;
    }
}
