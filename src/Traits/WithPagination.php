<?php

namespace Pharaonic\Livewire\Table\Traits;

use Illuminate\Pagination\Paginator;
use Livewire\WithPagination as LivewireWithPagination;

/**
 * Table Pagination Methodology.
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithPagination
{
    use LivewireWithPagination;

    /**
     * Customize the Livewire initializeWithPagination
     *
     * @return void
     */
    public function initializeWithPagination()
    {
        $this->page = $this->resolvePage();
        $this->options->set('paginate.current', $this->page);

        Paginator::currentPageResolver(function () {
            return (int) $this->page;
        });

        Paginator::defaultView($this->paginationView());
        Paginator::defaultSimpleView($this->paginationSimpleView());
    }

    /**
     * Customize the Livewire setPage
     *
     * @param integer $page
     * @return void
     */
    public function setPage($page)
    {
        $this->page = $page;

        $this->options->set('paginate.current', $this->page);
        $this->getFreshRecords();
    }
}
