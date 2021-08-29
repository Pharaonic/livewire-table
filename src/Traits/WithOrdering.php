<?php

namespace Pharaonic\Livewire\Table\Traits;

/**
 * Table Ordering Methodology.
 *
 * @property string|null $orderColumn
 * @property string $orderDirection
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithOrdering
{
    public $orderColumn;
    public $orderDirection = 'asc';

    /**
     * Initialize Ordering
     *
     * @return void
     */
    public function initializeWithOrdering()
    {
        $this->listeners += ['orderBy', 'orderByDesc', 'orderByToggle'];

        if ($this->orderColumn) {
            $this->setOrder($this->orderColumn, $this->orderDirection);
        } elseif (!$this->orderColumn && $this->options->get('order.status') && $this->options->get('order.column')) {
            $this->orderColumn = $this->options->get('order.column');
            $this->orderDirection = $this->options->get('order.direction', 'asc');
        }
    }

    /**
     * Register the order info.
     *
     * @param string|null $column
     * @param string $direction
     * @return void
     */
    private function setOrder(string $column = null, string $direction = 'asc')
    {
        if ($column)
            $this->columns->resetOrder();

        $this->orderColumn = $column;
        $this->orderDirection = $direction;

        $this->options->set('order.column', $column);
        $this->options->set('order.direction', $direction);

        if ($column)
            $this->columns->{$column}->setOrderDirection($direction);
        else
            $this->columns->resetOrder();
    }

    /**
     * Ascending order for a column.
     *
     * @param string $column
     * @return void
     */
    public function orderBy(string $column)
    {
        if (!$this->options->get('order.status') || !$this->columns->{$column} || !$this->columns->{$column}->orderable) return;

        $this->setOrder($column, 'asc');
        $this->getFreshRecords(true);
    }

    /**
     * Descending order for a column.
     *
     * @param string $column
     * @return void
     */
    public function orderByDesc(string $column)
    {
        if (!$this->options->get('order.status') || !$this->columns->{$column} || !$this->columns->{$column}->orderable) return;

        $this->setOrder($column, 'desc');
        $this->getFreshRecords(true);
    }

    /**
     * Toggling the order for a column.
     *
     * @param string $column
     * @return void
     */
    public function orderByToggle(string $column)
    {
        if (!$this->options->get('order.status') || !$this->columns->{$column} || !$this->columns->{$column}->orderable) return;

        if ($this->orderColumn == $column)
            $this->setOrder($column, $this->orderDirection == 'asc' ? 'desc' : 'asc');
        else
            $this->setOrder($column, 'asc');

        $this->getFreshRecords(true);
    }
}
