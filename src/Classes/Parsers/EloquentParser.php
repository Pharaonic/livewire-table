<?php

namespace Pharaonic\Livewire\Table\Classes\Parsers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;
use Pharaonic\Livewire\Table\Classes\Core\Options;
use Pharaonic\Livewire\Table\Classes\Core\Parser;
use Pharaonic\Livewire\Table\Classes\Structure\Columns;

/**
 * Eloquent Parser
 *
 * @method void run()
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class EloquentParser extends Parser
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
        // CREATE QUERY WITH RELATIONSHIPS
        $query = $this->collection->getQuery();
        $this->injectRelationshipsQuery($query);

        // SEARCH
        if ($this->options->get('search.status') && $search = $this->options->get('search.value')) {
            $this->collection->where(function ($query) use ($search) {
                foreach ($this->columns->getSearchables() as $index => $column) {
                    if (strpos($column, '.') !== false) {
                        // RELATIONSHIP
                        $column = explode('.', $column);
                        $relationship = $column[0];
                        $column = array_pop($column);

                        if ($this->collection->getRelation($relationship)) {
                            $relationTable = $this->collection->getRelation($relationship)->getQuery()->getModel()->getTable();
                            $column = $relationTable . '.' . $column;
                        }
                    }

                    $query->{$index == 0 ? 'where' : 'orWhere'}($column, 'LIKE', '%' . $search . '%');
                }
            });
        }

        // FILTER
        if ($this->options->get('filter.status') && !empty($columns = $this->columns->getFilterables($this->options->get('filter.columns', [])))) {
            $this->collection->where(function ($query) use ($columns) {
                foreach ($columns as $column => $value) {
                    if (strpos($column, '.') !== false) {
                        // RELATIONSHIP
                        $column = explode('.', $column);
                        $relationship = $column[0];
                        $column = array_pop($column);

                        if ($this->collection->getRelation($relationship)) {
                            $relationTable = $this->collection->getRelation($relationship)->getQuery()->getModel()->getTable();
                            $column = $relationTable . '.' . $column;
                        }
                    }

                    $query->where($column, '=', $value);
                }
            });
        }


        // ORDER
        if ($this->options->get('order.status') && $column = $this->options->get('order.column')) {
            if ($this->columns->{$column}->data && $this->columns->{$column}->orderable) {
                $direction =  strtolower($this->options->get('order.direction')) == 'desc' ? 'desc' : 'asc';
                $column = $this->columns->{$column}->data;

                if (strpos($column, '.') !== false) {
                    // // RELATIONSHIP
                    // $column = explode('.', $column);
                    // $relationship = $column[0];
                    // $column = array_pop($column);

                    // if ($this->collection->getRelation($relationship)) {

                        // TASK : SORT BY REALTIONSHIP
                        // $relationTable = $this->collection->getRelation($relationship)->getQuery()->getModel()->getTable();
                        // $query->columns[] = DB::raw("{$relationTable}.{$column} as Raggi");
                        // $query->orderBy("Raggi", $direction);
                    // }
                } else {
                    // DIRECT
                    $query->orderBy($column, $direction);
                }
            }
        }


        // SET NEW QUERY
        $this->collection->setQuery($query);

        // PAGINATE
        if ($this->options->get('paginate.status')) {
            $this->collection = $this->collection->paginate($this->options->get('paginate.length'));
            // dd($this->collection->pluck('id')->toArray());
        } else {
            $this->collection = $this->collection->get();
        }

        // ADDITIONS & EDITS
        $this->injectAdditionsAndEdits();

        return $this->collection;
    }

    /**
     * Inject Relationships To The Query
     *
     * @param \Illuminate\Database\Query\Builder $collectionQuery
     * @return void
     */
    private function injectRelationshipsQuery(&$collectionQuery)
    {
        if (!$collectionQuery->columns || !in_array($collectionQuery->from . '.*', $collectionQuery->columns))
            $collectionQuery->select($collectionQuery->from . '.*');

        $collectionQuery->distinct();

        $relations = array_keys($this->collection->getEagerLoads());
        $this->relationshipsInjector($relations, $this->collection, $collectionQuery);
    }

    /**
     * Direct Inject Relationships
     *
     * @param array $relations
     * @param mixed $collection
     * @param mixed $collectionQuery
     * @return void
     */
    public function relationshipsInjector(array $relations, $collection, &$collectionQuery)
    {
        while (!empty($relations)) {
            $relation = array_shift($relations);
            $obj = $collection->getRelation($relation);

            if (!empty($subRelations = array_keys($obj->getEagerLoads()))) {

                $objQuery = $obj->getQuery()->getQuery();

                $this->relationshipsInjector($subRelations, $obj, $objQuery);
                $obj->setQuery($objQuery);

                $subRelationsFullNames = array_map(function ($n) use ($relation) {
                    return $relation . '.' . $n;
                }, $subRelations);

                $relations = array_filter($relations, function ($current) use ($subRelationsFullNames) {
                    return !in_array($current, $subRelationsFullNames);
                });
            }

            if ($obj instanceof BelongsToMany) {
                $pivot      = $obj->getTable();
                $pivotPK    = $obj->getExistenceCompareKey();
                $pivotFK    = $obj->getQualifiedParentKeyName();
                $this->injectJoin($collectionQuery, $pivot, $pivotPK, $pivotFK);

                $related    = $obj->getRelated();
                $table      = $related->getTable();
                $tablePK    = $related->getForeignKey();
                $first      = $pivot . '.' . $tablePK;
                $second     = $related->getQualifiedKeyName();
                $this->injectJoin($collectionQuery, $table, $first, $second);
            } elseif ($obj instanceof HasOneThrough) {
                $pivot      = explode('.', $obj->getQualifiedParentKeyName())[0]; // extract pivot table from key
                $pivotPK    = $pivot . '.' . $obj->getLocalKeyName();
                $pivotFK    = $obj->getQualifiedLocalKeyName();
                $this->injectJoin($collectionQuery, $pivot, $pivotPK, $pivotFK);

                $related    = $obj->getRelated();
                $table      = $related->getTable();
                $tablePK    = $related->getForeignKey();
                $first      = $pivot . '.' . $tablePK;
                $second     = $related->getQualifiedKeyName();
            } elseif ($obj instanceof HasOneOrMany) {
                $table      = $obj->getRelated()->getTable();
                $first      = $obj->getQualifiedForeignKeyName();
                $second     = $obj->getQualifiedParentKeyName();
            } elseif ($obj instanceof BelongsTo) {
                $table      = $obj->getRelated()->getTable();
                $first      = $obj->getQualifiedForeignKeyName();
                $second     = $obj->getQualifiedOwnerKeyName();
            } else {
                throw new Exception('Relation ' . get_class($obj) . ' is not yet supported.');
            }

            $this->injectJoin($collectionQuery, $table, $first, $second);
        }
    }

    /**
     * Inject Join To Query
     *
     * @param string $table
     * @param string  $first
     * @param string $second
     * @return void
     */
    private function injectJoin(&$query, $table, $first, $second)
    {
        $joins = [];

        foreach ((array) $query->joins as $join)
            $joins[] = $join->table;

        if (!in_array($table, $joins))
            $query->join($table, $first, '=', $second, 'left');
    }
}
