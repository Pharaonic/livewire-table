<?php

namespace Pharaonic\Livewire\Table;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;

/**
 * Row Component of Pharaonic
 *
 * @property Model $record
 * @property array $columns
 * @property array $options
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class RowComponent extends LivewireComponent
{
    public $record;
    public $columns;
    public $options;

    /**
     * Component Constructor
     *
     * @param Model $record
     * @param array $columns
     * @param array $options
     * @return void
     */
    public function mount(Model $record, array $columns, array $options)
    {
        $this->record = $record;
        $this->columns = $columns;
        $this->options = $options;
    }

    /**
     * The view what the user gonna see.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view('livewire-table::row');
    }
}
