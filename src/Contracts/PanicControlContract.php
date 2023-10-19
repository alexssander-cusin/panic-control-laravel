<?php

namespace PanicControl\Contracts;

interface PanicControlContract
{
    public function getAll(): array;

    public function validator(string|bool $ignore = false): array;

    public function store(array $parameters): array;

    public function update(string|int $panic, array $parameters): array;

    public function count(): int;
}
