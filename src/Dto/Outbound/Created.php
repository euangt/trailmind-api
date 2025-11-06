<?php

namespace Dto\Outbound;

class Created extends EntityDto
{
    public function getStatusCode()
    {
        return 201;
    }
}
