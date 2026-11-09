# PRUEBAS-PHP — Pruebas Técnicas en PHP

Repositorio de **proyectos PHP independientes y autocontenidos** diseñado para practicar entrevistas técnicas, katas de código, retos de arquitectura y resolución de problemas algorítmicos. Cada directorio representa un reto aislado con sus propias dependencias, configuración y suite de pruebas.

## Guía Rápida

1. **Ingresar al directorio de la prueba:**
   ```bash
   cd <carpeta-de-la-prueba>
   ```
2. **Instalar dependencias (si incluye `composer.json`):**
   ```bash
   composer install
   ```
3. **Ejecutar la suite de pruebas o el script principal:**
   ```bash
   # Con script configurado en Composer / PHPUnit / Pest
   composer test
   # O ejecutar un script directamente
   php src/index.php
   ```

## Estructura del Repositorio

Cada prueba técnica vive en su propia carpeta de primer nivel para evitar colisiones de dependencias, versiones de PHP o herramientas entre ejercicios:

```text
PRUEBAS-PHP/
├── <reto-01-slug>/
│   ├── src/                # Código de producción / solución
│   ├── tests/              # Pruebas automatizadas (PHPUnit / Pest)
│   ├── composer.json       # Dependencias específicas y autoloading PSR-4
│   └── README.md           # Enunciado del problema, restricciones y decisiones de diseño
├── <reto-02-slug>/
│   └── ...
├── .gitignore              # Reglas globales de exclusión (vendor, cachés, entornos)
└── README.md               # Guía general e índice de pruebas
```

## Arquitectura y Convenciones

| Área | Convención |
|------|------------|
| **Aislamiento** | Cada carpeta es un proyecto autónomo, sin `vendor/` compartido en la raíz ni acoplamiento global. |
| **Tipado Estricto** | Incluir `declare(strict_types=1);` al inicio de todos los archivos PHP. |
| **Estándares** | Aplicar **PSR-4** para autoloading y **PSR-12** para estilo de código. |
| **Diseño** | Priorizar arquitectura limpia/hexagonal, principios SOLID y unidades pequeñas y testeables. |
| **Pruebas** | Incluir pruebas unitarias o de integración automatizadas (`PHPUnit` o `Pest`) en cada reto no trivial. |

## Índice de Pruebas

| # | Prueba | Enfoque | Stack / Herramientas | Estado |
|---|--------|---------|----------------------|--------|
| _01_ | _Por definir_ | _Algoritmos / Modelado de Dominio / API_ | _PHP 8.x, PHPUnit_ | _Pendiente_ |

## Lista de Verificación (Por Prueba)

Antes de dar por finalizada cualquier prueba técnica, verificar:

- [ ] `declare(strict_types=1);` está presente en todos los archivos `.php`.
- [ ] La solución separa la lógica de dominio/negocio de la entrada/salida o infraestructura.
- [ ] El flujo principal y los casos borde están cubiertos por pruebas automatizadas.
- [ ] La carpeta incluye su propio `README.md` con el enunciado y las instrucciones de ejecución.
- [ ] No quedan dependencias sin usar ni código muerto.

## Siguiente Paso: Crear una Nueva Prueba

Crear un nuevo directorio e inicializar el entorno con Composer:

```bash
mkdir <nombre-del-reto> && cd <nombre-del-reto>
composer init --name="pruebas-php/<nombre-del-reto>" --autoload="src/" -n
composer require --dev phpunit/phpunit
```
