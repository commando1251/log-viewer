<?php

namespace Commando1251\LogViewer\Concerns\LogReader;

use Commando1251\LogViewer\Direction;

trait CanSetDirectionUsingIndex
{
    public function reverse(): static
    {
        return $this->setDirection(Direction::Backward);
    }

    public function forward(): static
    {
        return $this->setDirection(Direction::Forward);
    }

    public function setDirection(?string $direction = null): static
    {
        $direction = $direction === Direction::Backward
            ? Direction::Backward
            : Direction::Forward;

        $this->index()->setDirection($direction);

        return $this->reset();
    }
}
