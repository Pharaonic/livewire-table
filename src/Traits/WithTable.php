<?php

namespace Pharaonic\Livewire\Table\Traits;

use Exception;
use Pharaonic\Livewire\Table\Classes\Core\Builder;
use Pharaonic\Livewire\Table\Classes\Core\Options;
use Pharaonic\Livewire\Table\Classes\Structure\Columns;

/**
 * Table Initializer.
 *
 * @property Options $options
 * @property Columns $columns
 * @property Builder $builder
 * @property mixed $records
 * @property boolean $initialized
 *
 * @method void initializeTraits()
 * @method void __destruct()
 * @method void initializeWithTable()
 * @method \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory render()
 * @method Builder builder()
 * @method static getFreshRecords()
 * @method void refresh()
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
trait WithTable
{
    /**
     * Options List
     *
     * @var Options
     */
    protected $options;

    /**
     * Columns List
     *
     * @var Columns
     */
    protected $columns;

    /**
     * Builder Instance
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Records List
     *
     * @var mixed
     */
    protected $records = [];

    /**
     * First Init Flag
     *
     * @var boolean
     */
    public $initialized = false;

    /**
     * Customize the Livewire initializeTraits/
     *
     * @return void
     */
    public function initializeTraits()
    {
        foreach (class_uses_recursive($class = static::class) as $trait) {
            if (method_exists($class, $method = 'initialize' . class_basename($trait))) {
                $this->{$method}();
            }
        }

        if (!$this->initialized) {

            $this->getFreshRecords();
            $this->initialized = true;
        }
    }

    /**
     * Action on Class Die
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->options && $this->options->get('AE.injected', false))
            $this->options->set('AE.injected', false);
    }

    /**
     * Initialize the trait
     *
     * @return void
     */
    public function initializeWithTable()
    {
        // Check `query` method existance.
        if (!method_exists($this, 'query'))
            throw new Exception('The `query` function is required.');

        // Load Columns
        $this->columns = new Columns(method_exists($this, 'columns') ? call_user_func([$this, 'columns']) : []);

        // Load options list.
        $this->options = new Options(method_exists($this, 'options') ? call_user_func([$this, 'options']) : []);
        $this->options->set('paginate.current', $this->page);

        // Set Pre-Ordering
        if ($this->options->get('order.status') && $this->options->get('order.column') && $this->columns->{$this->options->get('order.column')})
            $this->columns->{$this->options->get('order.column')}->setOrderDirection($this->options->get('order.direction', 'asc'));

        // Load query builder with customized columns list.
        $this->builder = call_user_func_array([$this, 'query'], []);

        // Add Refresh Event
        $this->listeners += ['refresh', 'resetAll'];
    }

    /**
     * Create a new instance of Builder.
     *
     * @return Builder
     */
    public function builder()
    {
        return new Builder;
    }

    /**
     * Getting all the fresh records.
     *
     * @param bool $resetPagination
     * @return static
     */
    public function getFreshRecords(bool $resetPagination = false)
    {
        // Reset Pagination
        if ($resetPagination && $this->options->get('paginate.status') && $this->options->get('paginate.current') != 1)
            $this->setPage(1);

        $this->records = $this->builder->run($this->options, $this->columns);

        return $this;
    }
}
