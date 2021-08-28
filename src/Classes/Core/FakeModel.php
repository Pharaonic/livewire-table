<?php

namespace Pharaonic\Livewire\Table\Classes\Core;

use Illuminate\Database\Eloquent\Model;

/**
 * Fake Model Object.
 *
 * @package pharaonic/livewire-table
 * @version 1.0.0
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class FakeModel extends Model
{
    /**
     * Model Faker
     *
     * @param array $attributes
     * @return FakeModel
     */
    public static function create(array $attributes)
    {
        return (new static)->forceFill($attributes);
    }
}
