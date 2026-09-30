0. Propósito:
Construir un plugin de WordPress Open Source y ultra ligero para evaluar de forma integral el estado de seguridad, actualización y configuración de una instalación de WordPress.  El plugin debe responder de forma clara y directa:¿Está mi WordPress actualizado, seguro y correctamente configurado? 

1. Principios del proyecto 100% Nativo y Ligero: Integrado completamente con la interfaz nativa de WP-Admin (usando WP_List_Table y componentes/estilos nativos de WordPress), evitando assets pesados o librerías de terceros innecesarias.Open Source sin límites: Sin planes Pro, sin funciones bloqueadas, sin telemetría y sin requerir cuentas ni claves API.  Read-only by default: Muestra recomendaciones claras y accionables, pero no modifica archivos, base de datos ni configuraciones de forma automática.  Sin Overengineering: Código PHP simple, limpio y mantenible. Sin agentes de servidor, sin SaaS, sin microservicios.

2. Componentes principales:
El plugin se estructura internamente en tres partes esenciales:Plaintext  [ WP-Cron / Escaneo Manual ]
               │
               ▼
   [ Runner de Checks Locales ] ──► [ Consultas a WPVulnerability API (Cached) ]
               │
               ▼
  [ Health Score & Dashboard Nativo ]
  
3. Entorno Local de Desarrollo (Docker)
El plugin incluye un setup minimalista con Docker Compose en `docker/docker-compose.yml` para levantar rápidamente una instancia limpia de WordPress en desarrollo:

services:
  db:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: wordpress_root_db_pass
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress_db_password

  wordpress:
    image: wordpress:latest
    ports:
      - "8080:80"
    restart: always
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress_db_password
      WORDPRESS_DB_NAME: wordpress
    volumes:
      - .:/var/www/html/wp-content/plugins/sitevitals

4. Alcance del MVP
4.1. Diagnóstico de WordPress, Themes y PluginsWordPress: Versión instalada vs. última disponible y versión de PHP actual + estado de soporte.Plugins y Themes:Estado de actualizaciones pendientes.Detección de vulnerabilidades conocidas consultando la WPVulnerability API (API abierta, gratuita y sin necesidad de API Key).Configuraciones de Seguridad Clave:Estado de HTTPS.Estado de WP_DEBUG / WP_DEBUG_LOG.Estado de XML-RPC.Estado de ejecución de WP-Cron y Loopback requests.
4.2. Health Score UnificadoUn Score General único de 0 a 100.Descuentos claros basados en hallazgos (ej. -20 por vulnerabilidad crítica, -10 por versión de PHP fuera de soporte, -5 por plugin desactualizado).3 Estados de diagnóstico rápido:Verde (90–100): Saludable / Excelente.Amarillo (70–89): Requiere Atención.Rojo (0–69): Crítico.
4.3. Frecuencia de EscaneoEscaneo Manual: Botón en el Admin para ejecutar el diagnóstico bajo demanda.Escaneo Automático (WP-Cron): Ajustable por el usuario desde los ajustes (Diario, Semanal, Mensual o Desactivado).Optimización y Cache: Los resultados de vulnerabilidades y escaneos se guardan mediante Transients / Base de datos para evitar peticiones HTTP repetitivas al navegar por el Admin.

4.5 Capacidades y Caracteristicas:

Para delimitar claramente el producto y evitar que vuelva a crecer innecesariamente, dividiremos las características y capacidades en tres niveles:

    Capacidades Funcionales: Lo que el plugin hace concretamente.

    Capacidades Técnicas: Cómo lo hace internamente sin sobrecargar el servidor.

    Límites Explícitos (Fuera de Alcance): Lo que NO hará para mantenerlo ligero.

1. Capacidades Funcionales
A. Módulo de Diagnóstico (Local & Read-Only)

    Chequeo de WordPress Core: Detecta la versión de WordPress en uso vs. la versión estable actual y valida si está habilitada la actualización automática.
    Chequeo del Entorno PHP: Evalúa la versión de PHP activa y alerta si está fuera de ciclo de vida (EOL) o si tiene extensiones/parámetros críticos con valores inseguros (memory_limit, display_errors).
    Chequeo de Archivos/Configuración de Seguridad:

        Estado de WP_DEBUG y WP_DEBUG_LOG (para evitar fuga de información).
        Estado de XML-RPC (activo o expuesto).
        Estado y forzado de conexión segura HTTPS / SSL.
        Estado de edición de archivos mediante el admin (DISALLOW_FILE_EDIT).

    Chequeo de Salud del Sistema (Reliability):
        Validación de ejecución correcta de WP-Cron.
        Validación de llamadas internas (Loopback Requests).

B. Módulo de Seguridad & Vulnerabilidades (API Abierta)

    Escaneo de Plugins: Inspección de la lista de plugins instalados (activos e inactivos) para verificar:

        Si tienen actualizaciones disponibles.
        Si existen vulnerabilidades conocidas (CVEs) consultando la WPVulnerability API (100% libre, sin requerir API Key).

    Escaneo de Themes: Verificación equivalente para temas activos e inactivos (versión desactualizada o vulnerabilidades reportadas).

