<?php

namespace K2\K2WSBundle\Entity;

class DataObject extends \ArrayObject
{
    public function __construct($json)
    {
        $array = $this->getSimpleDataArray(json_decode($json, true));
        foreach ($array as $key => $value) {
            $this[$key] = $value;
        }

        $this->parseURLParams('NextPageURL');
        $this->parseURLParams('PrevPageURL');
        $this->parseURLParams('FirstPageURL');
        $this->parseURLParams('LastPageURL');
    }

    private function parseURLParams($arrayKey)
    {
        if (array_key_exists($arrayKey, $this)) {
            $url = explode('?', $this[$arrayKey]);
            if (count($url) > 1) {
                parse_str($url[1], $paramArray);
                $this[$arrayKey . 'Params'] = $paramArray;
            }
        }
    }

    private function getSimpleDataArray($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($key === 'FieldValues') {
                    foreach ($value as $fieldValue) {
                        $_key = $fieldValue['Name'];
                        $_value = $fieldValue['Value'];
                        if (is_array($_value)) {
                            $result['FieldValues'][$_key] = $this->getSimpleDataArray($_value);
                        } else if (preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $_value, $matches)) {
                            $result['FieldValues'][$_key] = \DateTime::createFromFormat('U.u', vsprintf('%2$s.%3$s', $matches), new \DateTimeZone(vsprintf('%4$s', $matches)));

                        } else {
                            $result['FieldValues'][$_key] = $_value;
                        }
                    }
                } else {
                    $result[$key] = $this->getSimpleDataArray($value);
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function getExtendedDataArray($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($key === 'FieldValues') {
                    $result['FieldValues'] = [];
                    foreach ($value as $fieldKey => $fieldValue) {
                        if (is_array($fieldValue)) {
                            $result['FieldValues'][] = ['Name' => $fieldKey, 'Value' => $this->getExtendedDataArray($fieldValue)];
                        } else if ($fieldValue instanceof \DateTime) {
                            $result['FieldValues'][] = ['Name' => $fieldKey, 'Value' => '/Date(' . $fieldValue->format('U') . '000+0200)/'];
                        } else {
                            $result['FieldValues'][] = ['Name' => $fieldKey, 'Value' => $fieldValue];
                        }
                    }
                } else {
                    $result[$key] = $this->getExtendedDataArray($value);
                }
            } else if ($value instanceof \DateTime) {
                $result[$key] = '/Date(' . $value->format('U') . '000+0200)/';
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getJSON()
    {
        return json_encode($this->getExtendedDataArray($this));
    }
}
