# AGENTS.md — Reglas de trabajo para SiteVitals

Instrucciones permanentes para el agente al trabajar en este repositorio. Prioridad igual que el `todo.md` y el `RUNBOOK.md`.

## Comunicación

- **Idioma**: español, salvo que el código o la petición exija inglés.
- **Estilo**: conciso, técnico y directo. Sin relleno ni preámbulos.
- **Ante la duda, se pregunta. No se asume.** Si una decisión tiene trade-offs o cambia algo ya establecido, ofrecer opciones concretas (pregunta con las más recomendable primero).
- Al acabar una tarea de código, resumen breve de qué se cambió y estado de verificación.

## Stack base (INMUTABLE — no se toca)

Estas decisiones están tomadas y no deben modificarse sin el usuario:

- Plugin open source `sitevitals`, repositorio `zer0-sh/sitevitals`, read-only por defecto.
- WordPress mínimo **6.5**; PHP mínimo **7.4** (compatibilidad foco: 7.x y 8.x).
- Sin planes Pro, sin telemetría, sin cuentas ni API keys, sin agentes/SaaS/microservicios.
- i18n: inglés + español, idioma tomado del sitio; text domain `sitevitals`.
- Caché de resultados por defecto: 12 horas (transients).
- Entorno local: Docker en `docker/` (`docker-compose.yml`), WP en puerto `8080`, MySQL **8.0** (no tocar).

## Dependencias de desarrollo (versiones fijadas)

Validar siempre que estén en la última versión compatible, **sin romper el stack base**:

- PHPUnit `^9.6` — tope real: PHP 7.4 del stack base. NO subir a 10/11/12 (exigen PHP ≥ 8.1).
- WPCS `^3.4.1`, PHPCS `^3.13.6` — PHPCS 4.x NO usar: WPCS 3.4.1 aún no lo soporta. 3.13.6 incluye fix CVE-2026-67434.
- GitHub Actions: `actions/checkout@v7`, `actions/cache@v6`, `shivammathur/setup-php@v2`, matriz PHP `7.4` + `8.5`.
- PHPCS `testVersion`: `7.4-8.5`.

Ante una actualización, verificar con `composer update` y reintentar lint + tests; avisar si una versión debe quedarse atrás y por qué.

## Arquitectura y estándares

- Autoload **PSR-4** vía Composer: `SiteVitals\` → `includes/`, `SiteVitals\Admin\` → `admin/`, `SiteVitals\Tests\` → `tests/`.
- Los archivos de clase se nombran como la clase (`Checker.php`, no `class-checker.php`). La convención `class-*.php` del RUNBOOK está superada.
- Checks: infraestructura en `includes/Checks/` (namespace `SiteVitals\Checks`), contrato `CheckInterface`, base `AbstractCheck`.
- Resultados: value objects **inmutables** (`SiteVitals\Result`) con validación estricta en el constructor.
- Checks de **solo lectura**: nunca escriben archivos, base de datos ni configuración.
- Los fallos individuales de un check no interrumpen el escaneo (severidad `error`, 0 puntos).
- Código simple, mantenible, responsabilidad única. **Sin under/over-engineering**.
- **No añadir comentarios** salvo lógica compleja o decisiones no obvias. Escapar siempre salida dinámica (`esc_html`/`esc_attr`), incluso en mensajes de excepción.
- Compatibilidad PHP 7.4: sin enums, sin `readonly`, sin promotion en constructor, sin nullsafe.

## Flujo obligatorio por fase (toma como plantilla)

1. Leer `todo.md` y el `RUNBOOK.md`; identificar las líneas de la sección a ejecutar.
2. Preguntar dudas antes de asumir (ver sección Comunicación).
3. Implementar de forma estricta (optimización y buenas prácticas primero).
4. Verificar:
   ```bash
   make composer-install   # instalar/actualizar dependencias
   make lint               # PHPCS (WordPress) — debe quedar limpio
   make lint:fix           # PHPCBF para autofix; reevaluar lo no autofixable
   make test               # PHPUnit — debe quedar en verde
   curl -s -o /dev/null -w "%{http_code}" http://localhost:8080   # 302 = WP OK
   docker logs docker-wordpress-1 | grep -iE "PHP (Fatal|Warning|Notice|Parse)"
   ```
   Si aparece una infracción de WPCS legítima (p. ej. conflicto con PSR-4), añadir exclusión **justificada con comentario** en `phpcs.xml.dist`.
5. Marcar con `[x]` los items completados en `todo.md` y ajustar el texto si la realidad cambió (siempre coherente con el repo).
6. Actualizar `CHANGELOG.md` con los cambios a la sección `[Unreleased]`.
7. Mantener el `README.md` al día si hay comandos o flujo nuevo.

## Git

- **No hacer commit salvo petición explícita.**
- Si se pide: commits atómicos, mensajes convencionales (`feat/fix/refactor/chore/docs`), revisar `git status`/`git diff` antes, no incluir secretos ni `vendor/` ni `composer.lock` (gitignored).
- No force-push, no editar config git, no `-i`, no commits vacíos.

## Recordatorios de contexto

- Tareas pendientes se siguen en `todo.md` por líneas (las secciones avanzan en orden).
- Extremos ya resueltos: si parece que algo se rehace, revisar CHANGELOG y todo.md antes de preguntar si procede.