<?php

namespace Pharaonic\Livewire\Table\Traits;

/**
 * Table Searching Methodology.
 *
 * @property string|null $search
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithSearching
{
    /**
     * Search value
     *
     * @var string|null
     */
    public $search;

    public function initializeWithSearching()
    {
        if ($this->search && $this->search != '') {
            $this->options->set('search.value', $this->search);
        } elseif (!$this->search && $this->options->get('search.status') && $this->options->get('search.value')) {
            $this->search = $this->options->get('search.value');
        }
    }

    /**
     * Searching for a value.
     *
     * @param string $search
     * @return void
     */
    protected function updatingSearch(string $search)
    {
        if (!$this->options->get('search.status')) return;

        if ($search == '') {
            $this->search = null;
            $this->options->set('search.value', null);
        } else {
            $this->options->set('search.value', $search);
        }

        $this->setOrder(null);
        $this->getFreshRecords(true);
    }
}
