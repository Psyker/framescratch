<?php

namespace Framework\Twig;

class TimeExtension extends \Twig_Extension
{
    /**
     * @return array \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param \DateTime $date
     * @param string $format
     * @return string
     */
    public function ago($date, string $format = 'Y/m/d H:i')
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        return '<span class="timeago" datetime="' .
            $date->format(\DateTime::ISO8601) .'">'.
            $date->format($format) .
            '</span>';
    }
}
