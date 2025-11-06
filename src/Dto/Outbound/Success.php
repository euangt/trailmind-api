<?php

namespace Dto\Outbound;

class Success extends EntityDto
{
    /**
     * @var bool
     */
    private $hideKeys = false;

    public function getStatusCode()
    {
        return 200;
    }

    public function hideKeys(): void
    {
        $this->hideKeys = true;
    }

    public function jsonSerialize(): mixed
    {
        if ($this->hideKeys) {
            $this->removeKeys();
        }

        return $this->data;
    }

    private function removeKeys(): void
    {
        $this->data = reset($this->data);
    }
}