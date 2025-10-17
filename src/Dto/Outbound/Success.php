<?php

namespace Dto\Outbound;

class Success extends EntityDto
{
    /**
     * @var bool
     */
    private $hideKeys = false;

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 200;
    }

    public function hideKeys(): void
    {
        $this->hideKeys = true;
    }

    /**
     * {@inheritDoc}
     */
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