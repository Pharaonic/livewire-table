<?php

namespace Pharaonic\Livewire\Table\Classes\Structure;

use Exception;

/**
 * Columns Container
 *
 * @method array getSearchables()
 * @method array getOrderables()
 * @method array getFilterables()
 * @method array getRowData(object $record)
 * @method void resetOrder()
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
final class Columns
{
    /**
     * Columns List
     *
     * @var array
     */
    public $list = [];

    /**
     * Create a new Columns instance
     *
     * @param array $columns
     */
    function __construct(array $columns)
    {
        foreach ($columns as $column) {
            if (!isset($column['name']))
                throw new Exception('Every column should have a name.');

            $this->{$column['name']} = $column;
        }
    }

    /**
     * Setting Column
     *
     * @param string $name
     * @param array $options
     */
    function __set(string $name, array $options)
    {
        $this->list[$name] = new Column($options);
    }

    /**
     * Getting column value
     *
     * @param string $name
     * @return Column|null
     */
    function __get(string $name)
    {
        return $this->list[$name] ?? null;
    }

    /**
     * Get all the searchable columns data.
     *
     * @return array
     */
    public function getSearchables()
    {
        return array_values(array_filter(array_map(function ($col) {
            return $col->searchable ? $col->data : null;
        }, $this->list)));
    }

    /**
     * Get all the orderable columns data.
     *
     * @return array
     */
    public function getOrderables()
    {
        return array_values(array_filter(array_map(function ($col) {
            return $col->orderable ? $col->data : null;
        }, $this->list)));
    }

    /**
     * Get all the filterable columns data.
     *
     * @param array $columns
     * @return array
     */
    public function getFilterables(array $columns)
    {
        $output = [];

        if (empty($columns)) return $output;

        foreach ($columns as $name => $value) {
            if (!$this->{$name} || !$this->{$name}->filterable) continue;

            $output[$this->{$name}->data] = $value;
        }

        return $output;
    }

    /**
     * Collect the row needs of data
     *
     * @param object $record
     * @return array
     */
    public function getRowData(object $record)
    {
        $output = [];

        foreach ($this->list as $column) $output[] = [
            'class'         => $column->getClass(),
            'attributes'    => $column->getAttributes(),
            'data'          => $record->{$column->getData() ?? $column->name} ?? ($column->getView() ?? null)
        ];

        return $output;
    }

    /**
     * Reset ordering direction.
     *
     * @return void
     */
    public function resetOrder()
    {
        foreach ($this->list as $key => &$col)
            $col->direction = null;
    }
}
