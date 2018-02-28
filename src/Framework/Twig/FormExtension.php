<?php

namespace Framework\Twig;

class FormExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }

    /**
     * Generate html code for a field.
     * @param array $context
     * @param string $key
     * @param $value
     * @param string $label|null
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $attributes = [
            'class' => 'form-control',
            'id' => $key,
            'name' => $key
        ];
        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "
            <div class=\"".$class."\">
                <label for=\"{$key}\">{$label}</label>
                {$input}
                {$error}
            </div>
        ";
    }

    /**
     * Return small tag with error class if there is an error.
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return "<small class=\"form-text text-muted\">{$error}</small>";
        }

        return "";
    }

    /**
     * Generate an input field with attributes.
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" ". $this->getHtmlFromArray($attributes) ." value=\"{$value}\">";
    }

    /**
     * Generate a textarea field with attributes.
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea ". $this->getHtmlFromArray($attributes).">{$value}</textarea>";
    }

    /**
     * Transform array data to html.
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes)
    {
        return implode(' ', array_map(function($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }

}