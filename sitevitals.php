<?php
/**
 * Plugin Name: SiteVitals
 * Plugin URI:  https://github.com/zer0-sh/sitevitals
 * Description: Ultra lightweight WordPress plugin to monitor your website's performance and uptime.
 * Version:     0.1.2
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author:      SiteVitals
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sitevitals
 * Domain Path: /languages
 *
 * @package SiteVitals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SITEVITALS_PLUGIN_FILE' ) ) {
	define( 'SITEVITALS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'SITEVITALS_VERSION' ) ) {
	define( 'SITEVITALS_VERSION', '0.1.2' );
}

$sitevitals_autoload = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $sitevitals_autoload ) ) {
	require_once $sitevitals_autoload;
}

unset( $sitevitals_autoload );

add_action( \SiteVitals\Cron::HOOK, 'sitevitals_run_scheduled_scan' );
add_filter( 'cron_schedules', 'sitevitals_cron_schedules' );

register_activation_hook( __FILE__, 'sitevitals_activate' );
register_deactivation_hook( __FILE__, 'sitevitals_deactivate' );

if ( is_admin() ) {
	sitevitals_init_admin();
}

/**
 * Las traducciones de `sitevitals` se cargan automáticamente según el slug
 * del plugin (WordPress 4.6+ / 6.7+: carga just-in-time por textdomain y
 * traducciones del directorio de idiomas), por lo que no se necesita una
 * llamada manual a `load_plugin_textdomain()`. El filtro de fallback
 * regional español se conserva más abajo.
 */

/**
 * Redirige los archivos `.mo` de cualquier locale regional de español
 * (`es_CO`, `es_MX`, `es_AR`…) al genérico `es_ES` cuando no existe un
 * `.mo` específico para esa región.
 *
 * Con la carga just-in-time de traducciones (WordPress 6.7+), el registro de
 * textdomains (`WP_Textdomain_Registry`) resuelve el `.mo` por locale exacto
 * (`sitevitals-es_CO.mo`) y no aplica fallback regional ni pasa por el filtro
 * `plugin_locale`. Este filtro `load_textdomain_mofile` (aplicado siempre,
 * dentro de `load_textdomain()`) sustituye la región por `es_ES` y mantiene
 * el resto de idiomas intactos.
 *
 * @param string $mofile Ruta del archivo .mo que WordPress va a cargar.
 * @param string $domain Text domain del filtro.
 *
 * @return string Ruta .mo ajustada.
 */
function sitevitals_mofile_spanish_fallback( string $mofile, string $domain ): string {
	if ( 'sitevitals' !== $domain || ! preg_match( '/sitevitals-es_[A-Z]{2}\.mo$/i', $mofile ) ) {
		return $mofile;
	}

	if ( is_readable( $mofile ) ) {
		return $mofile;
	}

	return preg_replace( '/sitevitals-es_[A-Z]{2}\.mo$/i', 'sitevitals-es_ES.mo', $mofile );
}

add_filter( 'load_textdomain_mofile', 'sitevitals_mofile_spanish_fallback', 10, 2 );

/**
 * Inicializa el panel de administración de SiteVitals.
 *
 * Se ejecuta al cargar el plugin dentro del panel: el menú se registra en
 * `admin_menu`, que en admin.php ya se ha disparado cuando corre admin_init.
 *
 * @return void
 */
function sitevitals_init_admin(): void {
	$sitevitals_admin = new \SiteVitals\Admin\AdminPage(
		sitevitals_runner(),
		sitevitals_scan_store(),
		sitevitals_cron(),
		sitevitals_settings_store(),
		sitevitals_ignored_store()
	);

	$sitevitals_admin->register();
}

/**
 * Devuelve el checker con todos los checks locales registrados.
 *
 * @return \SiteVitals\Checker
 */
function sitevitals_checker(): \SiteVitals\Checker {
	static $sitevitals_checker = null;

	if ( null === $sitevitals_checker ) {
		$sitevitals_checker = new \SiteVitals\Checker();

		foreach ( \SiteVitals\Checks\LocalChecks::all() as $sitevitals_check ) {
			$sitevitals_checker->add_check( $sitevitals_check );
		}
	}

	return $sitevitals_checker;
}

