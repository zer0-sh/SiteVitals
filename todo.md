# TODO — SiteVitals

Checklist de implementación del MVP descrito en [`RUNBOOK.md`](RUNBOOK.md).

## 0. Alcance y decisiones iniciales

- [x] Confirmar que el producto será un plugin de WordPress Open Source.
- [x] Confirmar que el plugin será read-only por defecto.
- [x] Confirmar que el plugin no tendrá planes Pro ni funciones bloqueadas.
- [x] Confirmar que el plugin no usará telemetría.
- [x] Confirmar que el plugin no requerirá cuentas ni API keys.
- [x] Confirmar que el plugin no modificará automáticamente archivos, base de datos ni configuraciones.
- [x] Confirmar que el plugin no incluirá agentes de servidor, SaaS ni microservicios.
- [x] Definir el nombre y slug definitivos del plugin: `sitevitals`.
- [x] Definir WordPress `6.5` como versión mínima soportada.
- [x] Definir PHP `7.4` como versión mínima soportada.
- [x] Mantener como foco principal de compatibilidad las versiones PHP `7.x` y `8.x`.
- [x] Definir como destinatario por defecto el correo del administrador del sitio.
- [x] Permitir personalizar el correo destinatario desde los ajustes.
- [x] Sustituir el umbral obligatorio de score por un ajuste explícito para enviar resultados por correo.
- [x] Definir que el plugin estará disponible en inglés y español.
- [x] Definir que el idioma se tomará del idioma configurado en el sitio.
- [x] Definir la duración por defecto de la caché: 12 horas.

## 1. Estructura y entorno local

- [x] Crear el archivo principal `sitevitals.php`.
- [x] Añadir al archivo principal la cabecera estándar de plugin de WordPress.
- [x] Añadir la protección contra acceso directo al archivo principal.
- [x] Crear la carpeta `includes/`.
- [x] Crear la carpeta `includes/checks/`.
- [x] Crear la carpeta `admin/`.
- [x] Crear la carpeta `admin/views/`.
- [x] Crear la carpeta `assets/`.
- [x] Crear la carpeta `tests/`.
- [x] Crear la carpeta `.github/workflows/`.
- [x] Crear y ubicar `docker-compose.yml` en `docker/`.
- [x] Configurar el servicio MySQL en Docker Compose.
- [x] Configurar el servicio WordPress en Docker Compose.
- [x] Publicar WordPress en el puerto local `8080`.
- [x] Montar el plugin dentro de `wp-content/plugins/sitevitals`.
- [x] Levantar los contenedores con Docker Compose.
- [x] Completar la instalación inicial de WordPress.
- [x] Activar el plugin desde WP-Admin.
- [x] Comprobar que el plugin no genera errores PHP al activarse.
- [x] Añadir un procedimiento para detener el entorno local.
- [x] Añadir un procedimiento para reiniciar el entorno local.

## 2. Calidad base y herramientas de desarrollo

- [x] Crear `composer.json`.
- [x] Añadir PHPUnit como dependencia de desarrollo.
- [x] Añadir PHPCS como dependencia de desarrollo.
- [x] Añadir WordPress Coding Standards como dependencia de desarrollo.
- [x] Configurar el estándar de WordPress en PHPCS.
- [x] Configurar exclusiones justificadas de PHPCS, si fueran necesarias.
- [x] Configurar el autoload de las clases del plugin.
- [x] Crear una configuración base de PHPUnit.
- [x] Ejecutar PHPCS sobre el código inicial.
- [x] Corregir los errores de PHPCS del código inicial.
- [x] Ejecutar PHPUnit aunque todavía no existan todos los tests.
- [x] Documentar en el README los comandos de instalación y validación.

## 3. Checks locales de WordPress

### 3.1. Infraestructura de checks

