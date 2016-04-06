# Configure security
K2WSBundle allows you to authenticate as standard K2 Web Service Users in your symfony application.

**Note:**
> Make sure your app is properly [configured](README.md) to use K2WSBundle.

## Configure symfony services
```yaml
    k2_ws.webservice_user_provider:
        class: K2WSBundle\Security\User\WebserviceUserProvider
        arguments:
            - "@k2_ws.core"
            - "@k2_ws.data"

    k2_ws.form_login_authenticator:
        class: K2WSBundle\Security\FormLoginAuthenticator
        arguments:
            - "@router"
