<?php

namespace suffi\Simple\Helpers;

/**
 * Class ArrayHelper
 *
 * Хелпер для работы с массивами
 *
 * @package suffi\Simple\Helpers
 */
class ArrayHelper
{

    /**
     * Возвращает значение массива $source по ключу $key. Если не находит - $default.
     * Вложенность задается через точку
     *
     * @param array $source
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get(array $source = [], $key, $default = null)
    {
        if (!is_array($source)) {
            return $default;
        }

        if (!is_string($key) && !is_int($key)) {
            return $default;
        }

        if (is_array($source) && array_key_exists($key, $source)) {
            return $source[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $source = static::get($source, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_array($source)) {
            return array_key_exists($key, $source) ? $source[$key] : $default;
        } else {
            return $default;
        }
    }

    /**
     * Проверяет, есть ли в массиве $source ключ $key.
     * Вложенность задается через точку
     *
     * @param array $source
     * @param $key
     * @return bool
     */
    public static function has(array $source = [], $key):bool
    {
        if (!is_array($source)) {
            return false;
        }

        if (!is_string($key) && !is_int($key)) {
            return false;
        }

        if (is_array($source) && array_key_exists($key, $source)) {
            return true;
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $subkey = substr($key, 0, $pos);
            if (!static::has($source, $subkey)) {
                return false;
            }
            $source = static::get($source, $subkey);
            $key = substr($key, $pos + 1);
        }

        if (is_array($source)) {
            return array_key_exists($key, $source);
        } else {
            return false;
        }
    }

    /**
     * Установка в массиве $source ключа $key в значение $value.
     * Вложенность задается через точку
     *
     * @param array $source
     * @param $key
     * @param $value
     * @return void
     */
    public static function set(array &$source = [], $key, $value)
    {
        if (!is_array($source)) {
            return;
        }

        if (!is_string($key) && !is_int($key)) {
            return;
        }

        if (array_key_exists($key, $source)) {
            $source[$key] = $value;
            return;
        }

        if (($pos = strpos($key, '.')) !== false) {
            $childKey = substr($key, 0, $pos);
            $childSource = static::get($source, $childKey, []);
            if (!is_array($childSource)) {
                return;
            }
            self::set($childSource, substr($key, $pos + 1), $value);
            $source[$childKey] = $childSource;
            return;
        }

        $source[$key] = $value;
        return;
    }

    /**
     * Удаление в массиве $source ключа $key.
     * Вложенность задается через точку
     * @param array $source
     * @param $key
     */
    public static function remove(array &$source = [], $key)
    {
        if (!is_string($key) && !is_int($key)) {
            return;
        }

        if (array_key_exists($key, $source)) {
            unset($source[$key]);
            return;
        }

        if (($pos = strpos($key, '.')) !== false) {
            $childKey = substr($key, 0, $pos);
            $childSource = static::get($source, $childKey, []);
            if (!is_array($childSource)) {
                return;
            }
            self::remove($childSource, substr($key, $pos + 1));
            if (static::has($source, $childKey)) {
                $source[$childKey] = $childSource;
            }
            return;
        }
    }

    /**
     * Приведение массива к ассоциативному путем создания ключей из значения по ключу в подмассивах
     *
     * @param array $source
     * @param $key
     * @return array
     */
    public static function index(array $source = [], $key):array
    {
        if (!is_string($key) && !is_int($key)) {
            return $source;
        }

        $result = [];
        foreach ($source as $value) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $result[$value[$key]] = $value;
            }
        }
        return $result;
    }

    /**
     * Приведение массива к массиву с группировкой по ключу
     *
     * @param array $source
     * @param $key
     * @return array
     */
    public static function groups(array $source = [], $key):array
    {
        if (!is_string($key) && !is_int($key)) {
            return $source;
        }

        $result = [];
        foreach ($source as $value) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $result[$value[$key]][] = $value;
            }
        }
        return $result;
    }

    /**
     * Слияние двух массивов. Второй массив перетиравет значения первого в случае совпадений
     * @param array $source
     * @param array $dist
     * @return array
     */
    public static function merge(array $source = [], array $dist = []):array
    {
        foreach ($dist as $k => $v) {
            if (is_int($k)) {
                if (isset($source[$k])) {
                    $source[] = $v;
                } else {
                    $source[$k] = $v;
                }
            } elseif (is_array($v) && isset($source[$k]) && is_array($source[$k])) {
                $source[$k] = self::merge($source[$k], $v);
            } else {
                $source[$k] = $v;
            }
        }
        return $source;
    }
}
