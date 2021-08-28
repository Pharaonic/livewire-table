<tr {!! $options['class'] . ' ' . $options['attributes'] !!}>
    @foreach ($columns as $name => $column)
        <td {!! $column['class'] . ' ' . $column['attributes'] !!}>
            {!! $this->getCustomValue($name) ?? $record->{$name} ?? ($column['view'] ? view($column['view'], ['record' => $record]) : null) !!}
        </td>
    @endforeach
</tr>
