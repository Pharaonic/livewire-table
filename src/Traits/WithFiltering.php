<?php

namespace Pharaonic\Livewire\Table\Traits;

/**
 * Table Filtering Methodology.
 *
 * @property array $filter
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithFiltering
{
    /**
     * filter columns & values
     *
     * @var array
     */
    public $filter = [];

    /**
     * Initialize Filtering
     *
     * @return void
     */
    public function initializeWithFiltering()
    {
        if ($this->filter && !empty($this->filter))
            $this->options->set('filter.columns', $this->filter);
    }

    /**
     * Filtering with values
     *
     * @param array $filter
     * @return void
     */
    public function filter()
    {
        if (!$this->options->get('filter.status') || !$this->filter || !is_array($this->filter)) return;

        $this->filter = array_filter($this->filter);

        if (empty($this->filter)) {
            $this->options->set('filter.columns', []);
        } else {
            $this->options->set('filter.columns', $this->filter);
        }

        $this->setOrder(null);
        $this->getFreshRecords(true);
    }
}
