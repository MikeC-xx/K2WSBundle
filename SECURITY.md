# Configure security
K2WSBundle allows you to authenticate as standard K2 Web Service User in your symfony application.

User object holds this data:
```php
// K2WSBundle/Security/WebserviceUser.php

class WebserviceUser implements UserInterface, EquatableInterface
{
    /** ContactPersonId */
    private $id;

    /** LoginNameCalc */
    private $username;

    /** CFPass */
    private $password;

    /** Password salt, currently not used */
    private $salt;

    /** Name */
    private $firstName;

    /** Surname */
    private $lastName;

    /** TimeStamp */
    private $timestamp;

    /** User roles */
    private $roles;
```

When user is authenticated, you can access user object in Twig templates using app.user, for example:
```twig
<h2>You are logged in as: {{ app.user.firstName }} {{ app.user.lastName }}</h2>
```

**Note:**
> Before you continue, make sure your app is properly [configured](README.md) to use K2WSBundle.

## Configure symfony services
Add these services to your services.yml file.
```yaml
# app/config/services.yml
services:
  ...
  
    k2_ws.webservice_user_provider:
        class: K2WSBundle\Security\User\WebserviceUserProvider
        arguments:
            - "@k2_ws.core"
            - "@k2_ws.data"

    k2_ws.form_login_authenticator:
        class: K2WSBundle\Security\FormLoginAuthenticator
        arguments:
            - "@router"
            - homepage # default route name
```

**Note:**
> User is redirected to default route name after successful authentication if there is no URL set in the session.

## Configure provider, firewall and access control
Add a provider, encoder, main firewall and access control to your security.yml file.
```yml
# app/config/security.yml
security:
    providers:
        k2_ws:
            id: k2_ws.webservice_user_provider

    encoders:
        Symfony\Component\Security\Core\User\User: plaintext
    
    firewalls:
        ...
    
        main:
            anonymous: ~
            guard:
                authenticators:
                    - k2_ws.form_login_authenticator
            logout:
                path: security_logout
                target: security_login
  
    access_control:
        - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/secret-content, roles: ROLE_USER } # add your secret routes here
```
**Note:**
> Every K2 Web Service user has a ROLE_USER role by default.

## Create security controller
Create new security controller in your app:
```php
// src/AppBundle/Controller/SecurityController.php

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('K2WSBundle:Security:login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
        return new Response();
    }
}
```

**Note:**
> K2WSBundle provides default login form template. You can render your own template or override default template as long as input fields for username and password in your form are named "_username" and "_password".

## That's it
Your app is now configured to use K2 Web Service user authentication.
