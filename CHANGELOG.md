# Changelog

## [0.1.2] - 2026-09-18

### Cambios (rebranding sitevitals)

- Renombrado del plugin `wpvitals` → `sitevitals` (slug y nombre libres en wp.org, resuelve avisos `trademarked_term` de Plugin Check).
- Eliminada la llamada `load_plugin_textdomain()` (desaconsejada); el textdomain local se carga por convención de WP 6.7+.
- Añadidos `composer.json` y `composer.lock` al paquete distribuible (resuelve `missing_composer_json_file`).
- Actualizado `readme.txt` a `Tested up to: 7.1`.

## [Unreleased]

### Interfaz

- Sustituidos los iconos genéricos del dashboard por trazos inline de Tabler Icons, con licencia MIT.
- Corregido el orden de los volúmenes Docker para que Plugin Check detecte el slug `sitevitals` y su text domain correspondiente.
- Aislado el `wp-content/` local dentro del montaje Docker para que los plugins de desarrollo no se analicen como parte de SiteVitals.
- Documentada la excepción PHPCS del filtro core `xmlrpc_enabled`.

### Etapa 0 - Alcance y decisiones iniciales

- [x] Confirmado que el producto será un plugin de WordPress Open Source.
- [x] Confirmado el enfoque read-only por defecto.
- [x] Confirmado que no habrá planes Pro ni funciones bloqueadas.
- [x] Confirmado que no se usará telemetría.
- [x] Confirmado que no se requerirán cuentas ni API keys.
- [x] Confirmado que no se modificarán automáticamente archivos, base de datos ni configuraciones.
- [x] Confirmado que no se usarán agentes de servidor, SaaS ni microservicios.
- [x] Definidos el nombre y slug del plugin como `sitevitals`.
- [x] Definido WordPress `6.5` como versión mínima soportada.
- [x] Definido PHP `7.4` como versión mínima soportada.
- [x] Establecido el foco de compatibilidad en PHP `7.x` y `8.x`.
- [x] Definido el correo del administrador del sitio como destinatario por defecto.
- [x] Definida la posibilidad de personalizar el correo destinatario.
- [x] Definido el ajuste “Enviar resultados por correo” para controlar el envío.
- [x] Definidos inglés y español como idiomas soportados.
- [x] Definido el idioma del sitio como fuente para seleccionar las traducciones.
- [x] Definida la duración por defecto de la caché en 12 horas.

Decisiones añadidas:

- [x] Definido el ajuste “Enviar resultados por correo” como activado por defecto.
- [x] Definido el envío tras escaneos manuales ejecutados por el usuario.
- [x] Definido el envío tras todos los escaneos programados.
- [x] Definido que el correo incluirá el score y un resumen corto del diagnóstico.

### Etapa 1 - Estructura y entorno local

- [x] Creado el archivo principal `sitevitals.php` con cabecera estándar de WordPress y protección contra acceso directo.
- [x] Creada la estructura de directorios (`includes/`, `includes/checks/`, `admin/`, `admin/views/`, `assets/`, `tests/`, `.github/workflows/`).
- [x] Creado `docker/docker-compose.yml` con los servicios MySQL 8.0 y WordPress expuesto en el puerto 8080.
- [x] Configurado el volumen del plugin para montarse dentro de `wp-content/plugins/sitevitals`.
- [x] Desplegado y verificado el entorno local con Docker Compose.
- [x] Completada la instalación inicial de WordPress y activado el plugin desde WP-Admin.
- [x] Verificado que el plugin no genera errores PHP al activarse.
- [x] Documentados los procedimientos para detener y reiniciar el entorno local en `README.md`.

### Etapa 2 - Calidad base y herramientas de desarrollo

