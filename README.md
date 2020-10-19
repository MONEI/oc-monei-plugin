# MONEI Payment Gateway plugin

### Installation guide

Make sure monei/monei-php-sdk package is installed. 
If it wasn't installed automatically: add **monei/monei-php-sdk** to the composer.json of your project.

```$xslt
{
    "require": [
     ...
         "monei/monei-php-sdk": "0.1.0",
    ],

```

After that execute below at the root of your project.
```
composer update
```

### Setting up the payment method

In Settings > MONEI > MONEI Settings add your production/test API key.

## PaymentForm component

Collecting user details to process the order.

Step 1: you create new instance of MONEI\MONEI\Models\Order model any way you want in your code. 
For example in php code section in CMS page:

```$xslt
<?php
use MONEI\Monei\Models\Order;
function onStart() {
    $order = new Order();
    $order->total = 1000;
    $this['order'] = $order;
}
?>
```

Step 2: Place MONEIPaymentForm component in your CMS page. You need to have the Order instance to proceed with the payment.
Specify complete and cancel pages.

```html
[MONEIPaymentForm]
url_cancel = "payment_page"
url_complete = "complete_page"
==
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Pay for order</h3>
    </div>
    <div class="panel-body">
        <form data-request="MONEIPaymentForm::onSendRequest"
              data-request-data="{{ order and order.id ? ',order_id: '~order.id : ''}}">
            <div class="form-group">
                <input class="form-control" name="amount" placeholder="Order amount in EUR" />
            </div>
            <div class="form-group">
                <input class="form-control" name="name" placeholder="Customer Name" />
            </div>
            <div class="form-group">
                <input class="form-control" name="email" placeholder="Customer E-mail" />
            </div>
            <div class="form-group">
                <input class="form-control" name="phone" placeholder="Customer Phone" />
            </div>
            <button
                class="btn btn-success"
                id="submit_monei_request">Send Request to MONEI</button>
        </form>
    </div>
</div>
```

Step 3: create complete page and place MONEISuccessPage there. Point the  from Step 2 here.
If the payment goes well, you'll have {{ MONEISuccessPage.getPayment }} and 
{{ MONEISuccessPage.getOrder }} variables to access all payment current data.

````html
title = "Complete page"
url = "/complete_page"
layout = "default"

[MONEISuccessPage]
==
<div class="jumbotron title-js">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h1>Complete page</h1>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div>Order success!</div>
    <div>order: {{ MONEISuccessPage.getOrder.id }}</div>
    <div>order amount: {{ MONEISuccessPage.getOrder.total / 100 }} EUR</div>
</div>

````

## License

© 2019, under [GNU GPL v3](https://opensource.org/licenses/GPL-3.0).

Developed by [MONEI](https://monei.net/).