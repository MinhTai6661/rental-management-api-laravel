<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use ReflectionProperty;

readonly abstract class BaseDTO
{
    protected const MAP = [];
    protected static function defaults(): array
    {
        return [];
    }
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    // only set properties that are present in the request, ignore missing ones
    public static function fromRequestPartial(Request $request): static
    {
        $data = $request->validated();
        $defaults = static::defaults();
        $init = [];

        foreach (static::MAP as $requestKey => $propertyName) {
            if (array_key_exists($requestKey, $data)) {
                $init[$propertyName] = $data[$requestKey];
            } elseif (array_key_exists($propertyName, $defaults)) {
                $init[$propertyName] = $defaults[$propertyName];
            }
        }

        return new static($init);
    }

    // set all properties, if missing in request, it will set null or default 
    public static function fromRequest(Request $request): static
    {
        $data = $request->validated();
        $defaults = static::defaults();
        $init = [];

        foreach (static::MAP as $requestKey => $propertyName) {
            $init[$propertyName] = $data[$requestKey] ?? ($defaults[$propertyName] ?? null);
        }

        return new static($init);
    }

    public function toArray(): array
    {
        $result = [];
        foreach (static::MAP as $requestKey => $propertyName) {
            if ($this->isInitialized($propertyName)) {
                $result[$requestKey] = $this->{$propertyName};
            }
        }

        return $result;
    }

    protected function isInitialized(string $property): bool
    {
        $rp = new ReflectionProperty($this, $property);
        return $rp->isInitialized($this);
    }
}
