# Usage
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

## Use data in template
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
    <caption>My Orders</caption>
    <thead>
      <th>Document ID</th>
      <th>Date of Issue</th>
      <th>Price</th>
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
          <td colspan="3">No orders were found.</td>
        </tr>
      {% endif %}
    </tbody>
  </table>
{% endblock %}
```
