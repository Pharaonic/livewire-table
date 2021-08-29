<?php

namespace Pharaonic\Livewire\Table;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component as LivewireComponent;
use Pharaonic\Livewire\Table\Traits\WithCustomColumns;

/**
 * Row Component of Pharaonic
 *
 * @property Model $record
 * @property array $columns
 * @property array $options
 *
 * @method mixed mount(Model $record, array $columns, array $options)
 * @method \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory render()
 * @method mixed refreshTable()
 * @method mixed refreshColumns(...$columns)
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class RowComponent extends LivewireComponent
{
    use WithCustomColumns;

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

        if (method_exists($this, 'init'))
            call_user_func_array([$this, 'init'], [$record, $columns, $options]);
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

    /**
     * Refresh all the table records
     *
     * @return void
     */
    protected function refreshTable()
    {
        $this->emitUp('refresh');
    }
}
