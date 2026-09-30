<?php
/**
 * Tabla de hallazgos del panel de SiteVitals.
 *
 * Vista parcial reutilizada por el dashboard para cada categoría y para
 * los hallazgos ignorados; todas las tablas se renderizan igual.
 *
 * @package SiteVitals
 *
 * @var array $sitevitals_findings Filas con el formato de DashboardData::finding().
 * @var bool  $sitevitals_restore   true si las filas ya están ignoradas (acción "Restaurar").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sitevitals_restore = isset( $sitevitals_restore ) ? (bool) $sitevitals_restore : false;
?>
<table class="sitevitals-table">
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Finding', 'sitevitals' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Description', 'sitevitals' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Severity', 'sitevitals' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Action', 'sitevitals' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $sitevitals_findings as $sitevitals_finding ) : ?>
			<?php
			$sitevitals_points_tip = $sitevitals_finding['points'] > 0
				? sprintf(
					/* translators: %d: points deducted from the Health Score. */
					__( '-%d points', 'sitevitals' ),
					$sitevitals_finding['points']
				)
				: '';
			$sitevitals_is_group = isset( $sitevitals_finding['vulnerabilities'] ) && is_array( $sitevitals_finding['vulnerabilities'] );
			?>
			<tr>
				<td class="sitevitals-finding-cell">
					<span class="sitevitals-finding-title"><?php echo esc_html( $sitevitals_finding['title'] ); ?></span>
					<span class="sitevitals-info-icon" title="<?php echo esc_attr( $sitevitals_finding['description'] ); ?>">i</span>
					<br />
					<span class="sitevitals-finding-id"><?php echo esc_html( $sitevitals_finding['id'] ); ?></span>
					<?php if ( '' !== $sitevitals_finding['value'] ) : ?>
						<br />
						<span class="sitevitals-finding-value"><?php echo esc_html( $sitevitals_finding['value'] ); ?></span>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( $sitevitals_finding['recommendation'] ); ?></td>
				<td>
					<span class="sitevitals-severity sitevitals-severity-<?php echo esc_attr( $sitevitals_finding['severity'] ); ?>"
						<?php if ( '' !== $sitevitals_points_tip ) : ?>
							title="<?php echo esc_attr( $sitevitals_points_tip ); ?>"
						<?php endif; ?>
					><?php echo esc_html( $sitevitals_finding['severity_label'] ); ?></span>
				</td>
				<td class="sitevitals-action-col">
					<?php if ( $sitevitals_restore ) : ?>
						<a class="sitevitals-ignore-link" href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::toggle_ignored_url( \SiteVitals\Admin\AdminPage::UNIGNORE_ACTION, $sitevitals_finding['id'] ) ); ?>">
							<?php esc_html_e( 'Restore', 'sitevitals' ); ?>
						</a>
					<?php else : ?>
						<?php if ( '' !== $sitevitals_finding['link'] ) : ?>
							<a class="sitevitals-action-link" href="<?php echo esc_url( $sitevitals_finding['link'] ); ?>" target="_blank" rel="noreferrer noopener">
								<?php esc_html_e( 'View source', 'sitevitals' ); ?>
							</a>
						<?php elseif ( null !== $sitevitals_finding['screen'] ) : ?>
							<a class="sitevitals-action-link" href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::screen_url( $sitevitals_finding['screen'] ) ); ?>">
								<?php echo esc_html( \SiteVitals\Admin\AdminPage::screen_label( $sitevitals_finding['screen'] ) ); ?>
							</a>
						<?php elseif ( ! $sitevitals_finding['is_ok'] ) : ?>
							<span class="sitevitals-action-disabled"><?php esc_html_e( 'No action available', 'sitevitals' ); ?></span>
						<?php endif; ?>
						<?php if ( ! $sitevitals_finding['is_ok'] && ! $sitevitals_is_group ) : ?>
							<br />
							<a class="sitevitals-ignore-link" href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::toggle_ignored_url( \SiteVitals\Admin\AdminPage::IGNORE_ACTION, $sitevitals_finding['id'] ) ); ?>">
								<?php esc_html_e( 'Ignore', 'sitevitals' ); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
			</tr>
			<?php if ( $sitevitals_is_group ) : ?>
				<tr class="sitevitals-sub-row">
					<td colspan="4">
						<details class="sitevitals-sub-list">
							<summary><?php esc_html_e( 'Click for details', 'sitevitals' ); ?></summary>
							<ul class="sitevitals-sub-list-items">
								<?php foreach ( $sitevitals_finding['vulnerabilities'] as $sitevitals_child ) : ?>
									<?php
									$sitevitals_child_points_tip = $sitevitals_child['points'] > 0
										? sprintf(
											/* translators: %d: points deducted from the Health Score. */
											__( '-%d points', 'sitevitals' ),
											$sitevitals_child['points']
										)
										: '';
									?>
									<li>
										<span class="sitevitals-sub-body">
											<span class="sitevitals-sub-severity sitevitals-severity sitevitals-severity-<?php echo esc_attr( $sitevitals_child['severity'] ); ?>"
												<?php if ( '' !== $sitevitals_child_points_tip ) : ?>
													title="<?php echo esc_attr( $sitevitals_child_points_tip ); ?>"
												<?php endif; ?>
											><?php echo esc_html( $sitevitals_child['severity_label'] ); ?></span>
											<span class="sitevitals-sub-title"><?php echo esc_html( $sitevitals_child['title'] ); ?></span>
											<span class="sitevitals-info-icon" title="<?php echo esc_attr( $sitevitals_child['description'] ); ?>">i</span>
											<br />
											<span class="sitevitals-sub-id"><?php echo esc_html( $sitevitals_child['id'] ); ?></span>
											<span class="sitevitals-sub-meta"><?php echo esc_html( $sitevitals_child['recommendation'] ); ?></span>
										</span>
										<span class="sitevitals-sub-actions">
											<?php if ( '' !== $sitevitals_child['link'] ) : ?>
												<a class="sitevitals-action-link" href="<?php echo esc_url( $sitevitals_child['link'] ); ?>" target="_blank" rel="noreferrer noopener">
													<?php esc_html_e( 'View source', 'sitevitals' ); ?>
												</a>
											<?php endif; ?>
											<?php if ( $sitevitals_restore ) : ?>
												<a class="sitevitals-ignore-link" href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::toggle_ignored_url( \SiteVitals\Admin\AdminPage::UNIGNORE_ACTION, $sitevitals_child['id'] ) ); ?>">
													<?php esc_html_e( 'Restore', 'sitevitals' ); ?>
												</a>
											<?php elseif ( ! $sitevitals_child['is_ok'] ) : ?>
												<a class="sitevitals-ignore-link" href="<?php echo esc_url( \SiteVitals\Admin\AdminPage::toggle_ignored_url( \SiteVitals\Admin\AdminPage::IGNORE_ACTION, $sitevitals_child['id'] ) ); ?>">
													<?php esc_html_e( 'Ignore', 'sitevitals' ); ?>
												</a>
											<?php endif; ?>
										</span>
									</li>
								<?php endforeach; ?>
							</ul>
						</details>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
	</tbody>
</table>