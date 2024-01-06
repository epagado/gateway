Epagado Gateway
=====

Este script te permitirá generar los formularios para la integración del gateway de pago de Epagado.

## Instalación

Añade las dependencias vía composer: 

```bash
composer require epagado/gateway
```

## Ejemplo de pago instantáneo

Este proceso se realiza para pagos en el momento, sin necesidad de confirmación futura (TransactionType = 0)

```php
# Incluye tu arquivo de configuración (copia config.php para config.local.php)

$config = require (__DIR__.'/config.local.php');

# Cargamos la clase con los parámetros base

$TPV = new Epagado\Gateway($config);

# Indicamos los campos para el pedido

$TPV->setFormHiddens(array(
    'MerchantData' => 'Televisor de 50 pulgadas',
    'Order' => '012121323',
    'Amount' => '568,25',
    'UrlOK' => 'http://dominio.com/direccion-todo-correcto/',
    'UrlKO' => 'http://dominio.com/direccion-error',
    'UrlResp' => 'http://dominio.com/direccion-control-pago'
));

# Imprimimos el pedido el formulario y redirigimos a la TPV

echo '<form action="'.$TPV->getPath().'" method="post">'.$TPV->getFormHiddens().'</form>';

die('<script>document.forms[0].submit();</script>');
```

Para realizar el control de los pagos, la Epagado se comunicará con el comercio a través de la url indicada en **UrlResp**.

Este script no será visible ni debe responder nada, simplemente verifica el pago.

Epagado siempre se comunicará con el comercio a través de esta url, sea correcto o incorrecto.

Podemos realizar un script (Lo que en el ejemplo sería http://dominio.com/direccion-control-pago) que valide los pagos de la siguiente manera:

```php
# Incluye tu arquivo de configuración (copia config.php para config.local.php)

$config = require (__DIR__.'/config.local.php');

# Cargamos la clase con los parámetros base

$TPV = new Epagado\Gateway($config);

# Realizamos la comprobación de la transacción

try {
    $datos = $TPV->checkTransaction($_POST);
    $success = true;
    $message = '';
} catch (Exception $e) {
    $datos = $TPV->getTransactionParameters($_POST);
    $success = false;
    $message = $e->getMessage();
}

# Actualización del registro en caso de pago (ejemplo usando mi framework)

$Db->update(array(
    'table' => 'tpv',
    'limit' => 1,
    'data' => array(
        'pagado' => $success,
        'mensaje' => $message,
        'fecha_pago' => date('Y-m-d H:i:s'),
        'variables' => json_encode($datos),
        'post' => json_encode($_POST)
    ),
    'conditions' => array(
        'id' => $datos['Ds_Order']
    )
));

die();
```

--------
