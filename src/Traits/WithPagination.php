<?php

namespace Pharaonic\Livewire\Table\Traits;

use Illuminate\Pagination\Paginator;
use Livewire\WithPagination as LivewireWithPagination;

/**
 * Table Pagination Methodology.
 *
 * @property integer $paginateLength
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithPagination
{
    use LivewireWithPagination;

    /**
     * Pagination Length
     *
     * @return integer
     */
    public $paginateLength = 10;

    /**
     * Customize the Livewire initializeWithPagination
     *
     * @return void
     */
    public function initializeWithPagination()
    {
        foreach ($this->paginators as $key => $value) {
            $this->$key = $value;
        }

        $this->page = $this->resolvePage();

        $this->paginators['page'] = $this->page;

        Paginator::currentPageResolver(function ($pageName) {
            if (! isset($this->paginators[$pageName])) {
                $this->paginators[$pageName] = request()->query($pageName, 1);
            }

            return (int) $this->paginators[$pageName];
        });

        $this->options->set('paginate.current', $this->page);
        $this->options->set('paginate.length', $this->paginateLength);

        Paginator::defaultView($this->paginationView());
        Paginator::defaultSimpleView($this->paginationSimpleView());
    }

    public function setPage($page, $pageName = 'page')
    {
        $beforePaginatorMethod = 'updatingPaginators';
        $afterPaginatorMethod = 'updatedPaginators';

        $beforeMethod = 'updating' . $pageName;
        $afterMethod = 'updated' . $pageName;

        if (method_exists($this, $beforePaginatorMethod)) {
            $this->{$beforePaginatorMethod}($page, $pageName);
        }

        if (method_exists($this, $beforeMethod)) {
            $this->{$beforeMethod}($page, null);
        }

        $this->paginators[$pageName] =  $page;

        $this->{$pageName} = $page;
        
        $this->options->set('paginate.current', $this->page);
        $this->getFreshRecords();

        if (method_exists($this, $afterPaginatorMethod)) {
            $this->{$afterPaginatorMethod}($page, $pageName);
        }

        if (method_exists($this, $afterMethod)) {
            $this->{$afterMethod}($page, null);
        }
    }

    /**
     * Update Paginate Length
     *
     * @param integer $length
     * @return void
     */
    public function updatingPaginateLength(int $length)
    {
        $this->paginateLength = $length;
        $this->options->set('paginate.length', $length);

        $this->setPage(1);
    }
}
