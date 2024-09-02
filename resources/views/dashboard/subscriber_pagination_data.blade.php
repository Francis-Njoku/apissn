@foreach($data as $row)
<tr>
    <td>{{ $row->email}}</td>
    <td>{{ $row->order_id}}</td>
    <td>{{ number_format($row->amount,2) }}</td>
    <td>{{ $row->plan_name }}</td>
    <td>{{ $row->plan_type }}</td>
    <td>{{ $row->status }}</td>
    <td>{{ date('D d M Y',strtotime($row->due_date))}}</td>
    <td>{{ date('D d M Y',strtotime($row->created_at))}}</td>
</tr>
@endforeach
<tr>
    <td colspan="8" align="center">
        {!! $data->links() !!}
    </td>
</tr>