<?php


if (!function_exists('icon')) {
    /**
     * جلب أيقونة حسب الاسم
     *
     * @param string $name
     * @param string|null $set
     * @param string|null $classes
     * @return string
     */
    function icon(string $name, string $set = null, string $classes = null): string
    {
        $config = config('icons');
        $set = $set ?: $config['default_set'];

        $icons = $config[$set] ?? [];
        $icon = $icons[$name] ?? $icons['default'] ?? null;
        if (!$icon) return '';

        $extra = $classes ? ' ' . $classes : '';

        // Ionicons
        if ($set === 'ion') {
            return "<ion-icon name=\"{$icon}\" class=\"{$classes} \" role=\"img\" class=\"md hydrated\"></ion-icon>";
        }

        // FontAwesome / Bootstrap Icons
        $classPrefix = $set === 'fa' ? 'fas' : ($set === 'bi' ? 'bi' : '');

        return "<i class=\"{$classPrefix} {$icon}{$extra}\"></i>";
    }
}



if (!function_exists('svg_icon')) {
    /**
     * جلب أيقونة SVG من resources/svg
     *
     * @param string $name اسم ملف SVG بدون الامتداد
     * @param string $classes أي كلاسات إضافية للعنصر <svg>
     * @return string|null
     */
    function svg_icon(string $name, string $classes = ''): ?string
    {
        $path = resource_path("svg/{$name}.svg");

        if (!file_exists($path)) {
            return null;
        }

        $svg = file_get_contents($path);

        // إضافة الكلاسات لو لم تكن موجودة
        if ($classes) {
            // أضف الكلاسات إلى وسم <svg>
            $svg = preg_replace(
                '/<svg([^>]*)>/i',
                '<svg$1 class="' . $classes . '">',
                $svg,
                1
            );
        }

        return $svg;
    }
}
if (!function_exists('getIconHtml')) {
    /**
     * جلب أيقونة سواء كانت من FontAwesome أو SVG
     *
     * @param string $name اسم الأيقونة
     * @param string|null $set نوع الأيقونات (fa أو bi أو svg)
     * @param string|null $classes كلاس إضافي (اختياري)
     * @return string كود HTML للأيقونة
     */
    function getIconHtml(string $name, ?string $set = null, ?string $classes = null): string
    {

        if (Str::startsWith($name, '<')) {
            return $name;
        }
        if ($set === 'svg') {
            $svg = svg_icon($name, $classes ?? '');
            return $svg ?? '';
        }

        return icon($name, $set ?? 'fa', $classes ?? '');
    }
}
