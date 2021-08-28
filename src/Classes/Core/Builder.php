<?php

namespace Pharaonic\Livewire\Table\Classes\Core;

use Exception;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Pharaonic\Livewire\Table\Classes\Parsers\CollectionParser;
use Pharaonic\Livewire\Table\Classes\Parsers\EloquentCollectionParser;
use Pharaonic\Livewire\Table\Classes\Parsers\EloquentParser;
use Pharaonic\Livewire\Table\Classes\Parsers\QueryParser;
use Pharaonic\Livewire\Table\Classes\Structure\Columns;

/**
 * Data Query Builder
 *
 * @method Builder of(object $query)
 * @method Builder add(string $name, $value)
 * @method Builder edit(string $name, $value)
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
final class Builder
{
    public $query;
    public $columns = [];

    /**
     * Register the query.
     *
     * @param object $query
     * @return Builder
     */
    public function of(object $query)
    {
        if (!$query instanceof EloquentBuilder && !$query instanceof QueryBuilder && !$query instanceof Collection)
            throw new Exception('Eloquent/Query Builder or a Collection just allowed to use.');

        if ($this->query) return $this;

        $this->query = $query;

        return $this;
    }

    /**
     * Add a new column to the single row.
     *
     * @param string $name
     * @param mixed $value
     * @return Builder
     */
    public function add(string $name, $value)
    {
        if (gettype($value) == 'object' && !is_callable($value))
            throw new Exception('Object columns has not been allowed.');

        $this->columns[] = [
            'type'  => 'add',
            'name'  => $name,
            'value' => $value
        ];

        return $this;
    }

    /**
     * Edit an exist column of the single row.
     *
     * @param string $name
     * @param mixed $value
     * @return Builder
     */
    public function edit(string $name, $value)
    {
        if (gettype($value) == 'object' && !is_callable($value))
            throw new Exception('Object columns has not been allowed.');

        $this->columns[] = [
            'type'  => 'edit',
            'name'  => $name,
            'value' => $value
        ];

        return $this;
    }

    /**
     * Passing options, query to the right Parser
     *
     * @param Options $options
     * @param Columns $columns
     * @return mixed
     */
    public function run(Options $options, Columns $columns)
    {
        $query = clone $this->query;

        if ($query instanceof EloquentCollection) {
            return (new EloquentCollectionParser($query, $options, $columns, $this->columns))->run();
        } elseif ($query instanceof Collection) {
            return (new CollectionParser($query, $options, $columns, $this->columns))->run();
        } elseif ($query instanceof QueryBuilder) {
            return (new QueryParser($query, $options, $columns, $this->columns))->run();
        } elseif ($query instanceof EloquentBuilder) {
            return (new EloquentParser($query, $options, $columns, $this->columns))->run();
        } else {
            throw new Exception('This query is not supported.');
        }
    }
}
