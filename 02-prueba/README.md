# Prueba Técnica 02: Dashboard de Órdenes, KPIs de Negocio y Filtrado Interactivo

Construir un panel web en PHP 8.2+ que procese un lote de órdenes de comercio electrónico desde JSON, aplique reglas de validación de datos y exponga métricas clave (KPIs) junto con una tabla filtrable por parámetros HTTP `GET`.

## Inicio rápido

1. Instalar y generar el cargador automático de clases (PSR-4):
   ```bash
   composer dump-autoload
   ```
2. Iniciar el servidor web integrado de PHP apuntando al directorio `public/`:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Abrir `http://localhost:8000` en el navegador para visualizar el panel.

## Contexto y Arquitectura

El sistema recibe una exportación cruda de órdenes en `data/orders.json`. Como ocurre en integraciones reales con pasarelas de pago o migraciones legadas, el archivo contiene registros con anomalías y texto sin sanitizar ingresado por usuarios.

| Componente | Ruta | Responsabilidad |
|------------|------|-----------------|
| Origen de datos | `data/orders.json` | Lote de 20 órdenes con estados, categorías, montos y casos borde. |
| Lógica de dominio | `src/` (`namespace App\`) | Lectura de datos, validación de reglas de negocio, cálculo de KPIs y filtrado. |
| Punto de entrada y vista | `public/index.php` | Captura de filtros `$_GET`, invocación de servicios y renderizado HTML seguro. |

## Requerimientos Funcionales

### 1. Lectura y Validación de Datos

- Leer y decodificar el archivo `data/orders.json` manejando errores de lectura o de formato JSON.
- **Regla de negocio para órdenes inválidas**: Cualquier registro con `amount <= 0` (monto cero o negativo) se considera corrupto o inválido.
- Los registros inválidos (`amount <= 0`) **no deben incluirse** en los cálculos de ingresos ni mostrarse como órdenes válidas en la tabla principal, pero **sí deben contabilizarse** en el indicador de registros inválidos detectados.

### 2. KPIs de Negocio

Calcular y mostrar en la parte superior del dashboard los siguientes indicadores sobre las órdenes válidas (`amount > 0`):

| Indicador | Regla de cálculo |
|-----------|------------------|
| **Total recaudado** | Suma de `amount` únicamente de las órdenes válidas con `status === "completed"`. |
| **Ticket promedio** | Promedio de `amount` de las órdenes válidas con `status === "completed"` (manejar división por cero si no hubiera resultados). |
| **Órdenes por estado** | Conteo de órdenes válidas agrupadas por cada `status` (`completed`, `pending`, `cancelled`, `refunded`). |
| **Registros inválidos detectados** | Cantidad total de registros en el JSON cuyo `amount <= 0`. |

### 3. Filtros Interactivos por GET (`$_GET`)

Implementar un formulario HTML (`method="GET"`) que permita refinar el listado de órdenes válidas sin perder la selección activa al recargar:

- **Filtro por estado (`status`)**: Opciones para `Todos`, `completed`, `pending`, `cancelled` y `refunded`.
- **Filtro por categoría (`category`)**: Opciones para `Todas`, `Electronics`, `Software`, `Hardware` y `Services`.
- **Ordenamiento por monto**: Opción para ordenar los resultados por `amount` de forma descendente (de mayor a menor).

### 4. Tabla HTML de Órdenes

Renderizar una tabla con las órdenes válidas que cumplan los filtros activos:

- Mostrar `id`, `customer`, `email`, `category`, `amount`, `status`, `created_at` y `notes` (si `notes` es `null`, mostrar un indicador neutro como `-` o `Sin notas`).
- Formatear los montos numéricos con `number_format()` (por ejemplo, `$1,249.99`).
- **Seguridad XSS**: Escapar obligatoriamente todos los valores dinámicos impresos en el HTML utilizando `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.

## Casos Borde Incluidos en el Dataset

| Caso | Qué evalúa |
|------|------------|
| Orden con `amount: -150.00` y `status: "completed"` | Que el cálculo de ingresos completados excluya montos negativos aunque el estado sea `completed`. |
| Orden con `amount: 0` | Que el filtro de integridad descarte montos en cero y los sume al contador de registros inválidos. |
| Nota con `<script>alert('xss')</script> Reclamo urgente & prioritario` | Que la vista neutralice inyecciones HTML/JavaScript mediante `htmlspecialchars()`. |
| Campos `notes: null` y fechas idénticas | Manejo seguro de tipos anulables (`?string`) y estabilidad en el ordenamiento. |

## Checklist de Evaluación Técnica

- [ ] Uso de `declare(strict_types=1);` en todos los archivos PHP.
- [ ] Clases organizadas en `src/` bajo el namespace `App\` cargadas mediante PSR-4 (`vendor/autoload.php`).
- [ ] Tipado explícito en propiedades, parámetros y tipos de retorno.
- [ ] Separación clara entre la lógica de negocio/datos (`src/`) y la capa de presentación (`public/index.php`).
- [ ] Exclusión efectiva de registros con `amount <= 0` en los KPIs financieros y en la tabla de órdenes válidas.
- [ ] Funcionamiento combinado de los filtros `status`, `category` y ordenamiento descendente por `amount` vía `$_GET`.
- [ ] Salida HTML libre de vulnerabilidades XSS mediante `htmlspecialchars()`.

## Siguiente paso

Crear las clases de dominio o servicios dentro de `src/` e integrar la vista del panel en [`public/index.php`](file:///C:/Users/jekse/OneDrive/Desktop/proyectos/PRUEBAS-PHP/02-prueba/public/index.php).
