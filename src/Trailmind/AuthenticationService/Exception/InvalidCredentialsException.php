<?php

namespace Trailmind\AuthenticationService\Exception;

class InvalidCredentialsException extends TokenRequestException
{
	/**
	 * @param array<string, string> $payload
	 */
	public function __construct(array $payload = [], ?\Throwable $previous = null)
	{
		parent::__construct(
			$payload + [
				'error' => 'invalid_grant',
				'error_description' => 'The user credentials were incorrect.',
			],
			401,
			$previous,
		);
	}
}
