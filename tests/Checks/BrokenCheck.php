<?php
declare( strict_types=1 );

namespace SiteVitals\Tests\Checks;

use SiteVitals\Checks\AbstractCheck;
use SiteVitals\Result;

final class BrokenCheck extends AbstractCheck {

	public function get_id(): string {
		return 'test/broken';
	}

	public function get_title(): string {
		return 'Check que falla';
	}

	public function run(): Result {
		throw new \RuntimeException( 'Fallo simulado' );
	}
}