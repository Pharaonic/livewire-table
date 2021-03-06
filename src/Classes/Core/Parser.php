<?php

namespace Pharaonic\Livewire\Table\Classes\Core;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use ReflectionFunction;

/**
 * Main Parser
 *
 * @method mixed getColumnValue($callable, $value, Object $record)
 * @method mixed injectAdditionsAndEdits($inject = false)
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
abstract class Parser
{
    abstract public function run();

    /**
     * Getting the custom column value
     *
     * @param mixed $callable
     * @param mixed $value
     * @param Object $record
     * @return mixed
     */
    protected function getColumnValue($callable, $value, Object $record)
    {
        if (is_callable($callable)) {
            $callable = new ReflectionFunction($callable);

            $params = array_map(function ($param) use ($record, $value) {

                $type = $param->getType();
                if ($type) $type = $type->getName();

                if (!$type) {
                    return $value;
                } elseif (is_subclass_of($type, Model::class) || $type == Model::class || $type == 'object') {
                    return $record;
                } else {
                    return null;
                }
            }, $callable->getParameters());

            return $callable->invokeArgs($params);
        } else {
            return $callable;
        }
    }

    /**
     * Inject all the addtions and edits to every record.
     *
     * @return void
     */
    protected function injectAdditionsAndEdits($inject = false)
    {
        $injectedCollection = $this->collection->map(function ($record) {
            $attrs = array_keys($record->getAttributes());

            foreach ($this->customColumns as $column) {
                if ($record->{$column['name']} instanceof Carbon) {
                    $value = $this->getColumnValue($column['value'], $record->{$column['name']}, $record);
                    $record->timestamps = false;
                    $record->{$column['name']} = $value;
                } else {
                    $record->{$column['name']} = $this->getColumnValue($column['value'], $record->{$column['name']}, $record);
                }
            }

            return $record;
        });

        if ($this->collection instanceof LengthAwarePaginator)
            $this->collection->setCollection($injectedCollection);
        else
            $this->collection = $injectedCollection;

        if ($inject)
            $this->options->set('AE.injected', true);
    }
}
