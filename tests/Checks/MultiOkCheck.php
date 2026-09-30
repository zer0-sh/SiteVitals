<?php
declare( strict_types=1 );

namespace SiteVitals\Tests\Checks;

use SiteVitals\Checks\AbstractCheck;
use SiteVitals\Checks\MultiCheckInterface;
use SiteVitals\Result;

final class MultiOkCheck extends AbstractCheck implements MultiCheckInterface {

	public function get_id(): string {
		return 'test/multi';
	}

	public function get_title(): string {
		return 'Check multi-resultado';
	}

	public function run(): Result {
		return $this->result( Result::SEVERITY_OK, null );
	}

	public function run_many(): array {
		return array(
			$this->result( Result::SEVERITY_OK, 'a' ),
			$this->result( Result::SEVERITY_OK, 'b' ),
		);
	}
}