- [x] Crear `includes/Checker.php` (por autoload PSR-4; equivalente al `class-checker.php` del RUNBOOK).
- [x] Definir una interfaz o formato común para los resultados de los checks.
- [x] Incluir en cada resultado un identificador estable.
- [x] Incluir en cada resultado un título legible.
- [x] Incluir en cada resultado la severidad.
- [x] Incluir en cada resultado el valor detectado.
- [x] Incluir en cada resultado la recomendación.
- [x] Incluir en cada resultado la puntuación descontada.
- [x] Permitir ejecutar todos los checks locales desde el checker principal.
- [x] Hacer que los checks sean de solo lectura.
- [x] Manejar errores individuales sin interrumpir todo el escaneo.

### 3.2. WordPress Core

- [x] Crear el check de versión instalada de WordPress (`WordPressVersionCheck`).
- [x] Obtener la versión estable disponible de WordPress desde la transient nativa `update_core`.
- [x] Comparar la versión instalada con la versión disponible.
- [x] Detectar si hay una actualización pendiente del Core.
- [x] Detectar si las actualizaciones automáticas del Core están habilitadas.
- [x] Generar una recomendación cuando el Core esté desactualizado.
- [x] Añadir tests para versiones iguales.
- [x] Añadir tests para versiones instaladas antiguas.

### 3.3. Entorno PHP

- [x] Crear el check de versión de PHP (`PhpVersionCheck`).
- [x] Detectar la versión PHP activa.
- [x] Definir la fuente de datos del ciclo de vida de PHP (tabla curada en `PhpVersionCheck::PHP_EOL_DATES`).
- [x] Detectar si la versión de PHP está fuera de soporte.
- [x] Revisar `memory_limit`.
- [x] Revisar `display_errors`.
- [x] Revisar las extensiones PHP requeridas por WordPress.
- [x] Generar recomendaciones específicas para cada hallazgo PHP.
- [x] Añadir tests para PHP soportado.
- [x] Añadir tests para PHP fuera de soporte.
- [ ] Mantenimiento futuro: workflow que compruebe la tabla EOL de PHP (`PHP_EOL_DATES`) y actualice fechas/versiones.

### 3.4. Configuración de seguridad

- [x] Crear el check de HTTPS (`HttpsCheck`).
- [x] Detectar si la petición actual usa HTTPS.
- [x] Detectar configuraciones de WordPress que indiquen HTTPS.
- [x] Generar una recomendación si el sitio no fuerza HTTPS.
- [x] Crear el check de WP_DEBUG (`DebugCheck`).
- [x] Crear el check de WP_DEBUG_LOG (`DebugLogCheck`).
- [x] Detectar configuraciones de debug inseguras en producción.
- [x] Crear el check de XML-RPC (`XmlRpcCheck`).
- [x] Detectar si XML-RPC está habilitado o expuesto.
- [x] Crear el check de DISALLOW_FILE_EDIT (`FileEditCheck`).
- [x] Detectar si la edición de archivos desde WP-Admin está permitida.
- [x] Generar recomendaciones accionables para cada configuración insegura.
- [x] Añadir tests para HTTPS activo e inactivo.
- [x] Añadir tests para debug activo e inactivo.
- [x] Añadir tests para XML-RPC activo e inactivo.
- [x] Añadir tests para edición de archivos permitida y bloqueada.

### 3.5. Salud y fiabilidad

- [x] Crear el check de WP-Cron (`CronCheck`).
- [x] Detectar si WP-Cron está deshabilitado (`DISABLE_WP_CRON`).
- [x] Comprobar si existen eventos cron relevantes (eventos del Core vía `wp_get_scheduled_event`).
- [x] Crear el check de Loopback Requests (`LoopbackCheck`).
- [x] Ejecutar una comprobación de loopback de forma segura (`wp_remote_get`, timeout corto).
- [x] Detectar respuestas fallidas o con timeout.
- [x] Evitar que el check de loopback bloquee indefinidamente el escaneo.
- [x] Generar recomendaciones para fallos de WP-Cron.
- [x] Generar recomendaciones para fallos de loopback.
- [x] Añadir tests para WP-Cron habilitado y deshabilitado.
- [x] Añadir tests para loopback correcto y fallido.

## 4. Plugins, temas y actualizaciones

