<?php

namespace Trailmind\AuthenticationService\Exception;

class InvalidRefreshTokenException extends TokenRequestException
{
	/**
	 * @param array<string, string> $payload
	 */
	public function __construct(array $payload = [], ?\Throwable $previous = null)
	{
		parent::__construct(
		    $payload + [
		        'error' => 'invalid_grant',
		        'error_description' => 'The refresh token is invalid.',
		    ],
		    401,
		    $previous,
		);
	}
}
