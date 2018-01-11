<?php
namespace Framework;

use Framework\Validator\ValidationError;

class Validator
{
    private $params;

    /**
     * @var string[]
     */
    private $errors =  [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Check if the fields are not empty.
     * @param string[] ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys)
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * Check if fields are in array.
     * @param string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Check if the element is a slug.
     * @param string[] $keys
     * @return Validator
     * @internal param string $key
     */
    public function slug(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            $pattern = '/^([a-z0-9]+-?)+$/';
            if (!is_null($value) && !preg_match($pattern, $value)) {
                $this->addError($key, 'slug');
            }
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null ):self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (
            !is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (
            !is_null($min) &&
            ($length < $min)
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (
            !is_null($max) &&
            ($length > $max)
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }

        return $this;
    }

    /**
     * Get errors.
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add error.
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }

        return null;
    }
}