- [x] Creado `composer.json` con `type: wordpress-plugin`, soporte PHP `>=7.4` y autoload PSR-4 (`SiteVitals\` → `includes/`, `SiteVitals\Admin\` → `admin/`).
- [x] Añadidas dependencias de desarrollo: PHPUnit `^9.6`, PHPCS `^3.7` y WPCS `^3.0`.
- [x] Cargado `vendor/autoload.php` desde `sitevitals.php` de forma segura (guard + cleanup).
- [x] Configurado `phpcs.xml.dist` con el estándar `WordPress` y exclusiones (`vendor/`, `docker/`, `tests/`, `node_modules/, `.github/`).
- [x] Configurado `phpunit.xml.dist` standalone con bootstrap `tests/bootstrap.php` y smoke test inicial (2 tests en verde).
- [x] Ajustado el servicio `composer` en `docker/docker-compose.yml` para ejecutarse con el usuario local (`UID/GID`) y evitar archivos propiedad de root.
- [x] Creado `.gitignore` (vendor, composer.lock, cobertura, caches e IDE).
- [x] Verificado lint (PHPCS) sin errores y tests (PHPUnit) en verde.
- [x] Documentados en el README los comandos de instalación, lint y tests.

Extras añadidos en la Etapa 2:

- [x] Añadido `.github/workflows/ci.yml` (GitHub Actions): matriz PHP 7.4 y 8.3, PHPCS + PHPUnit en cada push y PR.
- [x] Añadido `.editorconfig` para consistencia de estilo entre editores.
- [x] Verificado que el sitio WordPress sigue cargando correctamente con el autoload activo.

Actualización de versiones (14 sep 2026):

- [x] CI: `actions/checkout@v4` → `v7` y `actions/cache@v4` → `v6` (acciones nodo 20 deprecadas). `setup-php@v2` vigente.
- [x] Matriz de PHP en CI: `8.3` → `8.5` (estable más reciente).
- [x] PHPCS: `^3.7` → `^3.13.6` (incompatibilidad de WPCS 3.4.1 con PHPCS 4; incluye fix CVE-2026-67434).
- [x] WPCS: `^3.0` → `^3.4.1` (instalada: 3.4.1).
- [x] PHPUnit se mantiene en `^9.6` (última compatible con el PHP 7.4 del stack base; instalada: 9.6.36).
- [x] `phpcs.xml.dist`: `testVersion` actualizado a `7.4-8.5`.
- [x] MySQL 8.0 se mantiene según decisión (no tocar DB).

### Etapa 3.1 - Infraestructura de checks

- [x] Creado `SiteVitals\Checker` (`includes/Checker.php`): registra checks (id únicos) y ejecuta todos aisladamente.
- [x] Creado `SiteVitals\Result` (`includes/Result.php`): value object inmutable con id, título, severidad, valor detectado, recomendación y puntos descontados (0-100), con validación estricta.
- [x] Creado el contrato `SiteVitals\Checks\CheckInterface` + base `AbstractCheck` (factory de resultados) en `includes/Checks/` (namespace PSR-4; sustituye la carpeta `includes/checks/` del RUNBOOK).
- [x] Checks de solo lectura por contrato (no escriben archivos, DD o configuración).
- [x] Errores individuales convertidos en `Result` con severidad `error` y 0 puntos, sin interrumpir el escaneo.
- [x] `Result` y `Checker` verificados con PHPUnit: 13 tests, 33 assertions.
- [x] WPCS: exclusión justificada de `WordPress.Files.FileName` (nomenclatura PSR-4 elegida) y escape de mensajes de excepción con `esc_html()`.
- [x] Verificado el sitio WordPress sin errores PHP tras la nueva infraestructura.

### Etapa 3.2-3.4 - Checks locales de Core, PHP y seguridad

- [x] `WordPressVersionCheck` (`core/version`): versión instalada vs. estable (transient `update_core`), actualización pendiente y estado de auto-updates.
- [x] `PhpVersionCheck` (`php/version`): versión PHP activa frente a tabla curada EOL (fuente: php.net/supported-versions), con estados ok / EOL próximo / EOL y severidad desconocida.
- [x] `PhpMemoryCheck` (`php/memory`): `memory_limit` vs. mínimo recomendado de WordPress (128M), soporta `-1` (ilimitado).
- [x] `PhpDisplayErrorsCheck` (`php/display_errors`): detección de `display_errors` activo (`On`/`1` vs `Off`/`stderr`).
- [x] `PhpExtensionsCheck` (`php/extensions`): revisión de las extensiones PHP requeridas por WordPress.
- [x] `HttpsCheck` (`security/https`): `is_ssl()` + `FORCE_SSL`/`FORCE_SSL_ADMIN`; distingue petición segura de HTTPS forzado tras proxy.
- [x] `DebugCheck` (`security/debug`) y `DebugLogCheck` (`security/debug_log`): `WP_DEBUG`, `WP_DEBUG_DISPLAY` y `WP_DEBUG_LOG` inseguros en producción.
- [x] `XmlRpcCheck` (`security/xmlrpc`) y `FileEditCheck` (`security/file_edit`): `xmlrpc_enabled` y `DISALLOW_FILE_EDIT`.
- [x] Checks con fuentes inyectables (`callable`) para tests standalone sin WordPress; por defecto leen del sitio.
- [x] Todos los checks con recomendaciones accionables traducidas (`sitevitals`) y puntos descontados coherentes con el Health Score (5/10).
- [x] 43 tests / 93 assertions en verde; PHPCS (WordPress) limpio; site sin errores PHP.
- [x] Todo futuro añadido: workflow de mantenimiento de la tabla EOL de PHP.

