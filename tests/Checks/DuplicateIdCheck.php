<?php
declare( strict_types=1 );

namespace SiteVitals\Tests\Checks;

use SiteVitals\Checks\AbstractCheck;
use SiteVitals\Result;

final class DuplicateIdCheck extends AbstractCheck {

	public function get_id(): string {
		return 'test/duplicate';
	}

	public function get_title(): string {
		return 'Check con id duplicado';
	}

	public function run(): Result {
		return $this->result( Result::SEVERITY_OK, null );
	}
}