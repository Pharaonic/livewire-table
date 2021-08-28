<div>
    {{-- SEARCH --}}
    @if($options->get('search.status'))
        <input wire:model.debounce.800ms='search' placeholder="Search">
        <hr>
    @endif

    {{-- FILTERING --}}
    @if($options->get('filter.status'))
        <form wire:submit.prevent='filter'>

            @foreach ($columns->list as $column)
                @if($column->filterable)
                    <input wire:model.defer='filter.{{ $column->name }}' placeholder="{{ $column->title ?? $column->name }}"><br>
                @endif
            @endforeach

            <button>Filter</button>
        </form>
        <hr>
    @endif

    {{-- TABLE WITH ORDERING AND STYLING --}}
    <table {!! $options->getAttribute('table.class', 'class') . ' ' . $options->getArrayAsString('table.attributes') !!}>
        <thead {!! $options->getAttribute('head.class', 'class') . ' ' . $options->getArrayAsString('head.attributes') !!}>
            <tr {!! $options->getAttribute('head.row.class', 'class') . ' ' . $options->getArrayAsString('head.row.attributes') !!}>

                @foreach ($columns->list as $column)
                    <th {!! $column->getHeadClass() . ' ' . $column->getHeadAttributes() !!} @if ($options->get('order.status') && $column->orderable)wire:click="$emitSelf('orderByToggle', '{{ $column->name }}')"@endif>
                        {!! $column->title ?? $column->name !!}
                        @if ($column->orderable && $column->direction)
                            {!! $options->get('head.order.' . ($column->direction == 'desc' ? 'desc' : 'asc')) !!}
                        @endif
                    </th>
                @endforeach

            </tr>
        </thead>
        <tbody {!! $options->getAttribute('body.class', 'class') . ' ' . $options->getArrayAsString('body.attributes') !!}>
            {{-- ROWS --}}
            @foreach ($records as $record)
                @livewire($options->get('row.component'),
                    [
                        'record'    => &$record,
                        'columns'   => $columns->getRowData($record),
                        'options'   => [
                            'class'         => $options->getAttribute('row.class', 'class'),
                            'attributes'    => $options->getArrayAsString('row.attributes'),
                        ]
                    ],
                    key($record instanceof \Illuminate\Database\Eloquent\Model ? base64_encode(get_class($record) . $record->getKey()) : now())
                )
            @endforeach
        </tbody>
    </table>

    {{-- PAGINATION --}}
    @if ($options->get('paginate.status'))
        {{ $records->links($options->get('paginate.theme')) }}
    @endif
</div>
