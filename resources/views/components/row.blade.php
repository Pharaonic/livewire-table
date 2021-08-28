<tr {!! $options['class'] . ' ' . $options['attributes'] !!}>
    @foreach ($columns as $column)
        <td {!! $column['class'] . ' ' . $column['attributes'] !!}>
            {!! $column['data'] !!}
        </td>
    @endforeach
</tr>
