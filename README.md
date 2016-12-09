# K2WSBundle
Symfony bundle to ease communication with K2 Web Service.

## Installation

### Include the bundle in your project
Download K2WSBundle master repository and put it inside your src directory.

### Enable the bundle
To start using the bundle, register the bundle in your application's kernel class:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new K2\K2WSBundle\K2WSBundle(),
        // ...
    );
}
```

### Configure the bundle
Configure connection to K2 Web Service:
```yaml
# app/config/config.yml
k2_ws:
    host: go.k2.cz # webservice host name
    name: restservicedemo # webservice instance name
    port: ~ # default is 80
    secure: ~ # if true => using https, default is false => using http
    username: DEMO3 # anonymous user login
    password: 1234 # anonymous user password
```

**Note:**
> K2 Web Service must be properly configured for authorization. Check your settings in web.config – anonymous key must be set to false. Webservice instance bindings must match host and webservice instance name in bundle config.

Configure symfony services:
```yaml
# app/config/services.yml
services:
    k2_ws.core:
        class: K2\K2WSBundle\Controller\CoreController
        arguments:
            - "%k2_ws.config%"
            - "@security.token_storage"

    k2_ws.data:
        class: K2\K2WSBundle\Controller\DataController
        arguments:
            - "@k2_ws.core"
```

**Note:**
> Make sure services.yml is imported in config.yml.

### Configure user authentication (optional)
If you would like to configure your app to use form login authenticator and web service user provider, please check out [security](SECURITY.md) documentation.

## That's it!
The bundle is installed. Check out the [usage section](USAGE.md) to find out how to use the bundle.