### Etapa 3.5 - Salud y fiabilidad

- [x] `CronCheck` (`system/cron`): detecta `DISABLE_WP_CRON` y comprueba eventos relevantes del Core (`wp_get_scheduled_event`), con recomendaciones para ambos fallos.
- [x] `LoopbackCheck` (`system/loopback`): petición loopback segura con `wp_remote_get` (timeout 5s, blocking) a `home_url`, detectando timeout/conexión bloqueada sin bloquear el escaneo.

### Etapa 4 - Plugins, temas y actualizaciones

- [x] Nueva `MultiCheckInterface` (`run_many(): Result[]`) y soporte en `Checker::run_all` (aplana resultados y aísla errores, como los checks simples).
- [x] `PluginsUpdatesCheck` (`components/plugins`) y `ThemesUpdatesCheck` (`components/themes`): leen las transients nativas `update_plugins`/`update_themes` y generan **un Result por componente desactualizado** (`plugin/{slug}`, `theme/{slug}`, -5 puntos) con enlace a `update-core.php`.
- [x] Verificación completa: 57 tests / 139 assertions en verde, PHPCS limpio, site sin errores PHP.

### Mejoras DAO/preparación de fases 6-7

- [x] Nuevo `SiteVitals\ScoreDiscounts` con los niveles base de descuento (`MINOR` = 5, `MAJOR` = 10); sustituye los puntos mágicos de todos los checks (será la base de la tabla por tipo de la sección 6).
- [x] Nuevo `SiteVitals\Checks\LocalChecks::all()`: registro único de los 14 checks locales implementados, punto de entrada para el flujo de escaneo (sección 7).
- [x] `HttpsCheck` ahora detecta también el esquema `https` de `siteurl` (además de `FORCE_SSL*`), cerrando el item del todo "configuraciones de WordPress que indiquen HTTPS".
- [x] Verificación completa: 61 tests / 149 assertions en verde, PHPCS limpio, site sin errores PHP.

### Etapa 5 - Vulnerabilidades y caché

- [x] `SiteVitals\Vulnerability` (`includes/Vulnerability.php`): value object inmutable con severidad normalizada, puntos según severidad y rango `operator` (método `affects()` con `version_compare`) para decidir en cliente si una versión instalada está afectada.
- [x] `SiteVitals\VulnerabilityClient` (`includes/VulnerabilityClient.php`): WPVulnerability API (`https://www.wpvulnerability.net`), transporte HTTP y caché inyectables como callables (por defecto `wp_remote_get` + transients de WordPress), endpoints `core/{version}/`, `plugin/{slug}/` y `theme/{slug}/`, parse del envelope común.
- [x] Severidad extraída de `impact.{cvss4,cvss3,cvss2,cvss}.severity` (incluye letras legacy c/h/m/l/n); si la API no puntúa CVSS se usa "media" como valor conservador.
- [x] Los fallos de la API (error, HTTP no 200, timeout, JSON inválido) devuelven consulta vacía (0 puntos) y no detienen el escaneo; no se cachean errores, solo respuestas válidas (incluidas las vacías).
- [x] Caché en transients por componente: core por versión, plugins y temas por slug (la API no filtra por versión; el filtrado local se aplica también al leer de caché). TTL por defecto 12 horas; soporte de `refresh` para invalidarla en escaneos manuales.
- [x] `SiteVitals\VulnerabilityQuery` (`includes/VulnerabilityQuery.php`): resultado inmutable con la lista filtrada y el marcado explícito `is_from_cache()`.
- [x] Tests del parsing (éxito, vacío, inválido, HTTP, timeout), filtrado por versión, caché válida/expirada/refresh: 95 tests / 231 assertions en verde, PHPCS limpio, site sin errores PHP.