- [x] Obtener la lista de plugins con actualizaciones disponibles (transient `update_plugins`).
- [x] Obtener la lista de temas con actualizaciones disponibles (transient `update_themes`).
- [x] Consultar las actualizaciones disponibles de plugins.
- [x] Consultar las actualizaciones disponibles de temas.
- [x] Detectar componentes desactualizados.
- [x] Generar un resultado individual por plugin desactualizado.
- [x] Generar un resultado individual por tema desactualizado.
- [x] Añadir enlaces a la pantalla nativa de actualizaciones de WordPress.
- [x] Añadir tests para listas vacías.
- [x] Añadir tests para componentes actualizados.
- [x] Añadir tests para componentes desactualizados.

## 5. Vulnerabilidades y caché

- [x] Crear `includes/Vulnerability.php` (nomenclatura PSR-4; sustituye la propuesta `class-vulnerability.php`).
- [x] Definir el cliente para la WPVulnerability API (`includes/VulnerabilityClient.php`).
- [x] Definir el formato interno de una vulnerabilidad (VO inmutable con severidad normalizada y rango `operator`).
- [x] Consultar vulnerabilidades del Core.
- [x] Consultar vulnerabilidades de plugins.
- [x] Consultar vulnerabilidades de temas.
- [x] Mapear cada componente instalado al formato requerido por la API (Core por versión; plugins y temas por slug).
- [x] Procesar respuestas exitosas de la API.
- [x] Procesar respuestas vacías de la API.
- [x] Procesar respuestas inválidas de la API.
- [x] Procesar errores HTTP de la API.
- [x] Procesar timeouts de la API.
- [x] Evitar que un fallo de la API detenga el diagnóstico local.
- [x] Crear una clave de transient por componente (core por versión; plugin/tema por slug, ya que la API no filtra por versión y el filtrado es local con `version_compare`).
- [x] Guardar respuestas de vulnerabilidades en transients.
- [x] Configurar una expiración por defecto de 12 horas.
- [x] Leer la caché antes de realizar una petición externa.
- [x] Evitar peticiones repetidas mientras la caché sea válida.
- [x] Permitir invalidar la caché al ejecutar un escaneo manual (`refresh`).
- [x] Marcar claramente los resultados obtenidos desde caché.
- [x] Añadir tests del parsing de respuestas de la API.
- [x] Añadir tests de caché válida.
- [x] Añadir tests de caché expirada.
- [x] Añadir tests de errores de la API.

## 6. Health Score

- [x] Crear `includes/Score.php` (nomenclatura PSR-4; sustituye la propuesta `class-score.php`).
- [x] Definir el score inicial en 100 puntos.
- [x] Definir la tabla de descuentos por tipo de hallazgo (`ScoreDiscounts`).
- [x] Definir el descuento por vulnerabilidad crítica (15).
- [x] Definir el descuento por vulnerabilidad alta, media y baja (10/5/2).
- [x] Definir el descuento por PHP fuera de soporte (MAJOR, 10).
- [x] Definir el descuento por Core desactualizado (MINOR, 5).
- [x] Definir el descuento por plugin desactualizado (MINOR, 5).
- [x] Definir el descuento por tema desactualizado (MINOR, 5).
- [x] Definir el descuento por debug inseguro (MINOR, 5).
- [x] Definir los descuentos de HTTPS, XML-RPC y demás checks de seguridad (MINOR, 5).
- [x] Evitar que el score sea menor que 0.
- [x] Evitar que el score sea mayor que 100.
- [x] Hacer transparente la lista de descuentos aplicados.
- [x] Clasificar `90–100` como “Saludable”.
- [x] Clasificar `70–89` como “Requiere Atención”.
- [x] Clasificar `0–69` como “Crítico”.
- [x] Añadir tests para score perfecto.
- [x] Añadir tests para descuentos acumulados.
- [x] Añadir tests para el límite inferior 0.
- [x] Añadir tests para los límites de cada estado.

## 7. Escaneo y persistencia de resultados

