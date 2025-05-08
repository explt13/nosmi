<?php

namespace Explt13\Nosmi\Interfaces;

interface ExchangeInterface
{
    public function withHeaders(array $headers): static;
}