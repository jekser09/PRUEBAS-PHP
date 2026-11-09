<?php
declare(strict_types = 1);

// 1. Cargar el autoloader de Composer para poder utilizar las clases de src/ (PSR-4)
require_once __DIR__ . '/../vendor/autoload.php';

use App\Logica;

// 2. Indicar al navegador o cliente HTTP (Postman/fetch) que la respuesta será JSON en UTF-8
header('Content-Type: application/json; charset=utf-8');

// 3. Extraer el verbo HTTP de la petición desde la superglobal $_SERVER ('GET', 'POST', 'PUT', 'DELETE')
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// 4. Extraer la URL completa solicitada por el cliente (ej. "/api/orders?status=completed&sort=1")
$rawUri = $_SERVER['REQUEST_URI'] ?? '/';

// 5. Separar únicamente la ruta (path) ignorando los parámetros "?..." (ej. "/api/orders")
$path = parse_url($rawUri, PHP_URL_PATH) ?: '/';

// 6. Normalizar la barra final para que "/api/orders/" y "/api/orders" coincidan igual
$path = rtrim($path, '/') ?: '/';

// 7. Instanciar la clase de negocio para consultar los datos
$logica = new Logica();

// 8. Enrutar la petición combinando el verbo HTTP ($method) y la ruta ($path)
switch (true) {

    // =========================================================================
    // ENDPOINT 1: GET /api/orders
    // Ejemplo de extracción de Query Params (?status=completed&category=Software&sort=1)
    // =========================================================================
    case $method === 'GET' && $path === '/api/orders':
        // $_GET contiene automáticamente todos los pares clave=valor que vienen después del "?" en la URL
        $status = $_GET['status'] ?? null;
        $category = $_GET['category'] ?? null;
        // filter_var convierte valores como "1", "true", "on" en un booleano real (true/false)
        $sort = filter_var($_GET['sort'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $orders = $logica->getOrders($status, $category, $sort);

        // Definir el código de estado HTTP 200 (OK)
        http_response_code(200);
        // Convertir el array asociativo de PHP a texto JSON y enviarlo al cliente
        echo json_encode([
            'ok' => true,
            'filters' => [
                'status' => $status,
                'category' => $category,
                'sort' => $sort,
            ],
            'count' => count($orders),
            'data' => $orders,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // ENDPOINT 2: GET /api/kpis
    // Ejemplo de endpoint de resumen sin parámetros
    // =========================================================================
    case $method === 'GET' && $path === '/api/kpis':
        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'data' => $logica->getKpis(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // ENDPOINT 3: GET /api/orders/{id} (ej. /api/orders/ORD-1001)
    // Ejemplo de extracción de parámetros dinámicos dentro de la ruta (Path Params)
    // =========================================================================
    case $method === 'GET' && preg_match('#^/api/orders/([A-Za-z0-9-]+)$#', $path, $matches) === 1:
        // $matches[0] tiene la ruta completa; $matches[1] tiene el valor capturado entre paréntesis: el ID
        $orderId = $matches[1];

        // Buscar la orden por su ID dentro del listado
        $found = array_values(array_filter(
            $logica->getOrders(null, null),
            fn(array $order): bool => $order['id'] === $orderId
        ));

        // Si no existe, responder con código HTTP 404 (Not Found)
        if (empty($found)) {
            http_response_code(404);
            echo json_encode([
                'ok' => false,
                'error' => "No se encontró la orden con ID {$orderId}",
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        http_response_code(200);
        echo json_encode([
            'ok' => true,
            'data' => $found[0],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // ENDPOINT 4: POST /api/orders
    // Ejemplo de lectura del cuerpo JSON (Request Body) enviado en una petición POST
    // =========================================================================
    case $method === 'POST' && $path === '/api/orders':
        // En PHP, el JSON enviado en el body de un POST/PUT no llega en $_POST,
        // sino que se lee del stream de entrada "php://input"
        $rawBody = file_get_contents('php://input') ?: '';
        $payload = json_decode($rawBody, true);

        // Validar que el JSON enviado sea válido
        if (!is_array($payload) || empty($payload['customer']) || empty($payload['amount'])) {
            http_response_code(400); // 400 Bad Request
            echo json_encode([
                'ok' => false,
                'error' => 'El cuerpo de la petición debe ser un JSON válido con customer y amount.',
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Responder con 201 Created simulando la creación del recurso
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'message' => 'Orden recibida correctamente',
            'received' => $payload,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // RUTA POR DEFECTO: 404 Not Found
    // Cuando ninguna combinación de verbo y ruta coincide
    // =========================================================================
    default:
        http_response_code(404);
        echo json_encode([
            'ok' => false,
            'error' => "Ruta no encontrada: {$method} {$path}",
        ], JSON_UNESCAPED_UNICODE);
        break;
}

