<?php

namespace Pharaonic\Livewire\Table\Classes\Parsers;

use Pharaonic\Livewire\Table\Classes\Core\FakeModel;
use Pharaonic\Livewire\Table\Classes\Core\Options;
use Pharaonic\Livewire\Table\Classes\Core\Parser;
use Pharaonic\Livewire\Table\Classes\Structure\Columns;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Collection Parser
 *
 * @method void run()
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class CollectionParser extends Parser
{
    protected $collection, $options, $columns, $customColumns;

    /**
     * Create a new parser instance.
     *
     * @param Collection $collection
     * @param Options $options
     * @param Columns $columns
     * @param array $customColumns
     */
    public function __construct(Collection $collection, Options $options, Columns $columns, array $customColumns = [])
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
        // PARSE ALL INTO ELOQUENT MODEL OBJECTS
        $this->convertIntoEloquents();

        // ADDITIONS & EDITS
        $this->injectAdditionsAndEdits();

        // SEARCH
        if ($this->options->get('search.status') && $search = $this->options->get('search.value')) {

            $columns = $this->columns->getSearchables();

            $this->collection = $this->collection->filter(function ($record) use ($search, $columns) {
                foreach ($columns as $column)
                    if (preg_match('/' . $search . '/i', strip_tags($record->{$column}), $matches) > 0)
                        return true;

                return false;
            });
        }

        // FILTER
        if ($this->options->get('filter.status') && !empty($columns = $this->columns->getFilterables($this->options->get('filter.columns', [])))) {
            $this->collection = $this->collection->filter(function ($record) use ($columns) {
                foreach ($columns as $column => $value)
                    if ($record->{$column} != $value) return false;

                return true;
            });
        }

        // ORDER
        if ($this->options->get('order.status') && $column = $this->options->get('order.column')) {
            if ($this->columns->{$column}->data && $this->columns->{$column}->orderable) {
                $this->collection = $this->collection->sortBy(
                    $this->columns->{$column}->data,
                    SORT_NATURAL,
                    strtolower($this->options->get('order.direction')) == 'desc'
                );
            }
        }

        // PAGINATE
        if ($this->options->get('paginate.status')) {
            $current = $this->options->get('paginate.current');
            $length = $this->options->get('paginate.length');

            return new LengthAwarePaginator(
                $this->collection->forPage($current, $length),
                $this->collection->count(),
                $length,
                $current
            );
        }


        return $this->collection;
    }

    /**
     * Convert all object into Eloquent Model [FAKE]
     *
     * @return void
     */
    private function convertIntoEloquents()
    {
        $this->collection = $this->collection->map(function ($obj) {
            return FakeModel::create((array)$obj);
        });
    }
}
