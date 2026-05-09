<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use ReflectionProperty;

abstract class BaseDTO
{
    protected const MAP = [];

    protected array $except = [];

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    // only init properties that exist in request
    public static function fromRequestPartial(Request $request): static
    {
        $data = $request->validated();
        $init = [];

        foreach (static::MAP as $requestKey => $propertyName) {
            if (array_key_exists($requestKey, $data)) {
                $init[$propertyName] = $data[$requestKey];
            }
        }

        return new static($init);
    }

    //  init properties that exist in request, if not exist, init with null
    public static function fromRequest(Request $request): static
    {
        $data = $request->validated();
        $init = [];

        $reflection = new \ReflectionClass(static::class);
        $defaultProperties = $reflection->getDefaultProperties();

        foreach (static::MAP as $requestKey => $propertyName) {
            if (array_key_exists($requestKey, $data)) {
                $init[$propertyName] = $data[$requestKey];
            } else {
                $init[$propertyName] = $defaultProperties[$propertyName] ?? null;
            }
        }

        return new static($init);
    }

    public function toArray(): array
    {
        $result = [];
        foreach (static::MAP as $requestKey => $propertyName) {
            if ($this->hasValue($propertyName) && ! in_array($propertyName, $this->except)) {
                $result[$requestKey] = $this->{$propertyName};
            }
        }

        return $result;
    }

    protected function hasValue(string $property): bool
    {
        $rp = new ReflectionProperty($this, $property);

        return $rp->isInitialized($this);
    }
}
