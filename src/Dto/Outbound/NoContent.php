<?php

namespace Dto\Outbound;

class NoContent extends EntityDto
{
    public function getStatusCode()
    {
        return 204;
    }
}
