# Usage
You can use K2WSBundle to retrieve, post and put data to K2 Web Service.

K2WSBundle comes with Czech (cs) and English (en) translations for the examples given in this documentation. You have to enable translator in your config.yml file:

```yml
# app/config/config.yml
parameters:
    locale: cs # set this to cs or en
    ...

framework:
    ...

    translator: { fallbacks: ["%locale%"] }
```

## Retrieve data
You can retrive data using k2_ws.data service in your controllers. Data is serialized into K2WSBundle\Entity\DataObject class.

First, get service from container:
```php
// src/AppBundle/Controller/YourController.php
public function myAction()
{
    $K2WSData = $this->get('k2_ws.data');
}
```

Next, choose what kind of data you want to get. Use can use these methods:

1. getDataObjectList($className, $params = null)
1. getDataObject($className, $primaryKey, $params = null)

Argument $params is an key-value array.

For example, retrieve a list of orders where ContactPersonId is equal to currently logged in user:

```php
// src/AppBundle/Controller/YourController.php
public function myAction()
{
    $K2WSData = $this->get('k2_ws.data');
    
    $userId = $this->getUser()->getId();
    
    $orders = $K2WSData->getDataObjectList(
        'Zak',
        [
            'fields' => ['DocumentIdentificationCalc', 'DateOfIssue', 'PriceCCalc'],
            'conditions' => ['ContactPersonId;EQ;' . $userId]
        ]
    );
```

### Use data in template
After your data has been retrieved, you can pass it to your template:

```php
// src/AppBundle/Controller/DefaultController.php
public function homepageAction()
{
    $K2WSData = $this->get('k2_ws.data');
    
    $userId = $this->getUser()->getId();
    
    $orders = $K2WSData->getDataObjectList(
        'Zak',
        [
            'fields' => ['DocumentIdentificationCalc', 'DateOfIssue', 'PriceCCalc'],
            'conditions' => ['ContactPersonId;EQ;' . $userId]
        ]
    );
    
    return $this->render('default/index.html.twig', ['orders' => $orders]);
}
```

In your template, you can use the data like this:
```twig
{# app/Resources/views/default/index.html.twig #}

{% extends 'base.html.twig' %}

{% block body %}
  <table>
    <caption>{{ 'orders.my'|trans }}</caption>
    <thead>
      <th>{{ 'fields.document_identification'|trans }}</th>
      <th>{{ 'fields.date_of_issue'|trans }}</th>
      <th>{{ 'fields.price'|trans }}</th>
    </thead>
    <tbody>
      {% if orders.Items|length > 0 %}
        {% for order in orders.Items %}
          <tr>
            <td>{{ order.FieldValues.DocumentIdentificationCalc }}</td>
            <td>{{ order.FieldValues.DateOfIssue|date('j. n. Y') }}</td>
            <td>{{ order.FieldValues.PriceCCalc|number_format(2, ',', ' ') }} Kč</td>
          </tr>
        {% endfor %}
      {% else %}
        <tr>
          <td colspan="3">{{ 'orders.none_found'|trans }}</td>
        </tr>
      {% endif %}
    </tbody>
  </table>
{% endblock %}
```
## Post data
> TODO

## Put data
> TODO
