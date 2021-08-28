<?php

namespace Pharaonic\Livewire\Table\Classes\Parsers;

use Pharaonic\Livewire\Table\Classes\Core\FakeModel;
use Pharaonic\Livewire\Table\Classes\Core\Options;
use Pharaonic\Livewire\Table\Classes\Core\Parser;
use Pharaonic\Livewire\Table\Classes\Structure\Columns;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Query Parser
 *
 * @method void run()
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class QueryParser extends Parser
{
    protected $collection, $options, $columns, $customColumns;

    /**
     * Create a new parser instance.
     *
     * @param collection $collection
     * @param Options $options
     * @param Columns $columns
     * @param array $customColumns
     */
    public function __construct(Builder $collection, Options $options, Columns $columns, array $customColumns = [])
    {
        $this->collection = $collection;
        $this->options = $options;
        $this->columns = $columns;
        $this->customColumns = $customColumns;
    }

    /**
     * Parsing the collection with options and columns.
     *
     * @return void
     */
    public function run()
    {
        // SEARCH
        if ($this->options->get('search.status') && $search = $this->options->get('search.value')) {
            $columns = $this->columns->getSearchables();

            $this->collection->where(function ($query) use ($columns, $search) {
                foreach ($columns as $index => $column) {
                    if ($index == 0) {
                        $query->where($column, 'LIKE', '%' . $search . '%');
                    } else {
                        $query->orWhere($column, 'LIKE', '%' . $search . '%');
                    }
                }
            });
        }

        // FILTER
        if ($this->options->get('filter.status') && !empty($columns = $this->columns->getFilterables($this->options->get('filter.columns', [])))) {
            foreach ($columns as $name => $value)
                $this->collection->where($name, '=', $value);
        }

        // ORDER
        if ($this->options->get('order.status') && $column = $this->options->get('order.column')) {
            if ($this->columns->{$column}->data && $this->columns->{$column}->orderable) {
                $this->collection->orderBy(
                    $this->columns->{$column}->data,
                    strtolower($this->options->get('order.direction')) == 'desc' ? 'desc' : 'asc'
                );
            }
        }

        // PAGINATE
        if ($this->options->get('paginate.status')) {
            $this->collection = $this->collection->paginate($this->options->get('paginate.length'));
        } else {
            $this->collection = $this->collection->get();
        }

        // CONVERT INTO ELOQUENT MODELS
        $this->convertIntoEloquents();

        // ADDITIONS & EDITS
        $this->injectAdditionsAndEdits();


        return $this->collection;
    }

    /**
     * Convert all object into Eloquent Model [FAKE]
     *
     * @return void
     */
    private function convertIntoEloquents()
    {
        $converted = $this->collection->map(function ($obj) {
            return FakeModel::create((array)$obj);
        });

        if ($this->collection instanceof LengthAwarePaginator)
            $this->collection->setCollection($converted);
        else
            $this->collection = $converted;
    }
}
