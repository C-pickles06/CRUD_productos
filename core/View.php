<?php

namespace Core;

/**
 * Renderizador de vistas PHP con layout opcional.
 */
class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $viewFile = self::path($view);
        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        $content = self::capture($viewFile, $data);

        if ($layout === null) {
            return $content;
        }

        $layoutFile = self::path($layout);
        if (!is_file($layoutFile)) {
            return $content;
        }

        return self::capture($layoutFile, array_merge($data, ['content' => $content]));
    }

    public static function display(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        echo self::render($view, $data, $layout);
    }

    private static function path(string $view): string
    {
        $relative = str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        return dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'Views' . DIRECTORY_SEPARATOR
            . $relative;
    }

    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