- [x] Definir el flujo completo de escaneo: `SiteVitals\Scanner::scan()` orquesta checks locales + consultas de vulnerabilidades y agrega ambos en un `ScanOutcome` con su Health Score.
- [x] Ejecutar checks locales en cada escaneo (`Checker::run_all()`).
- [x] Ejecutar checks de plugins y temas en cada escaneo (multichecks `run_many()` aplanados por el checker).
- [x] Ejecutar consultas de vulnerabilidades respetando la caché (transients de `VulnerabilityClient`); el origen manual fuerza `refresh` para invalidarla.
- [x] Calcular el Health Score al finalizar los checks (`Score::from_results` sobre los resultados del escaneo).
- [x] Guardar el último resultado completo en WordPress (`SiteVitals\ScanStore` en la opción `sitevitals_last_scan`).
- [x] Guardar la fecha y hora del último escaneo (`completed_at`).
- [x] Guardar el origen del escaneo: manual o programado (constantes `ScanOutcome::ORIGIN_*`).
- [x] Guardar errores parciales sin perder resultados válidos: fallo de check → Result `error`; fallo de API → sin hallazgos; `from_array()` descarta resultados corruptos individuales.
- [x] Permitir recuperar el último resultado sin repetir el escaneo (`ScanStore::get()`).
- [x] Definir el comportamiento cuando todavía no existe ningún resultado: `get()` devuelve `null` y la vista muestra el estado vacío con llamada a escanear.

## 8. Interfaz nativa de WP-Admin

- [x] Crear la pantalla en `admin/AdminPage.php` (nombres PSR-4; sustituye `class-admin-page.php`).
- [x] Registrar una pantalla dedicada en WP-Admin (`admin_menu`, menú Vitals, slug `sitevitals`).
- [x] Restringir el acceso según capacidades de administrador (`manage_options` en menú y al procesar el escaneo).
- [x] Crear la vista principal en `admin/views/dashboard.php`.
- [x] Mostrar el score general de 0 a 100 (clase de estado por color).
- [x] Mostrar el estado textual del score (Saludable / Requiere atención / Crítico).
- [x] Mostrar la fecha del último escaneo (formato nativo de ajustes, con `date_i18n`).
- [x] Mostrar el número de vulnerabilidades activas.
- [x] Mostrar el número de actualizaciones pendientes.
- [x] Mostrar los hallazgos agrupados por categoría (core/php/security/system/plugin/theme/vuln) con `SiteVitals\Admin\DashboardData`.
- [x] Mostrar la severidad de cada hallazgo (etiqueta + badge de color).
- [x] Mostrar una recomendación clara para cada hallazgo.
- [x] Mostrar enlaces directos a las pantallas nativas pertinentes (update-core, plugins, temas, site-health, ajustes) y, en vulnerabilidades, enlace externo a la fuente.
- [x] Añadir el botón “Escanear ahora” (envío por `admin-post.php`).
- [x] Proteger el formulario con nonce (`wp_nonce_field` + `check_admin_referer`).
- [x] Validar permisos antes de ejecutar un escaneo manual.
- [x] Mostrar un aviso de éxito al terminar un escaneo.
- [x] Mostrar un aviso de error si el escaneo falla.
- [x] Evitar escaneos manuales duplicados simultáneos (lock transient de 60 s).
- [x] Usar avisos nativos `notice-success`, `notice-error` y `notice-warning`.
- [x] Usar tablas nativas (`widefat striped`) para los listados.
- [x] Mantener la interfaz funcional sin librerías externas.
- [x] Añadir CSS mínimo en `assets/css/admin.css` (tarjetas, colores por estado, modo responsive).
- [x] Añadir JavaScript mínimo solo si es necesario: no hace falta, se prescinde de él.
- [x] Comprobar la interfaz en escritorio: render verificado por script (HTML con resultado y sin resultado); revisión visual final del usuario pendiente.
- [x] Comprobar la interfaz en pantallas pequeñas: rule `@media (max-width: 782px)` en el CSS y `overflow-x` en tablas; revisión visual final del usuario pendiente.
- [x] Comprobar textos, etiquetas y estados vacíos: verificado estado sin escaneos y sin hallazgos, con todas las etiquetas traducibles.

