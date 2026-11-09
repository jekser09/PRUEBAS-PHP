<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

$path = __DIR__."/../data/orders.json";
$file_json = is_readable($path) ? file_get_contents($path):null;
$orders = json_decode($file_json,true);
$headers = array_keys($orders[0]);
// TODO: Implementar la solución de la Prueba Técnica 02
?>
<!---->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/static/CSS/index.css" type="text/css">
    <script src="/static/JS/index.js" defer></script>
    <title>Solucion</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <?php foreach($headers as $head):?>
                    <th><?= htmlspecialchars($head) ?></th>
                <?php endforeach;?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order):?>
                <tr class=<?= (float)$order['amount'] <= 0 ? "invalid":"" ?>>
                    <?php foreach($order as $value):?>
                        <td><?= htmlspecialchars((string)($value ?? '---')) ?></td>
                    <?php endforeach;?>
                </tr>
            <?php endforeach;?>
        </tbody>
        <tfoot>
            <tr>
                
            </tr>
        </tfoot>
    </table>  
</body>
</html>