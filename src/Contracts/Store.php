<?php

namespace PanicControl\Contracts;

interface Store
{
    public function all(): array;

    public function create(array $parameters): array;

    public function update(string|int $panic, array $parameters): array;
}
