<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use App\Logica;

$status = $_GET['status'] ?? null;
$category = $_GET['category'] ?? null;
$sort = isset($_GET['sort']); // true si tildó el checkbox,
$logica = new Logica();
$orders = $logica->getOrders(status:$status,category:$category,sort:$sort);
$headers = !empty($orders) ? array_keys($orders[0]) : [];
$kpis = $logica->getKpis();

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
    <form method="GET">
    <select name="status">
        <option value="">Todos los estados</option>         
        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option> 
        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="refunded" <?= $status === 'refunded' ? 'selected' : '' ?>>Refunded</option>
        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
  
    <select name="category">
        <option value="">Todas las categorías</option>      
        <option value="Electronics" <?= $category === 'Electronics' ? 'selected' : ''?>>Electronics</option>
        <option value="Software" <?= $category === 'Software' ? 'selected' : ''?>>Software</option>
        <option value="Hardware" <?= $category === 'Hardware' ? 'selected' : ''?>>Hardware</option>
        <option value="Services" <?= $category === 'Services' ? 'selected' : ''?>>Services</option>
    </select>
  
    <label>
        <input type="checkbox" name="sort" value="1" <?= $sort ? 'checked' : '' ?>>
        Mayor a menor monto
    </label>
  
    <button type="submit">Filtrar</button>
    <a href="/">Limpiar</a>
    </form>
    <div id="container-kpis">
        <div id="total-amount" class="container-kpi">
            <h3>Total Recaudado:</h3>
            <p><?= "$".$kpis['Total Recaudado'] ?></p>
        </div>
        <div id="avg-amount" class="container-kpi">
            <h3>Average amount:</h3>
            <p><?= "$".$kpis['Ticket Promedio'] ?></p>
        </div>
        <div id="total-amount" class="container-kpi">
            <h3>Total Status:</h3>
            <?php foreach($kpis['Ordenes por estado'] as $key => $value):?>
                <p><?= ucfirst($key) ?>: <?= $value ?></p>
            <?php endforeach;?>
        </div>
        <div id="total-invalids" class="container-kpi">
            <h3>Invalid records:</h3>
            <p><?= $kpis['Registros invalidos'] ?></p>
        </div>
    </div>
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
                    <?php 
                        if(is_numeric($value)){
                            $value = "$".number_format($value,2);
                        }
                        $data = (string)($value ?? '---')
                    ?>
                    <td><?= htmlspecialchars($data) ?></td>
                    <?php endforeach;?>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</body>
</html>