## 9. Ajustes y WP-Cron

- [x] Crear `includes/Cron.php` (PSR-4; la convención `class-*.php` está superada).
- [x] Definir los intervalos diario, semanal y mensual.
- [x] Añadir la opción “Desactivado”.
- [x] Registrar el evento cron al activar el plugin.
- [x] Eliminar el evento cron al desactivar el plugin.
- [x] Reprogramar el evento cuando cambie la frecuencia.
- [x] Ejecutar el flujo de escaneo desde el evento cron.
- [x] Añadir una pantalla o sección de ajustes.
- [x] Añadir el selector de frecuencia.
- [x] Registrar y sanear el valor guardado.
- [x] Proteger los ajustes con nonce y capacidades.
- [x] Mostrar la frecuencia actualmente configurada.
- [ ] Mostrar/ocultar la entrada “Vitals” de la barra lateral desde el ajuste (decisión pendiente; por defecto visible).
- [x] Evitar eventos cron duplicados.
- [x] Registrar la fecha de la próxima ejecución.
- [x] Añadir tests de activación y desactivación del cron.
- [x] Añadir tests de cambio de frecuencia.
- [ ] Revisión visual de la pantalla de ajustes pendiente por el usuario.

## 10. Notificaciones por correo

- [x] Añadir el ajuste “Enviar resultados por correo”.
- [x] Definir el valor por defecto del ajuste de envío por correo como activado.
- [x] Usar el correo del administrador como destinatario por defecto.
- [x] Permitir personalizar el destinatario desde los ajustes.
- [x] Validar y sanear el correo destinatario personalizado.
- [x] Definir que el correo incluirá el score y un resumen corto del diagnóstico.
- [x] Definir el envío tras escaneos manuales cuando el usuario los ejecute.
- [x] Definir el envío tras todos los escaneos programados.
- [x] Implementar el envío según el alcance definido para escaneos manuales y programados.
- [x] No enviar correos repetidos innecesariamente.
- [x] Crear un asunto identificable.
- [x] Crear un resumen legible del diagnóstico.
- [x] Incluir el score y un resumen corto del diagnóstico.
- [x] Enviar el correo al administrador configurado.
- [x] Manejar fallos de `wp_mail` sin romper el escaneo.
- [x] Añadir tests de condiciones de notificación.

## 11. Internacionalización

- [x] Definir `sitevitals` como text domain del plugin.
- [x] Identificar todos los textos visibles para traducir.
- [x] Envolver los textos PHP en funciones de internacionalización de WordPress.
- [x] Preparar la traducción en inglés (idioma base del código, 200 cadenas).
- [x] Preparar la traducción en español (`languages/sitevitals-es_ES.po/.mo`, 170 msgids).
- [x] Usar el idioma configurado en el sitio para seleccionar las traducciones.
- [x] Traducir títulos, botones, avisos, estados y recomendaciones.
- [x] Traducir los asuntos y contenidos de los correos.
- [x] Verificar la interfaz con el sitio configurado en inglés.
- [x] Verificar la interfaz con el sitio configurado en español.
- [x] Verificar los correos con el sitio configurado en inglés.
- [x] Verificar los correos con el sitio configurado en español.
- [x] Confirmado que locales regionales de español (`es_CO`, `es_MX`, `es_AR`…) sin `.mo` propio usan `sitevitals-es_ES.mo` como fallback genérico (filtro `load_textdomain_mofile` sobre el dominio `sitevitals`).

## 12. Info del sitio, formato de hora, cabeceras e ignorados

