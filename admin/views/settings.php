<?php
/**
 * Vista de ajustes de SiteVitals.
 *
 * @package SiteVitals
 *
 * @var array $data Datos preparados por AdminPage::render_settings().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sitevitals_settings = $data['settings'];
?>
<div class="wrap sitevitals-wrap">
	<h1><?php esc_html_e( 'SiteVitals Settings', 'sitevitals' ); ?></h1>

	<?php if ( 'updated' === $data['notice'] ) : ?>
		<div class="notice notice-success is-dismissible inline">
			<p><?php esc_html_e( 'Settings saved successfully.', 'sitevitals' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="sitevitals-frequency"><?php esc_html_e( 'Scan frequency', 'sitevitals' ); ?></label>
				</th>
				<td>
					<select name="sitevitals[frequency]" id="sitevitals-frequency">
						<?php
						$sitevitals_frequencies = array(
							'disabled' => __( 'Disabled', 'sitevitals' ),
							'daily'    => __( 'Daily', 'sitevitals' ),
							'weekly'   => __( 'Weekly', 'sitevitals' ),
							'monthly'  => __( 'Monthly', 'sitevitals' ),
						);

						foreach ( $sitevitals_frequencies as $sitevitals_value => $sitevitals_label ) :
							?>
							<option value="<?php echo esc_attr( $sitevitals_value ); ?>" <?php selected( $sitevitals_settings->get_frequency(), $sitevitals_value ); ?>>
								<?php echo esc_html( $sitevitals_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'How often the site status is checked automatically.', 'sitevitals' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="sitevitals-mail-enabled"><?php esc_html_e( 'Email reports', 'sitevitals' ); ?></label>
				</th>
				<td>
					<label for="sitevitals-mail-enabled">
						<input
							type="checkbox"
							name="sitevitals[mail_enabled]"
							id="sitevitals-mail-enabled"
							value="1"
							<?php checked( $sitevitals_settings->is_mail_enabled(), true ); ?>
						/>
						<?php esc_html_e( 'Send results by email', 'sitevitals' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'After each manual scan and when the diagnosis changes in scheduled ones.', 'sitevitals' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="sitevitals-recipient"><?php esc_html_e( 'Recipient', 'sitevitals' ); ?></label>
				</th>
				<td>
					<input
						type="email"
						name="sitevitals[recipient]"
						id="sitevitals-recipient"
						class="regular-text"
						value="<?php echo esc_attr( $sitevitals_settings->get_recipient() ); ?>"
						placeholder="<?php echo esc_attr( (string) get_option( 'admin_email', '' ) ); ?>"
					/>
					<p class="description">
						<?php
						printf(
							/* translators: %s: default administrator email. */
							esc_html__( 'Leave empty to use the administrator email («%s»).', 'sitevitals' ),
							esc_html( (string) get_option( 'admin_email', '' ) )
						);
						?>
					</p>
				</td>
			</tr>
		<tr>
				<th scope="row">
					<label for="sitevitals-time-24h"><?php esc_html_e( 'Time format', 'sitevitals' ); ?></label>
				</th>
				<td>
					<label for="sitevitals-time-24h">
						<input
							type="checkbox"
							name="sitevitals[time_24h]"
							id="sitevitals-time-24h"
							value="1"
							<?php checked( $sitevitals_settings->is_time_24h(), true ); ?>
						/>
						<?php esc_html_e( 'Use 24-hour format', 'sitevitals' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Shows scan and scheduling times in 24-hour format.', 'sitevitals' ); ?>
					</p>
				</td>
			</tr>
		</table>

		<?php wp_nonce_field( 'sitevitals_settings' ); ?>
		<input type="hidden" name="action" value="sitevitals_settings" />
		<?php submit_button( __( 'Save settings', 'sitevitals' ), 'primary', 'submit', false ); ?>
	</form>

	<div class="notice notice-info inline sitevitals-feature-request">
		<p><strong><?php esc_html_e( 'Have a feature request?', 'sitevitals' ); ?></strong></p>
		<p>
			<?php esc_html_e( 'Create an issue in the repository or send me an email at', 'sitevitals' ); ?>
			<a href="<?php echo esc_url( 'https://github.com/zer0-sh/sitevitals/issues/new' ); ?>"><?php esc_html_e( 'Create an issue', 'sitevitals' ); ?></a>
			<?php esc_html_e( 'or', 'sitevitals' ); ?>
			<a href="<?php echo esc_url( 'mailto:zer0sh@protonmail.ch?subject=SiteVitals%20feature%20request' ); ?>">zer0sh@protonmail.ch</a>
			<?php esc_html_e( 'with your request. I will work on it as soon as I have some time.', 'sitevitals' ); ?>
		</p>
	</div>

	<?php $sitevitals_time_format = $sitevitals_settings->is_time_24h() ? 'H:i' : (string) get_option( 'time_format' ); ?>

	<p>
		<strong>
			<?php
			printf(
				/* translators: 1: configured frequency, 2: date of the next run. */
				esc_html__( 'Scheduled scan: %1$s. Next run: %2$s.', 'sitevitals' ),
				esc_html( $sitevitals_frequencies[ $sitevitals_settings->get_frequency() ] ),
				null !== $data['next_run']
					? esc_html(
						date_i18n(
							get_option( 'date_format' ) . ' ' . $sitevitals_time_format,
							$data['next_run']
						)
					)
					: esc_html__( 'not scheduled', 'sitevitals' )
			);
			?>
		</strong>
	</p>
</div>