/**
 * Devuelve el escáner del sitio.
 *
 * @return \SiteVitals\Scanner
 */
function sitevitals_scanner(): \SiteVitals\Scanner {
	static $sitevitals_scanner = null;

	if ( null === $sitevitals_scanner ) {
		$sitevitals_scanner = new \SiteVitals\Scanner( sitevitals_checker(), new \SiteVitals\VulnerabilityClient() );
	}

	return $sitevitals_scanner;
}

/**
 * Devuelve el almacén del último resultado.
 *
 * @return \SiteVitals\ScanStore
 */
function sitevitals_scan_store(): \SiteVitals\ScanStore {
	static $sitevitals_scan_store = null;

	if ( null === $sitevitals_scan_store ) {
		$sitevitals_scan_store = new \SiteVitals\ScanStore();
	}

	return $sitevitals_scan_store;
}

/**
 * Devuelve el almacén de los ajustes del plugin.
 *
 * @return \SiteVitals\SettingsStore
 */
function sitevitals_settings_store(): \SiteVitals\SettingsStore {
	static $sitevitals_settings_store = null;

	if ( null === $sitevitals_settings_store ) {
		$sitevitals_settings_store = new \SiteVitals\SettingsStore();
	}

	return $sitevitals_settings_store;
}

/**
 * Devuelve el almacén de los hallazgos ignorados.
 *
 * @return \SiteVitals\IgnoredStore
 */
function sitevitals_ignored_store(): \SiteVitals\IgnoredStore {
	static $sitevitals_ignored_store = null;

	if ( null === $sitevitals_ignored_store ) {
		$sitevitals_ignored_store = new \SiteVitals\IgnoredStore();
	}

	return $sitevitals_ignored_store;
}

/**
 * Devuelve el enviador de informes por correo.
 *
 * @return \SiteVitals\MailReport
 */
function sitevitals_mailer(): \SiteVitals\MailReport {
	static $sitevitals_mailer = null;

	if ( null === $sitevitals_mailer ) {
		$sitevitals_mailer = new \SiteVitals\MailReport();
	}

	return $sitevitals_mailer;
}

/**
 * Devuelve el gestor del escaneo programado.
 *
 * @return \SiteVitals\Cron
 */
function sitevitals_cron(): \SiteVitals\Cron {
	static $sitevitals_cron = null;

	if ( null === $sitevitals_cron ) {
		$sitevitals_cron = new \SiteVitals\Cron();
	}

	return $sitevitals_cron;
}

/**
 * Devuelve el orquestador del flujo de escaneo.
 *
 * @return \SiteVitals\ScanRunner
 */
function sitevitals_runner(): \SiteVitals\ScanRunner {
	static $sitevitals_runner = null;

	if ( null === $sitevitals_runner ) {
		$sitevitals_runner = new \SiteVitals\ScanRunner(
			sitevitals_scanner(),
			sitevitals_scan_store(),
			sitevitals_mailer()
		);
	}

	return $sitevitals_runner;
}

/**
 * Ejecuta el escaneo programado por WP-Cron.
 *
 * @return void
 */
function sitevitals_run_scheduled_scan(): void {
	sitevitals_runner()->run( \SiteVitals\ScanOutcome::ORIGIN_SCHEDULED, sitevitals_settings_store()->get() );
}

/**
 * Registra las recurrencias propias del plugin en WP-Cron.
 *
 * @param array $schedules Registro de recurrencias existente.
 *
 * @return array
 */
function sitevitals_cron_schedules( array $schedules ): array {
	return \SiteVitals\Cron::add_schedules( $schedules );
}

/**
 * Programa el escaneo al activar el plugin.
 *
 * @return void
 */
function sitevitals_activate(): void {
	sitevitals_cron()->schedule( sitevitals_settings_store()->get() );
}

/**
 * Limpia el evento cron al desactivar el plugin.
 *
 * @return void
 */
function sitevitals_deactivate(): void {
	sitevitals_cron()->clear();
}
