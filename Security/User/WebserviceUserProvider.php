<?php

namespace K2WSBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use K2WSBundle\Controller\CoreController;
use K2WSBundle\Controller\DataController;
use K2WSBundle\Entity\DataObject;

class WebserviceUserProvider implements UserProviderInterface
{
    private $core;
    private $data;

    public function __construct(CoreController $core, DataController $data)
    {
        $this->core = $core;
        $this->data = $data;
    }

    public function loadUserByUsername($credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        $url = $this->data->getDataUrl('ActualContactPerson', null, [
            'fields' => [
                'ContactPersonId',
                'Name',
                'Surname',
                'TimeStamp'
                ]
            ])
        ;

        $ch = $this->core->getCurlHandle($url, $username, $password);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($data && $httpCode === 200) {
            $user = new DataObject($data);

            $id = $user['FieldValues']['ContactPersonId'];
            $firstName = $user['FieldValues']['Name'];
            $lastName = $user['FieldValues']['Surname'];
            $timeStamp = $user['FieldValues']['TimeStamp'];
            $roles = $this->getUserRoles($id);
            $extraData = $this->getUserExtraData($id);

            return new WebserviceUser($id, $username, $password, $firstName, $lastName, $timeStamp, $roles, $extraData);
        }

        throw new BadCredentialsException();
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername(['username' => $user->getUsername(), 'password' => $user->getPassword()]);
    }

    public function supportsClass($class)
    {
        return $class === 'K2WSBundle\Security\User\WebserviceUser';
    }

    public function getUserRoles($id)
    {
        return ['ROLE_USER'];
    }

    public function getUserExtraData($id)
    {
        return [];
    }

    public function getK2WSData()
    {
        return $this->data;
    }

    public function getK2WSCore()
    {
        return $this->core;
    }
}
