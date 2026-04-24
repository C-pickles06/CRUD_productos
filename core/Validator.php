<?php

namespace Core;

/**
 * Validador ligero basado en reglas tipo Laravel.
 * Ej: ['nombre' => 'required|min:3', 'precio' => 'required|numeric|min:0']
 */
class Validator
{
    private array $errors = [];

    public function __construct(private array $data, private array $rules)
    {
    }

    public static function make(array $data, array $rules): self
    {
        $v = new self($data, $rules);
        $v->run();
        return $v;
    }

    public function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->apply($field, $value, $name, $param);
            }
        }
    }

    private function apply(string $field, mixed $value, string $rule, ?string $param): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, "El campo {$field} es obligatorio.");
                }
                break;
            case 'min':
                if (is_numeric($value)) {
                    if ((float) $value < (float) $param) {
                        $this->addError($field, "El campo {$field} debe ser mayor o igual a {$param}.");
                    }
                } elseif (is_string($value) && mb_strlen($value) < (int) $param) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$param} caracteres.");
                }
                break;
            case 'max':
                if (is_numeric($value)) {
                    if ((float) $value > (float) $param) {
                        $this->addError($field, "El campo {$field} debe ser menor o igual a {$param}.");
                    }
                } elseif (is_string($value) && mb_strlen($value) > (int) $param) {
                    $this->addError($field, "El campo {$field} debe tener máximo {$param} caracteres.");
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico.");
                }
                break;
            case 'integer':
                if ($value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, "El campo {$field} debe ser un entero.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0] ?? '';
        }
        return $first;
    }
}
