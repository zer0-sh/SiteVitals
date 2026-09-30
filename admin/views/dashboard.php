<?php
/**
 * Vista principal del panel de SiteVitals.
 *
 * @package SiteVitals
 *
 * @var array $data Datos preparados por DashboardData::build().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sitevitals_has    = $data['has_result'];
$sitevitals_state  = $sitevitals_has ? $data['score_state'] : 'unknown';
$sitevitals_gauge  = null !== $data['score_total'] ? (int) round( $data['score_total'] * 1.8 ) : 0;
$sitevitals_format = $data['settings']->is_time_24h() ? 'H:i' : (string) get_option( 'time_format' );
$sitevitals_values = array_merge(
	$data['site'],
	array(
		'vuln'    => $data['vulnerabilities_count'],
		'updates' => $data['pending_updates_count'],
	)
);
?>
<div class="wrap sitevitals-wrap">
	<?php if ( 'ok' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'Scan completed successfully.', 'sitevitals' ); ?></p>
		</div>
	<?php elseif ( 'error' === $data['notice'] ) : ?>
		<div class="notice notice-error inline">
			<p><?php esc_html_e( 'The scan failed. Try again in a few seconds.', 'sitevitals' ); ?></p>
		</div>
	<?php elseif ( 'busy' === $data['notice'] ) : ?>
		<div class="notice notice-warning inline">
			<p><?php esc_html_e( 'A scan is already in progress.', 'sitevitals' ); ?></p>
		</div>
	<?php elseif ( 'ignored' === $data['notice'] ) : ?>
		<div class="notice notice-info is-dismissible inline">
			<p><?php esc_html_e( 'The finding has been moved to the ignored section.', 'sitevitals' ); ?></p>
		</div>
	<?php elseif ( 'restored' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'The finding has been restored and counts again in the diagnosis.', 'sitevitals' ); ?></p>
		</div>
	<?php elseif ( 'restored_all' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'All ignored findings have been restored.', 'sitevitals' ); ?></p>
		</div>
	<?php endif; ?>

	<header class="sitevitals-header">
		<div class="sitevitals-brand">
			<span class="sitevitals-brand-mark" aria-hidden="true">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" />
					<path d="M15 19l2 2l4 -4" />
				</svg>
			</span>
			<div class="sitevitals-brand-text">
				<h1><?php esc_html_e( 'SiteVitals', 'sitevitals' ); ?></h1>
				<span class="sitevitals-brand-tag"><?php esc_html_e( 'Health, security and performance diagnosis', 'sitevitals' ); ?></span>
			</div>
		</div>

		<form class="sitevitals-scan-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="sitevitals_scan" />
			<?php wp_nonce_field( 'sitevitals_scan' ); ?>
			<button type="submit" class="button button-primary sitevitals-scan-button">
				<span><?php esc_html_e( 'Scan now', 'sitevitals' ); ?></span>
			</button>
		</form>
	</header>

	<?php if ( ! $sitevitals_has ) : ?>
		<div class="notice notice-info inline">
			<p><?php esc_html_e( 'No scans yet. Click "Scan now" to generate the first diagnosis.', 'sitevitals' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="sitevitals-grid">
		<section class="sitevitals-grid-col sitevitals-col-main" aria-label="<?php esc_attr_e( 'Site summary', 'sitevitals' ); ?>">
			<div class="sitevitals-card">
				<h2 class="sitevitals-card-title"><?php esc_html_e( 'Health Score', 'sitevitals' ); ?></h2>
				<div class="sitevitals-gauge sitevitals-gauge-<?php echo esc_attr( $sitevitals_state ); ?>" style="<?php echo esc_attr( '--p:' . $sitevitals_gauge . 'deg' ); ?>">
					<div class="sitevitals-gauge-fill"></div>
					<div class="sitevitals-gauge-value">
						<?php
						if ( $sitevitals_has ) {
							echo esc_html( (string) $data['score_total'] );
						} else {
							echo '&mdash;';
						}
						?>
					</div>
				</div>
				<div class="sitevitals-card-meta">
					<?php echo esc_html( $sitevitals_has ? $data['score_label'] : __( 'No scan', 'sitevitals' ) ); ?>
				</div>
			</div>

			<div class="sitevitals-card">
				<h2 class="sitevitals-card-title"><?php esc_html_e( 'Last scan', 'sitevitals' ); ?></h2>
				<div class="sitevitals-card-value">
					<?php
					if ( $sitevitals_has ) {
						echo esc_html(
							date_i18n(
								get_option( 'date_format' ) . ' ' . $sitevitals_format,
								$data['completed_at']
							)
						);
					} else {
						echo '&mdash;';
					}
					?>
				</div>
				<div class="sitevitals-card-meta">
					<?php echo esc_html( $sitevitals_has ? $data['origin_label'] : __( 'No scans', 'sitevitals' ) ); ?>
				</div>
			</div>
		</section>

		<section class="sitevitals-grid-col sitevitals-col-site" aria-label="<?php esc_attr_e( 'Technical details', 'sitevitals' ); ?>">
			<h2 class="sitevitals-grid-heading"><?php esc_html_e( 'Technical details', 'sitevitals' ); ?></h2>

			<div class="sitevitals-card sitevitals-tech">
				<span class="sitevitals-tech-icon sitevitals-tech-icon-globe" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
						<path d="M3.6 9h16.8" />
						<path d="M3.6 15h16.8" />
						<path d="M11.5 3a17 17 0 0 0 0 18" />
						<path d="M12.5 3a17 17 0 0 1 0 18" />
					</svg>
				</span>
				<div class="sitevitals-tech-text">
					<div class="sitevitals-tech-label"><?php esc_html_e( 'Site URL', 'sitevitals' ); ?></div>
					<div class="sitevitals-tech-value sitevitals-site-url">
						<a href="<?php echo esc_url( $sitevitals_values['url'] ); ?>" target="_blank" rel="noreferrer noopener">
							<?php echo esc_html( $sitevitals_values['url'] ); ?>
						</a>
					</div>
				</div>
			</div>

			<div class="sitevitals-tech-pair">
				<div class="sitevitals-card sitevitals-tech">
					<span class="sitevitals-tech-icon sitevitals-tech-icon-wp" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M9.5 9h3" />
							<path d="M4 9h2.5" />
							<path d="M11 9l3 11l4 -9" />
							<path d="M5.5 9l3.5 11l3 -7" />
							<path d="M18 11c.177 -.528 1 -1.364 1 -2.5c0 -1.78 -.776 -2.5 -1.875 -2.5c-.898 0 -1.125 .812 -1.125 1.429c0 1.83 2 2.058 2 3.571" />
							<path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
						</svg>
					</span>
					<div class="sitevitals-tech-text">
						<div class="sitevitals-tech-label"><?php esc_html_e( 'WordPress', 'sitevitals' ); ?></div>
						<div class="sitevitals-tech-value"><?php echo esc_html( $sitevitals_values['core'] ); ?></div>
					</div>
				</div>

				<div class="sitevitals-card sitevitals-tech">
					<span class="sitevitals-tech-icon sitevitals-tech-icon-php" aria-hidden="true">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M2 12a10 9 0 1 0 20 0a10 9 0 1 0 -20 0" />
							<path d="M5.5 15l.395 -1.974l.605 -3.026h1.32a1 1 0 0 1 .986 1.164l-.167 1a1 1 0 0 1 -.986 .836h-1.653" />
							<path d="M15.5 15l.395 -1.974l.605 -3.026h1.32a1 1 0 0 1 .986 1.164l-.167 1a1 1 0 0 1 -.986 .836h-1.653" />
							<path d="M12 7.5l-1 5.5" />
							<path d="M11.6 10h2.4l-.5 3" />
						</svg>
					</span>
					<div class="sitevitals-tech-text">
						<div class="sitevitals-tech-label"><?php esc_html_e( 'PHP', 'sitevitals' ); ?></div>
						<div class="sitevitals-tech-value"><?php echo esc_html( $sitevitals_values['php'] ); ?></div>
					</div>
				</div>
			</div>

			<div class="sitevitals-card sitevitals-tech">
				<span class="sitevitals-tech-icon sitevitals-tech-icon-server" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-2" />
						<path d="M3 15a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -2" />
						<path d="M7 8l0 .01" />
						<path d="M7 16l0 .01" />
					</svg>
				</span>
				<div class="sitevitals-tech-text">
					<div class="sitevitals-tech-label"><?php esc_html_e( 'Server', 'sitevitals' ); ?></div>
					<div class="sitevitals-tech-value"><?php echo esc_html( '' !== $sitevitals_values['server'] ? $sitevitals_values['server'] : __( 'Not detectable', 'sitevitals' ) ); ?></div>
				</div>
			</div>
		</section>

		<section class="sitevitals-grid-col sitevitals-col-key" aria-label="<?php esc_attr_e( 'Key findings', 'sitevitals' ); ?>">
			<h2 class="sitevitals-grid-heading"><?php esc_html_e( 'Key findings', 'sitevitals' ); ?></h2>

			<div class="sitevitals-card sitevitals-stat">
				<div class="sitevitals-stat-value sitevitals-stat-value-<?php echo esc_attr( $sitevitals_values['vuln'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( (string) $sitevitals_values['vuln'] ); ?>
				</div>
				<div class="sitevitals-stat-label"><?php esc_html_e( 'Active vulnerabilities', 'sitevitals' ); ?></div>
				<span class="sitevitals-badge sitevitals-badge-<?php echo esc_attr( $sitevitals_values['vuln'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( $sitevitals_values['vuln'] > 0 ? __( 'At risk', 'sitevitals' ) : __( 'Safe', 'sitevitals' ) ); ?>
				</span>
			</div>

			<div class="sitevitals-card sitevitals-stat">
				<div class="sitevitals-stat-value sitevitals-stat-value-<?php echo esc_attr( $sitevitals_values['updates'] > 0 ? 'bad' : 'good' ); ?>">
					<?php echo esc_html( (string) $sitevitals_values['updates'] ); ?>
				</div>
				<div class="sitevitals-stat-label"><?php esc_html_e( 'Pending updates', 'sitevitals' ); ?></div>
				<span class="sitevitals-badge sitevitals-badge-<?php echo esc_attr( $sitevitals_values['updates'] > 0 ? 'warn' : 'good' ); ?>">
					<?php echo esc_html( $sitevitals_values['updates'] > 0 ? __( 'Pending', 'sitevitals' ) : __( 'Up to date', 'sitevitals' ) ); ?>
				</span>
			</div>
		</section>
	</div>

	<?php if ( $sitevitals_has && array() !== $data['categories'] ) : ?>
		<?php foreach ( $data['categories'] as $sitevitals_group ) : ?>
			<section class="sitevitals-section">
				<h2 class="sitevitals-section-title"><?php echo esc_html( $sitevitals_group['label'] ); ?></h2>
				<?php
				$sitevitals_findings = $sitevitals_group['findings'];
				$sitevitals_restore  = false;
				require __DIR__ . '/partials/findings-table.php';
				?>
			</section>
		<?php endforeach; ?>
	<?php elseif ( $sitevitals_has ) : ?>
		<div class="notice notice-success inline">
			<p><?php esc_html_e( 'No relevant findings detected.', 'sitevitals' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $sitevitals_has && $data['ignored_count'] > 0 ) : ?>
		<div class="sitevitals-section">
			<details class="sitevitals-ignored">
				<summary>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: number of ignored findings. */
							__( 'Ignored (%d)', 'sitevitals' ),
							$data['ignored_count']
						)
					);
					?>
					<span class="sitevitals-restore-all">
						<a href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::restore_all_url() ); ?>">
							<?php esc_html_e( 'Restore all', 'sitevitals' ); ?>
						</a>
					</span>
				</summary>
				<?php
				$sitevitals_findings = $data['ignored'];
				$sitevitals_restore  = true;
				require __DIR__ . '/partials/findings-table.php';
				?>
			</details>
		</div>
	<?php endif; ?>
</div>