### Etapa 6 - Health Score

- [x] `SiteVitals\Score` (`includes/Score.php`): parte de 100 y descuenta los puntos de cada `Result` (fuente única de verdad). Total acotado a 0–100 y lista transparente de descuentos aplicados; estados: 90–100 Saludable, 70–89 Requiere Atención, 0–69 Crítico.
- [x] `ScoreDiscounts` ampliado con la tabla por severidad de vulnerabilidad (`VULN_CRITICAL`=15, `VULN_HIGH`=10, `VULN_MEDIUM`=5, `VULN_LOW`=2) y `for_vulnerability_severity()`; los descuentos por hallazgo local ya usaban `MINOR`=5/`MAJOR`=10.
- [x] Tests de score perfecto, descuentos acumulados, límite inferior 0, transparencia y límites de cada estado.
- [x] Verificación completa: 95 tests / 231 assertions en verde, PHPCS limpio, site sin errores PHP.

### Etapa 7 - Escaneo y persistencia de resultados

- [x] `SiteVitals\Scanner` (`includes/Scanner.php`): orquesta checks locales (`Checker::run_all()`) y consultas de vulnerabilidades (core, plugins y temas) y agrega todo en un `ScanOutcome` con su Health Score; las fuentes (versiones instaladas, reloj) y el `VulnerabilityClient` se inyectan.
- [x] `SiteVitals\ScanOutcome` (`includes/ScanOutcome.php`): resultado inmutable con resultados crudos, origen (manual/programado), `completed_at` y score; serializable a array plano (`to_array`/`from_array`). `from_array` descarta resultados corruptos sin romper el resto.
- [x] `SiteVitals\ScanStore` (`includes/ScanStore.php`): persiste el último escaneo en la opción `sitevitals_last_scan` (get/update inyectables); un valor corrupto o de versión anterior se trata como inexistente.
- [x] Vulnerabilidades convertidas en `Result` con id `vuln/{tipo}/{slug}/{uuid}`, severidad `critical` solo para críticas (resto `warning`), puntos según severidad y pantalla nativa de destino en el valor.
- [x] El escaneo manual fuerza `refresh` de la caché de vulnerabilidades; el programado la respeta (TTL 12 h).
- [x] Fallos parciales controlados: check que falla → Result `error`; API que falla → sin hallazgos; componente sin versión → sin consulta.
- [x] `Result` ampliado con `to_array()`/`from_array()` para persistencia (round-trip validado con tests).
- [x] Tests de Scanner (combinación, refresh/caché, fallo de API, origen inválido), ScanOutcome (score, conteos, serialización, corrupción), ScanStore (round-trip, corrupto, sin resultado) y DashboardData (agrupación, escapo de datos): suite completa 125 tests / 317 assertions en verde.

### Etapa 8 - Interfaz nativa de WP-Admin

- [x] `SiteVitals\Admin\AdminPage` (`admin/AdminPage.php`): menú dedicado "Vitals" (slug `sitevitals`, capability `manage_options`), formulario "Escanear ahora" vía `admin-post.php` con nonce, avisos nativos tras el escaneo y bloqueo anti-duplicado con transient de 60 s.
- [x] `SiteVitals\Admin\DashboardData` (`admin/DashboardData.php`): transformador puro del último `ScanOutcome` en datos planos para la vista (score, estado, fecha, origen, conteos, hallazgos agrupados por categoría y con severidad/recomendación/pantalla).
- [x] Vista `admin/views/dashboard.php`: tarjetas de score/último escaneo/conteos, tablas `widefat striped` por categoría, enlaces a pantallas nativas y a fuentes de vulnerabilidades, estados vacíos y avisos `notice-success/error/warning`; todo escapado.
- [x] CSS mínimo en `assets/css/admin.css` (sin JS) con estilos responsive para pantallas pequeñas.
- [x] Bootstrap en `sitevitals.php` al cargar el plugin dentro del panel (`is_admin()`): se construyen `Checker` (con `LocalChecks::all()`), `Scanner`, `ScanStore` y `AdminPage` y se registran los hooks. El menú se engancha a `admin_menu`, que en `admin.php` se dispara antes que `admin_init` (sin esto la pantalla daba 403).
- [x] Verificación: lint (PHPCS WordPress) limpio, 125 tests / 317 assertions en verde, render de la vista comprobado con y sin resultado, sitio sin errores PHP tras la carga en el admin real.

