<?php

namespace K2\K2WSBundle\Controller;

use K2\K2WSBundle\Entity\DataObject;
use K2\K2WSBundle\Controller\CoreController as Core;

class DataController
{
    private $core;

    const FILTER_OPERATOR_UNKNOWN = 0;
    const FILTER_OPERATOR_LESS = 1;
    const FILTER_OPERATOR_LESS_OR_EQUAL = 2;
    const FILTER_OPERATOR_EQUAL = 3;
    const FILTER_OPERATOR_NOT_EQUAL = 4;
    const FILTER_OPERATOR_GREATER_OR_EQUAL = 5;
    const FILTER_OPERATOR_GREATER = 6;
    const FILTER_OPERATOR_LIKE = 7;
    const FILTER_OPERATOR_BETWEEN = 8;
    const FILTER_OPERATOR_IN = 9;
    const FILTER_OPERATOR_EMPTY = 10;

    const LIKE_STYLE_ANYWHERE = 0;
    const LIKE_STYLE_BEGINNING = 1;
    const LIKE_STYLE_STRICT = 2;

    const VALUE_TYPE_CONSTANT = 0;
    const VALUE_TYPE_EXPRESSION = 1;
    const VALUE_TYPE_FIELD = 2;

    const CONDITION_TYPE_FIELD = 'FieldCondition:K2.Data';
    const CONDITION_TYPE_NODE = 'NodeCondition:K2.Data';

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

    public function getDataObjectListBySelection($className, array $conditions = [], $selectionId = null, $topCount = null, $parameters = null)
    {
        $url = $this->core->getBaseUrl() . '/Data/GetListBySelection/' . $className;

        $selectionData = [
            'SelectionId' => $selectionId,
            'TopCount' => $topCount,
            'conditions' => $conditions,
            'parameters' => $parameters
        ];

        $ch = $this->core->getCurlHandle($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Core::HTTP_METHOD_POST);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($selectionData));

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === Core::HTTP_STATUS_OK) {
            return new DataObject($data);
        } else {
            throw new \Exception('Could not get data object list by selection: ' . $httpCode . ' ' . $data);
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
