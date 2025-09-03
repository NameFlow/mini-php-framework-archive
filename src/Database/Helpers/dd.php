<?php

declare(strict_types=1);

// Объявляем неймспейс, чтобы соответствовать PSR-4,
// но сама функция будет глобальной из-за того, как мы ее регистрируем.
namespace App\Helpers; 

if (!function_exists('App\Helpers\dd')) {
    /**
     * Dumps the given variables and ends the script.
     *
     * @param  mixed  ...$vars
     * @return void
     */
    function dd(...$vars): void
    {
        echo "<pre>";
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo "</pre>";
        die();
    }
}