### Etapa 9 - Ajustes y WP-Cron

- [x] `SiteVitals\Settings` (`includes/Settings.php`): value object inmutable con frecuencia (`disabled|daily|weekly|monthly`), `mail_enabled` y `recipient` personalizado opcional; `defaults()`, `from_array()` (persistencia) y `from_unsafe()` (formulario, con `sanitize_email`/`is_email` inyectables que descartan destinatarios inválidos); `get_interval()` y `get_recurrence()` por frecuencia (86400/604800/2592000 s).
- [x] `SiteVitals\SettingsStore` (`includes/SettingsStore.php`): persiste los ajustes en la opción `sitevitals_settings` (get/update inyectables); un valor corrupto devuelve los defaults sin romper el panel.
- [x] `SiteVitals\Cron` (`includes/Cron.php`): hook `sitevitals_scan_scheduled` y recurrencias propias semanal (`sitevitals_weekly`) y mensual (`sitevitals_monthly`) vía filtro `cron_schedules` (la diaria usa la nativa `daily`); `schedule()` limpia antes de programar para evitar duplicados y rechaza la frecuencia desactivada; `clear()` y `next_run()` con funciones de WP-Cron inyectables.
- [x] `SiteVitals\ScanRunner` (`includes/ScanRunner.php`): orquesta el flujo (lectura del previo → escaneo → guardado → notificación) y expone un único `run(string $origin, Settings $settings, bool $refresh)`.
- [x] `ScanOutcome::signature()`: firma estable del diagnóstico (id+severidad+puntos, orden-independiente) para decidir el envío programado sin repetir correos.
- [x] Pantalla de ajustes (submenú `sitevitals-settings`): selector de frecuencia, checkbox de envío por correo y destinatario personalizado; guardado vía `admin-post.php` con nonce y capacidad `manage_options`; `SettingsStore::save()` + `Cron::schedule()` tras guardar; muestra la frecuencia y la próxima ejecución.
- [x] Bootstrap en `sitevitals.php`: factories estáticas (checker/scanner/scan_store/settings_store/mailer/cron/runner), hook del escaneo programado, filtro `cron_schedules` y `register_activation_hook`/`register_deactivation_hook` para programar/limpiar el evento.
- [x] La lectura de estados de redirect se centralizó en `AdminPage::query_status()` (un único `phpcs:ignore` justificado) y las vistas reciben `$data['notice']` en lugar de leer `$_GET`.
- [x] Tests de Settings (defaults, persistencia, saneo/validación de destino, intervalos/recurrencias), SettingsStore (round-trip y corrupción), Cron (schedules, duplicados, reprogramación, clear), MailReport (matriz de notificación, envío, fallos `wp_mail`) y ScanRunner (orquestación, no repetición); suite completa 173 tests / 401 assertions en verde.

### Etapa 10 - Notificaciones por correo

- [x] `SiteVitals\MailReport` (`includes/MailReport.php`): `should_notify()` (mail desactivado o frecuencia desactivada → no envía; manual siempre; programado solo si cambia la firma o no hay previo), `send()` con destinatario personalizado o correo del admin y captura de `Throwable` (un fallo de `wp_mail` nunca rompe el escaneo), asunto `[SiteVitals] Health Score N/100 — estado` y cuerpo en texto plano con score, estado, conteos y hasta 15 hallazgos con su recomendación.
- [x] `send_if_due()` combina las condiciones de notificación con el envío para uso directo desde `ScanRunner`.
- [x] Verificación: lint (PHPCS WordPress) limpio, 173 tests / 401 assertions en verde, render de dashboard y ajustes comprobado por script (notice ok/updated, frecuencia, destinatario y próxima ejecución), sitio sin errores PHP.

### Etapa 11 - Internacionalización

