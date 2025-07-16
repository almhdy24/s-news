<?php
declare(strict_types=1);

namespace Core\DB;

use Core\DB\Exception\ValidationException;

class Schema
{
    private array $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function validate(array $record, bool $isUpdate = false): void
    {
        foreach ($this->fields as $field => $rules) {
            $valueExists = array_key_exists($field, $record);
            $value = $record[$field] ?? null;

            if (($rules['required'] ?? false) && !$isUpdate && !$valueExists) {
                throw new ValidationException("Field '$field' is required.");
            }

            if ($valueExists && isset($rules['type'])) {
                $valid = match($rules['type']) {
                    'int' => is_int($value),
                    'string' => is_string($value),
                    'bool' => is_bool($value),
                    'float' => is_float($value),
                    default => true
                };

                if (!$valid) {
                    throw new ValidationException("Field '$field' must be of type {$rules['type']}.");
                }
            }

            if ($valueExists && isset($rules['minLength']) && is_string($value)) {
                if (strlen($value) < $rules['minLength']) {
                    throw new ValidationException("Field '$field' must be at least {$rules['minLength']} characters.");
                }
            }
        }
    }
}