- [x] Mostrar en la parte superior del panel la URL del proyecto, con enlace “Ver sitio”.
- [x] Mostrar en la parte superior del panel la versión de PHP activa.
- [x] Mostrar en la parte superior del panel la versión de WordPress instalada.
- [x] Mostrar en la parte superior del panel el software del servidor web.
- [x] Añadir el ajuste “Formato de hora de 24 horas”.
- [x] Aplicar el formato de 24 horas a la hora de los escaneos y la próxima ejecución.
- [x] Crear el check de cabeceras de seguridad (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, Strict-Transport-Security).
- [x] Devolver un resultado por cabecera consultada (contrato multi-resultado).
- [x] Mostrar el valor detectado de las cabeceras presentes junto a las ausentes.
- [x] Notar en el check que la medición se hace solo sobre la portada pública.
- [x] Puntuar 2 puntos por cabecera ausente.
- [x] Consultar las cabeceras en una única petición HTTP con timeout.
- [x] Devolver un único Result de error sin puntos si la consulta de cabeceras falla.
- [x] Permitir ignorar hallazgos individuales desde el dashboard.
- [x] Permitir restaurar hallazgos ignorados.
- [x] Permitir restaurar todos los hallazgos ignorados de una vez.
- [x] Guardar los ignorados en una opción independiente (`sitevitals_ignored`).
- [x] Proteger las acciones de ignorar/restaurar con nonce y capacidades.
- [x] Excluir los hallazgos ignorados del score y de los conteos del panel.
- [x] Mostrar la sección colapsable “Ignorados” al final del dashboard.

## 13. Rediseño del dashboard

- [x] Header compacto con marca a la izquierda (logo + nombre + versión) y botón “Escanear ahora” en el extremo derecho.
- [x] Layout en grid de 3 columnas: Health Score (gauge semicircular + último escaneo), detalles técnicos (URL, WordPress, PHP, servidor) y hallazgos clave (vulnerabilidades y actualizaciones con badges).
- [x] Gauge de Health Score en CSS puro (`conic-gradient`, sin JS).
- [x] Mini-iconos identificativos para URL, WordPress, PHP y servidor en las tarjetas técnicas.
- [x] Badges de estado (Seguro / En riesgo / Pendiente) en las tarjetas de hallazgos clave.
- [x] Tablas de hallazgos al 100% de ancho con padding reducido y rayas alternas.
- [x] Icono de información (ⓘ) junto a cada hallazgo con el tooltip de la recomendación.
- [x] Badges de severidad en tonos cálidos con los puntos descontados debajo.
- [x] Columna de acción separada con botones secundarios sutiles.
- [x] Tema claro: fondo `#f4f5f7`, tarjetas blancas con `border-radius` 8–12px y sombras sutiles.
- [x] Footer discreto con copyright y versión del plugin.
- [x] Definir la constante `SITEVITALS_VERSION` a partir de la cabecera del plugin.
- [x] Mantener el responsive: grid 2/1 columnas y botón a ancho completo en móvil.
- [x] Severidad como pill siempre visible; los puntos descontados (`-X puntos`) como tooltip del pill solo cuando aplican.
- [x] Icono (ⓘ) con descripción breve de qué verifica cada check (HTTPS, XML-RPC, cabeceras, etc.), no la recomendación.
- [x] Tablas de hallazgos unificadas en un único parcial (`partials/findings-table.php`) usado por categorías y por ignorados.
- [x] Varias CVEs del mismo componente: el score descuenta una sola vez (la más grave) y el panel las agrupa en una fila expandible con sub-lista (severidad, ver fuente, ignorar/restaurar por CVE).
- [x] Fix doble flecha en la sección de ignorados (solo chevron, sin el marcador nativo de Firefox).
- [x] Reorden de columnas de hallazgos: Severidad penúltima (Hallazgo | Recomendación | Severidad | Acción).
- [x] Ancho de columnas: Hallazgo al 25% y Recomendación absorbe el espacio restante.
- [x] Filas agrupadas de CVEs con estructura normal de 4 columnas (la pill de severidad en su columna) y el desglose en una sub-fila a ancho completo (`colspan`); las filas normales sin cambios.
- [x] Columna "Recomendación" renombrada a "Descripción".
- [x] Acciones de cada CVE de la sub-lista alineadas a la derecha.
- [x] Cada CVE de la sub-lista como fila horizontal (cuerpo a la izquierda, acciones a la derecha en la misma línea).
- [x] Acciones de la sub-lista con separación del borde derecho.
- [x] Resumen de la fila agrupada: "Se detectaron X CVEs relacionadas a {componente}. Actualiza pronto." en la columna Descripción; el toggle de la sub-fila usa "Click para desglose".
- [x] Botón rojo no clickeable "Sin acción disponible" para hallazgos sin enlace ni pantalla asociada.
- [x] La acción de la fila agrupada lleva a la pantalla nativa de actualización (Plugins/Temas/Actualizaciones) según el tipo de componente.
- [x] Fix posición de avisos: la clase `inline` evita que el JS de WordPress reubique los `notice` al inicio; los avisos de resultado se muestran como primer elemento de la página, por encima de la tarjeta de cabecera (sin entrar en ella).
- [x] Sub-lista de CVEs con la información completa de cada vulnerabilidad (severidad, título, descripción, id, recomendación y acciones individuales), dentro de la fila agrupada.
- [x] Verificación: PHPCS limpio, PHPUnit en verde y render smoke correcto.