- [x] Text domain `sitevitals` del plugin y carga de traducciones con `load_plugin_textdomain()` (hook `init`, función `sitevitals_load_textdomain()`), tomando el idioma configurado en el sitio.
- [x] Inglés como idioma base del código: 200 cadenas reemplazadas en 29 archivos (títulos, botones, avisos, estados, recomendaciones, asunto y cuerpo de los correos), con cargar `translators` donde aplica; `SiteVitals` se mantiene como marca sin traducir.
- [x] Traducción al español: `languages/sitevitals.pot` (170 msgids) generado con WP-CLI (`wp i18n make-pot`) y `languages/sitevitals-es_ES.po`/`.mo` compilado (`wp i18n make-mo`).
- [x] Verificado en la instalación real: con el sitio en es_ES la interfaz y los mensajes se muestran en español (placeholders y elipsis correctos) y con en_US en inglés (cadenas fuente); los correos usan las mismas cadenas traducibles.
- [x] Tests actualizados a los textos en inglés (DashboardData, MailReport, Cron, WordPressVersionCheck): 194 tests / 499 assertions en verde y PHPCS limpio.

### Etapa 12 - Info del sitio, formato de hora, cabeceras e ignorados

- [x] `SiteVitals\Admin\SiteInfo` (`admin/SiteInfo.php`): transformador puro que expone URL del proyecto, versión de PHP y versión del núcleo de WordPress con fuentes inyectables (`home_url`/`PHP_VERSION`/`get_bloginfo`).
- [x] Ajuste “Formato de hora”: `Settings` ampliado con `time_24h` (`from_array`/`from_unsafe`/`to_array`/`is_time_24h`); las vistas de dashboard y ajustes aplican `H:i` con `date_i18n` cuando está activado.
- [x] `SiteVitals\Checks\SecurityHeadersCheck` (`includes/Checks/SecurityHeadersCheck.php`): check multi-resultado que consulta las cabeceras de la portada en una única petición HTTP (`wp_remote_get` con timeout 5, fuente inyectable) y devuelve un `Result` por cabecera básica (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, Strict-Transport-Security); 2 puntos por cabecera ausente (`HEADER_ISSUE_POINTS`) y un único Result de error sin puntos si la consulta falla. Registrado en `LocalChecks::all()`.
- [x] `SiteVitals\IgnoredStore` (`includes/IgnoredStore.php`): opción propia `sitevitals_ignored` (get/update inyectables), lista única de identificadores y `sanitize_id()` con lista blanca de caracteres que rechaza `..`; pasada a `AdminPage` vía factory `sitevitals_ignored_store()`.
- [x] Ignorar/restaurar: acciones `admin_post` `sitevitals_ignore`/`sitevitals_unignore` con nonce y capacidad `manage_options`, helper `AdminPage::toggle_ignored_url()` y avisos nativos `ignored`/`restored` tras el redirect.
- [x] `DashboardData::build( ?ScanOutcome, array $ignored )`: excluye los ignorados del score (`Score::from_results` sobre los visibles), de los conteos y de los hallazgos; devuelve el grupo `ignored` con su conteo; nueva categoría `headers` en `CATEGORY_ORDER` sin pantalla ni enlace.
- [x] Vistas: tres tarjetas superiores (URL del proyecto, versión de PHP, versión de WordPress), enlace “Ignorar” en cada hallazgo, sección colapsable `<details>` “Ignorados (N)” con enlace “Restaurar” y checkbox de 24 h en ajustes; CSS para URLs largas (`word-break`), enlace de ignorar y `details/summary`.
- [x] Añadidos sobre la marcha en la misma etapa: enlace “Ver sitio” en la tarjeta de URL, tarjeta de “Servidor” (`SERVER_SOFTWARE`, saneado con `sanitize_text_field`), acción masiva “Restaurar todos” (`sitevitals_restore_all` con nonce + `IgnoredStore::clear()`), y muestra del valor detectado en las cabeceras presentes cuando la categoría reporta algún fallo (filas OK verdes, sin enlace Ignorar); nota en el check de que la medición es solo sobre la portada pública.
- [x] Verificación: lint (PHPCS WordPress) limpio, 190 tests / 478 assertions en verde, render de dashboard y ajustes comprobado por script (site info con enlace y servidor, cabeceras con valor, Ignorar solo en no-OK, sección Ignorados con “Restaurar todos”), sitio sin errores PHP.

### Etapa 13 - Rediseño del dashboard

