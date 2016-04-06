<?php

namespace K2WSBundle\Entity;

class DataObject extends \ArrayObject
{
    public function __construct($json)
    {
        $array = $this->getSimpleDataArray(json_decode($json, true));
        foreach ($array as $key => $value) {
            $this[$key] = $value;
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
                        } else {
                            $result['FieldValues'][] = ['Name' => $fieldKey, 'Value' => $fieldValue];
                        }
                    }
                } else {
                    $result[$key] = $this->getExtendedDataArray($value);
                }
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