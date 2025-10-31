<?php

namespace Dto\Outbound;

class NoContent extends EntityDto
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 204;
    }
}
