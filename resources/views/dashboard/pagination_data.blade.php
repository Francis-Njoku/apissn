@foreach($data as $row)
<tr>
    <td>{{ $row->id}}</td>
    <td>{{ $row->email }}</td>
    <td>{{ $row->first_name }}</td>
    <td>{{ $row->last_name }}</td>
    <td>{{ date('D d M Y',strtotime($row->created_at))}}</td>
</tr>
@endforeach
<tr>
    <td colspan="5" align="center">
        {!! $data->links() !!}
    </td>
</tr>