<?php

namespace Pharaonic\Livewire\Table\Classes\Structure;

/**
 * Column Object
 *
 * @property string $name
 * @property string $title
 * @property string|null $data
 * @property bool $orderable
 * @property bool $searchable
 *
 * @method string|null getClass()
 * @method string|null getAttributes()
 * @method string|null getHeadClass()
 * @method string|null getHeadAttributes()
 * @method string|null getData()
 * @method void setOrderDirection(string $direction)
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
final class Column
{
    public $name, $title, $data, $orderable, $searchable, $filterable, $direction, $view;
    private $class, $attributes, $head;

    /**
     * Create a new column instance.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->name         = $options['name'];
        $this->title        = isset($options['title']) ? __($options['title']) : null;
        $this->data         = isset($options['data']) ? $options['data'] : null;

        $this->orderable    = isset($options['orderable']) ? $options['orderable'] : false;
        $this->searchable   = isset($options['searchable']) ? $options['searchable'] : false;
        $this->filterable   = isset($options['filterable']) ? $options['filterable'] : false;

        $this->class        = isset($options['class']) ? $options['class'] : null;
        $this->attributes   = isset($options['attributes']) ? $options['attributes'] : [];
        $this->view         = isset($options['view']) ? $options['view'] : null;

        $this->head         = [
            'class'         => $options['head.class'] ?? null,
            'attributes'    => $options['head.attributes'] ?? [],
        ];

        return $this;
    }

    /**
     * Getting the real data name without relationships
     *
     * @return string|null
     */
    public function getData()
    {
        if (strpos($this->data, '.') !== false) {
            $data = explode('.', $this->data);
            return array_pop($data);
        }

        return $this->data;
    }

    /**
     * Setting Order Direction
     *
     * @param string $direction
     * @return void
     */
    public function setOrderDirection(string $direction)
    {
        $this->direction = $direction;
    }

    /**
     * Getting full classes string
     *
     * @return null|string
     */
    public function getClass()
    {
        if (!$this->class) return null;

        return 'class="' . $this->class . '"';
    }

    /**
     * Getting full attributes string.
     *
     * @return null|string
     */
    public function getAttributes()
    {
        if (empty($this->attributes)) return null;

        $output = '';

        foreach ($this->attributes as $name => $value)
            $output .= (is_int($name) ? $value : $name . '="' . $value . '"') . ' ';

        return rtrim($output);
    }

    /**
     * Getting full class string of <th>
     *
     * @return string|null
     */
    public function getHeadClass()
    {
        if (!$this->head['class']) return null;

        return 'class="' . $this->head['class'] . '"';
    }

    /**
     * Getting full attributes string of <th>
     *
     * @return string|null
     */
    public function getHeadAttributes()
    {
        if (empty($this->head['attributes'])) return null;

        $output = '';

        foreach ($this->head['attributes'] as $name => $value)
            $output .= (is_int($name) ? $value : $name . '="' . $value . '"') . ' ';

        return rtrim($output);
    }
}