- [x] Header compacto: marca (logo + nombre + etiqueta) a la izquierda con chip de versión y botón “Escanear ahora” con icono en el extremo derecho, ya no contiguo a las tarjetas.
- [x] Grid de 3 columnas: (1) Health Score en gauge semicircular CSS (`conic-gradient`, sin JS, `--p` en grados precalculado) + tarjeta “Último escaneo” con formato 24 h; (2) “Detalles técnicos” con tarjetas de URL del proyecto, WordPress, PHP y servidor, cada una con mini-icono SVG/glifo; (3) “Hallazgos clave” con conteos grandes y badges de estado (Seguro / En riesgo / Pendiente / Al día).
- [x] Tablas de hallazgos al 100% de ancho con padding reducido, rayas alternas, icono de información (ⓘ) con tooltip, badges de severidad en tonos cálidos con los puntos descontados debajo y columna de acción separada (botones secundarios “Ver fuente”/pantallas nativas y enlace sutil “Ignorar”).
- [x] Tema claro: fondo `#f4f5f7`, tarjetas blancas con `border-radius` 10px y sombras sutiles; footer discreto con copyright y versión; responsive a 2 columnas y a 1 con botón a ancho completo en móvil.
- [x] Definida la constante `SITEVITALS_VERSION` en `sitevitals.php` como fuente de la versión para el footer y el chip del header (con guard en la vista).
- [x] Verificación: lint (PHPCS WordPress) limpio, 190 tests / 478 assertions en verde (sin cambios en dominios de datos ni lógica de negocio), render smoke correcto: gauge en 180deg, formato 24 h, detalles técnicos con fallback “No detectable”, badges, puntos de severidad y footer con versión.
- [x] Bugfix gauge: el `conic-gradient` usaba `from 180deg` (arco por la izquierda de las 6 a las 12, así al 100 % solo se veía medio semicírculo). Cambiado a `from 270deg`: el arco arranca en la base izquierda (9), pasa por las 12 y llega a la base derecha (3) al 100 %.

Retoques UI posteriores al rediseño:

- [x] Eliminado el chip de versión “v0.1.2” del header y toda la sección del footer.
- [x] WordPress y PHP en la misma fila (par de tarjetas en grid 2 columnas); URL y Servidor a ancho completo.
- [x] Tarjetas con igual alto total: `flex: 1 1 auto` dentro de cada columna con columnas a la misma altura.
- [x] Severidad oculta por defecto y visible al hacer hover sobre la fila (o con foco de teclado); aplica también a la tabla de ignorados.
- [x] El icono de información (ⓘ) en cada hallazgo ahora describe qué verifica el check (p. ej. HTTPS, XML-RPC, cabeceras) en lugar de repetir la recomendación: nueva clave `description` en `DashboardData::finding()` con mapa por id y prefijos (`headers/`, `plugin/`, `theme/`, `vuln/`).
- [x] Denominación de los hallazgos de vulnerabilidades del mismo componente: el score descuenta una sola vez por componente (la CVE más grave; `Score::from_results` agrupa por `type/slug`), y el panel muestra una única fila expandible (`<details>`) con la sub-lista de CVEs, cada una con su severidad, enlace "Ver fuente" e ignorar/restaurar individual. Una sola CVE conserva su fila normal.
- [x] La sub-lista de CVEs conserva la información completa de cada vulnerabilidad (pill de severidad con puntos, título, icono ⓘ de descripción, id, recomendación y acciones) dentro de la fila agrupada.
- [x] Reordenadas las columnas de las tablas de hallazgos: Severidad pasa a ser penúltima, justo antes de Acción (Hallazgo | Recomendación | Severidad | Acción).
- [x] Ancho de columnas: Hallazgo reduce al 25% y Recomendación absorbe el espacio liberado.
- [x] Las filas de vulnerabilidades agrupadas por componente mantienen la estructura normal de 4 columnas (Hallazgo | Descripción | Severidad | Acción): la pill de severidad queda en su columna y el desglose de CVEs es una sub-fila a ancho completo (`colspan="4"`) bajo la fila principal, de modo que la sub-lista no queda comprimida en la columna de Hallazgo.
- [x] La columna "Recomendación" pasa a denominarse "Descripción".
- [x] Las acciones de cada CVE de la sub-lista (Ver fuente, Ignorar/Restaurar) quedan alineadas a la derecha, igual que la columna Acción del resto de filas.
- [x] Cada CVE de la sub-lista agrupada se muestra como una fila horizontal: severidad, título e información a la izquierda y las acciones a la derecha, alineadas con la fila (flex con body y acciones separadas).
- [x] Las acciones de la sub-lista no quedan pegadas al borde de la tabla (`padding-right` en `.sitevitals-sub-actions`).
- [x] El resumen de la fila agrupada informa del número de CVEs del componente y pide actualizar, en la columna Descripción: "Se detectaron X CVEs relacionadas a {componente}. Actualiza pronto."; el toggle de la sub-fila usa "Click para desglose" como resumen.
- [x] La acción de la fila agrupada redirige a la pantalla nativa de actualización del componente (Plugins, Temas o Actualizaciones según el tipo) para que el usuario pueda actualizar desde el propio admin.
- [x] Los hallazgos sin enlace ni pantalla asociada muestran un botón rojo no clickeable "Sin acción disponible" en la columna Acción (solo cuando el hallazgo no está en orden).
- [x] Fix posición de los avisos del panel: el JS nativo de WordPress (`common.js`) reubicaba todos los `div.notice` al inicio de la página salvo los marcados con `.inline`. Todos los avisos de SiteVitals llevan ahora la clase `inline` y se renderizan exactamente donde se colocan en la plantilla; los avisos de resultado quedan como primer elemento de la página, por encima de la tarjeta de cabecera (sin entrar en ella).
- [x] Fix doble flecha en la sección "Ignorados": el marcador nativo de `<details>` (Firefox) se oculta con `list-style: none` en `summary`, dejando un único chevron.
- [x] Unificada la renderización de todas las tablas de hallazgos (categorías y ignorados) en un único parcial `admin/views/partials/findings-table.php`, invocado con un mismo `require` y la bandera `$sitevitals_restore`; eliminada la duplicación de marcado en `dashboard.php`.
- [x] Excluido `wp-content/` del lint de PHPCS y añadido a `.gitignore` (contenido local de WP con plugins de prueba, no es código del plugin).