C. Algoritmo de Health Score Unificado

    Puntuación Única (0 a 100): Algoritmo transparente que calcula la salud global restando puntos según la severidad del hallazgo.

        Ejemplo: -20 Vulnerabilidad crítica de plugin, -10 Versión PHP sin soporte, -5 Plugin desactualizado, -5 Debug mode activado en producción.

    Indicador Visual Directo (3 Niveles):

        90 – 100: Saludable.

        70 – 89: Requiere Atención.

        0 – 69: Estado Crítico.

D. Programación de Escaneos y Notificaciones

    Ejecución Bajo Demanda: Botón "Escanear ahora" en la interfaz del admin.

    Ejecución Programada (WP-Cron): Frecuencia configurable por el administrador (Diario, Semanal, Mensual o Desactivado).

    Notificaciones Básicas por Correo: Enviar un resumen por email al administrador solo si la puntuación baja de un umbral o se detecta una vulnerabilidad crítica tras el escaneo programado.

2. Capacidades Técnicas & UX
A. Interfaz y Experiencia de Usuario (WP-Native)

    Integración Nativa con WP-Admin: Pantalla dedicada usando estilos nativos de WordPress, cajas de metas (postboxes), alertas de aviso (notice-error, notice-warning) y tablas estándar (WP_List_Table).

    Tablero de Control de Impacto Rápido: Dashboard principal con el número de score central, número de vulnerabilidades activas y lista de recomendaciones con acción directa (ej. enlace directo a la pestaña de actualizaciones de WP).

B. Rendimiento y Caché Intercalada (Zero-Overhead)

    Caché vía Transients: Las respuestas de la API de vulnerabilidades y los resultados de escaneos pesados se guardan temporalmente en base de datos (transients de WP, por defecto 12 horas).

    Carga Bajo Demanda: El plugin solo ejecuta consultas a la API externa o revisiones profundas al presionar el botón de escaneo o mediante la tarea programada; nunca ralentiza la navegación cotidiana en el admin.

3. Límites Explícitos (Fuera de Alcance)

Para garantizar la mantenibilidad y ligereza del plugin, se excluyen deliberadamente:

    ❌ Sin Agentes de Servidor: No intentará inspeccionar recursos del sistema operativo a nivel SO (uso de CPU en tiempo real, consumo de RAM física, procesos de Linux).

    ❌ Sin Corrección Automática (Auto-Fix): No modificará código, no actualizará plugins ni alterará archivos .htaccess o wp-config.php automáticamente.

    ❌ Sin Conexiones Externas Complejas: Sin tableros SaaS externos, sin necesidad de crear cuenta de usuario, sin microservicios, sin Webhooks complejos.

    ❌ Sin Servidores Intermedios: El plugin se comunica directamente con la API pública de vulnerabilidades desde el servidor del usuario.

5. Estructura del Código:
Una estructura limpia y estándar de plugin de WordPress:Plaintextsitevitals/
├── assets/                  # CSS/JS nativo mínimo
├── docker/docker-compose.yml # Entorno de desarrollo local aislado
├── includes/
│   ├── class-checker.php    # Ejecutor principal de checks
│   ├── class-score.php      # Lógica de cálculo del Health Score (0-100)
│   ├── class-vulnerability.php # Cliente para WPVulnerability API (con Cache)
│   ├── class-cron.php       # Programación de escaneos (WP-Cron)
│   └── checks/              # Checks individuales simples
├── admin/
│   ├── class-admin-page.php # Renderizado UI nativo en WP-Admin
│   └── views/               # Plantillas de la interfaz
├── tests/                   # Pruebas automatizadas (PHPUnit)
├── .github/
│   └── workflows/ci.yml     # GitHub Actions (PHPCS + PHPUnit)
├── README.md
└── sitevitals.php           # Archivo principal del plugin

6. Calidad y CI/CD (Integración Continua)Pipeline simplificado en GitHub Actions (ci.yml) que se ejecuta en cada Push o Pull Request:Estándares de Código (PHPCS): Verificación con WordPress-Coding-Standards.Pruebas Unitarias (PHPUnit): Tests básicos para la lógica de cálculo del score, comparación de versiones y parsing de respuestas de la API.

7. Fases de DesarrolloFase 0: Estructura & Setup InicialCrear la estructura de carpetas e inicializar el repositorio.Agregar docker/docker-compose.yml para el desarrollo local.Configurar PHPCS (WordPress Coding Standards) y PHPUnit.

Fase 1: Checks LocalesImplementar los checks básicos de WP, PHP, HTTPS, XML-RPC y Debug Mode.
Fase 2: Integración de Vulnerabilidades Conectar con WPVulnerability API.Implementar sistema de caché con Transients de WP.
Fase 3: Health Score & UI NativaAlgoritmo transparente de cálculo del Score (0-100).Construir la vista nativa en WP-Admin con tablas e indicadores claros.
Fase 4: Programación & CI/CDIntegrar selector de frecuencia de escaneo (WP-Cron: Diario/Semanal/Mensual).Activar el flujo de GitHub Actions para linter y tests.

8.Filosofía Simplificada:
"Un plugin directo, súper rápido e instalado en 30 segundos, que le dice al usuario exactamente qué ajustar en su WordPress sin consumir recursos del servidor."
