<?php

namespace Pharaonic\Livewire\Table\Classes\Core;

/**
 * Table Options Container.
 *
 * @method mixed get(string $name, $default = null)
 * @method Options set(string $name, $value)
 * @method string|null getArrayAsString(string $arr)
 * @method string|null getAttribute(string $name, string $attribute)
 * @method void reset(array $options)
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
final class Options
{
    /**
     * OPTIONS LIST
     *
     * @var \Pharaonic\DotArray\DotArray
     */
    private $options;

    /**
     * Create a new instance.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->loadDefaultOptions();
        $this->parse($options);
    }

    /**
     * Getting option value.
     *
     * @param string $name
     * @param $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->options->get($name, $default);
    }

    /**
     * Setting option value.
     *
     * @param string $name
     * @param $value
     * @return Options
     */
    public function set(string $name, $value)
    {
        $this->options->set($name, $value);

        return $this;
    }

    /**
     * Getting array key/value as string
     *
     * @param string $arr
     * @return string|null
     */
    public function getArrayAsString(string $arr)
    {
        $arr = $this->get($arr, null);

        if (!$arr || !is_array($arr)) return;

        $output = '';

        foreach ($arr as $key => $value)
            $output .= (is_int($key) ? $value : $key . '="' . $value . '"') . ' ';

        return rtrim($output);
    }

    /**
     * Getting HTML Attribute
     *
     * @param string $name
     * @param string $attribute
     * @return string|null
     */
    public function getAttribute(string $name, string $attribute)
    {
        $name = $this->get($name, null);
        if (!$name) return;

        return $attribute . '="' . $name . '"';
    }

    /**
     * Reset all options
     *
     * @param array $options
     * @return void
     */
    public function reset(array $options)
    {
        $this->loadDefaultOptions();
        $this->parse($options);
    }

    /**
     * Load all deafult options.
     *
     * @return void
     */
    private function loadDefaultOptions()
    {
        $this->options = dot(config('Pharaonic.Livewire.table', config('livewire-table')));
    }

    /**
     * Parse all options (Create & Replace)
     *
     * @param array $options
     * @return array
     */
    private function parse(array $options)
    {
        foreach ($options as $name => $value)
            $this->set($name, $value);
    }
}
