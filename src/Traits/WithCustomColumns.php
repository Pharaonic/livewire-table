<?php

namespace Pharaonic\Livewire\Table\Traits;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Livewire\Table\Classes\Core\Builder;
use ReflectionFunction;

trait WithCustomColumns
{
    /**
     * Customized columns to Invoke on render if Needed
     *
     * @var array
     */
    protected $customColumns = [];

    /**
     * First Init Flag
     *
     * @var boolean
     */
    public $initialized = false;

    /**
     * Initialize the Custom Columns for the second render
     * Ex.Case (Update Row)
     *
     * @return void
     */
    public function initializeWithCustomColumns()
    {
        if (method_exists($this, 'columns') && $this->initialized) {
            $this->customColumns = call_user_func([$this, 'columns']);

            if ($this->customColumns instanceof Builder) {

                $list = [];

                foreach ($this->customColumns->columns as $column)
                    $list[$column['name']] = $column['value'];

                $this->customColumns = $list;
            }
        }

        if (!$this->initialized)
            $this->initialized = true;
    }

    /**
     * Create a new instance of the builder;
     *
     * @return Builder
     */
    protected function builder()
    {
        return new Builder;
    }

    /**
     * Getting the custom column value
     *
     * @param mixed $callable
     * @param mixed $value
     * @param Object $record
     * @return mixed
     */
    protected function getColumnValue($callable, $value = null, Object $record)
    {
        if (is_callable($callable)) {
            $callable = new ReflectionFunction($callable);

            $params = array_map(function (&$param) use ($value, $record) {

                $type = $param->getType();
                if ($type) $type = $type->getName();

                if (!$type) {
                    return $value;
                } elseif ($type == get_class($record) || $type == Model::class || $type == 'object') {
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
     * Getting custom columns value
     *
     * @param string $name
     * @return mixed
     */
    public function getCustomValue(string $name)
    {
        if (!isset($this->customColumns[$name])) return;

        return $this->getColumnValue($this->customColumns[$name], $this->record->{$name} ?? null, $this->record);
    }
}
