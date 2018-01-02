<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * Get data from session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Add info to session
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void;

    /**
     * Remove a key from session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}