## 14. CI/CD

- [x] Crear `.github/workflows/ci.yml`.
- [x] Ejecutar el workflow en cada push.
- [x] Ejecutar el workflow en cada pull request.
- [x] Configurar PHP `7.4` como versión mínima en CI.
- [x] Configurar PHP `8.5` en CI (matriz `7.4` + `8.5`, validada con YAML y pasos simulados en PHP 8.5).
- [x] Probar en CI una versión representativa de PHP `7.x`.
- [x] Probar en CI una versión representativa de PHP `8.x`.
- [x] Instalar dependencias de Composer en CI.
- [x] Ejecutar PHPCS en CI.
- [x] Ejecutar PHPUnit en CI.
- [x] Hacer que el workflow falle si PHPCS falla.
- [x] Hacer que el workflow falle si PHPUnit falla.
- [x] Revisar que el workflow no dependa de secretos externos.
- [ ] Ejecutar el workflow con una rama de prueba.
- [ ] Corregir cualquier fallo del workflow.

## 15. Validación final del MVP

- [ ] Instalar el plugin desde una instalación WordPress limpia.
- [ ] Activar el plugin sin warnings ni errores.
- [ ] Ejecutar un escaneo manual completo.
- [ ] Verificar el check de versión de WordPress.
- [ ] Verificar el check de versión de PHP.
- [ ] Verificar el check de HTTPS.
- [ ] Verificar el check de debug.
- [ ] Verificar el check de XML-RPC.
- [ ] Verificar el check de `DISALLOW_FILE_EDIT`.
- [ ] Verificar el check de WP-Cron.
- [ ] Verificar el check de loopback.
- [ ] Verificar el check de cabeceras de seguridad.
- [ ] Verificar el ignore/restaurar de hallazgos y el recomputo del score.
- [ ] Verificar plugins desactualizados.
- [ ] Verificar temas desactualizados.
- [ ] Verificar vulnerabilidades del Core, plugins y temas.
- [ ] Verificar que la caché evita peticiones repetidas.
- [ ] Verificar el cálculo del score.
- [ ] Verificar los tres estados visuales del score.
- [ ] Verificar el guardado del último resultado.
- [ ] Verificar la ejecución diaria del cron.
- [ ] Verificar la ejecución semanal del cron.
- [ ] Verificar la ejecución mensual del cron.
- [ ] Verificar la opción de cron desactivado.
- [ ] Verificar el envío condicional de notificaciones.
- [ ] Verificar permisos y nonces en todas las acciones de admin.
- [ ] Verificar que ningún flujo modifica archivos o configuraciones automáticamente.
- [ ] Verificar que no existen librerías pesadas innecesarias.
- [ ] Ejecutar PHPCS sin errores.
- [ ] Ejecutar PHPUnit sin errores.
- [ ] Ejecutar el workflow completo de CI sin errores.
- [ ] Actualizar el README con instalación, uso y limitaciones.
- [ ] Revisar que las funcionalidades fuera de alcance no hayan sido implementadas.
- [ ] Preparar la primera versión etiquetada del plugin.
