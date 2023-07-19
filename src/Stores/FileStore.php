<?php

namespace PanicControl\Stores;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlFileNotFound;
use PanicControl\Facades\PanicControl;

final class FileStore implements Store
{
    public function all(): array
    {
        $storage = Storage::disk(config('panic-control.stores.file.disk'));

        return $storage->exists(config('panic-control.stores.file.path'))
            ? collect(json_decode($storage->get(config('panic-control.stores.file.path')), true))->keyBy('name')->toArray()
            : throw new PanicControlFileNotFound();
    }

    public function create(array $parameters): array
    {
        try {
            $panics = $this->all();
        } catch (PanicControlFileNotFound $th) {
            $panics = [];
        }

        $panics[] = $parameters + [
            'id' => (empty($panics)) ? 1 : max(array_column($panics, 'id')) + 1,
            'rules' => [],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        Storage::disk(config('panic-control.stores.file.disk'))
            ->put(config('panic-control.stores.file.path'), json_encode($panics, JSON_PRETTY_PRINT));

        return Arr::last($panics);
    }

    public function update(string|int $panicName, array $parameters): array
    {
        $panic = PanicControl::find($panicName);

        if (empty($panic)) {
            Log::error('Panic Control: não encontrado.', ['name' => $panicName, 'parameters' => $parameters]);
            throw new PanicControlDoesNotExist($panicName);
        }

        $parameters = array_merge(
            $panic,
            $parameters
        );

        $panics = $this->all();

        $panics[$parameters['name']] = $parameters;

        Storage::disk(config('panic-control.stores.file.disk'))
            ->put(config('panic-control.stores.file.path'), json_encode(array_values($panics), JSON_PRETTY_PRINT));

        return $parameters;
    }

    public function count(): int
    {
        return count($this->all());
    }
}