Entorno de pruebas:

- [x] Corregida la visibilidad de plugins de prueba instalados por Composer (`wpackagist-plugin/*`): con el montaje anterior quedaban anidados dentro de `sitevitals/wp-content/plugins/` y WordPress no los detectaba. Añadido volumen `../wp-content/plugins:/var/www/html/wp-content/plugins` en `docker-compose.yml` (el montaje de la raíz como `sitevitals` tiene prioridad en la ruta anidada).
- [x] Movidos los plugins de prueba de `require` a `require-dev` en `composer.json` para que no se empaqueten con el plugin en producción.

### Etapa 14.1 - CI/CD: empaquetado y release

- [x] Creado `.github/workflows/release.yml` (disparador: tags `v*` + `workflow_dispatch`).
- [x] El workflow extrae la versión del tag (`v*` → `{{ github.ref_name }}` sin el prefijo `v`) y la usa como nombre del zip: `sitevitals-{version}.zip`.
- [x] Resuelto que Composer en modo release debe usar `--no-dev --optimize-autoloader --classmap-authoritative`: el zip de producción NUNCA puede llevar dependencias de desarrollo (PHPCS, PHPUnit, WPCS, plugins de prueba de wpackagist) — solo el autoloader PSR-4 de producción.
- [x] El zip se construye con `rsync` a un staging excluyendo `tests/`, `docker/`, `docs/`, `.github/`, `wp-content/` (entorno local) y `composer.phar`; incluye `readme.txt` (formato wp.org), `assets/`, `languages/`, `vendor/` (autoloader) y el código del plugin.
- [x] El zip se publica como artifact (`actions/upload-artifact@v7`) y como asset de una GitHub Release (`softprops/action-gh-release@v3`) generada automáticamente en el tag.
- [x] Validado localmente que el YAML del workflow parsea (truco `on_` de YAML 1.1) y que los pasos incluyen checkout, extract de versión, setup PHP 8.5 + Composer, construcción del zip y subida del artifact.
- [x] Documentado en `readme.txt` (wp.org): descripción, instalación, FAQ, y requisitos WP >= 6.5 / PHP >= 7.4.
