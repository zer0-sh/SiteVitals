<?php
/**
 * Contrato para checks que generan múltiples resultados.
 *
 * @package SiteVitals
 */

declare( strict_types=1 );

namespace SiteVitals\Checks;

use SiteVitals\Result;

/**
 * Extensión opcional de CheckInterface para checks que producen un
 * resultado por hallazgo (p. ej. un componente desactualizado por Plugin
 * o Tema). El Checker los aplana a los mismos resultados que los simples.
 */
interface MultiCheckInterface {

	/**
	 * Devuelve los resultados del check, uno por hallazgo.
	 *
	 * @return Result[]
	 */
	public function run_many(): array;
}
