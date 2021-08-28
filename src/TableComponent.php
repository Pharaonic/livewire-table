<?php

namespace Pharaonic\Livewire\Table;

use Livewire\Component as LivewireComponent;
use Pharaonic\Livewire\Table\Traits\{
    WithFiltering,
    WithOrdering,
    WithPagination,
    WithSearching,
    WithTable
};

/**
 * Table Component of Pharaonic
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
abstract class TableComponent extends LivewireComponent
{
    use WithTable, WithPagination, WithSearching, WithFiltering, WithOrdering;

    /**
     * Reset all properities & records.
     *
     * @return void
     */
    public function resetAll()
    {
        $this->reset();
        $this->initialized = true;
        $this->columns->resetOrder();
        $this->options->reset(method_exists($this, 'options') ? call_user_func([$this, 'options']) : []);
        $this->getFreshRecords(true);
    }

    /**
     * Refresh all the view with content
     *
     * @return void
     */
    public function refresh()
    {
        $this->getFreshRecords();
        $this->callMethod('$refresh');
    }

    /**
     * The view what the user gonna see.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view($this->options->get('table.component'), [
            'records'   => $this->records,
            'options'   => $this->options,
            'columns'   => $this->columns,
        ]);
    }
}
