<?php
namespace Framework\Validator;

class ValidationError
{
    private $key;
    private $rule;
    private $messages = [
        'required' => 'The field %s is required',
        'empty' => 'The field %s is empty',
        'slug' => 'The slug %s is not valid',
        'minLength' => 'The field %s has to contain more than %d characters',
        'maxLength' => 'The field %s has to contain less than %d characters',
        'betweenLength' => 'The field %s has to contain between %d than %d characters',
        'datetime' => 'The field %s has to be a valid datetime (%s)'
    ];
    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string) call_user_func_array('sprintf', $params);
    }
}