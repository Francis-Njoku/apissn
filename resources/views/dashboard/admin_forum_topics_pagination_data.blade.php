@foreach($data as $row)
<tr>
    <td>{{ $row->title}}</td>
    <td>{{ $row->category}}</td>
    <td>{{ $row->reply_count }}</td>
    <td>{{ $row->status}}</td>
    <td>
    <a href="/admin/forum-edit-topic/{{$row->id}}" class="btn btn-info">Edit</a>
        <!--<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"
        data-id="{{$row->id}}" 
        data-title2="{{$row->title}}" data-description="{{$row->description}}"
        data-status="{{$row->status}}" data-title="Update Title">
           Edit
        </button>-->
    </td>
</tr>
@endforeach
<tr>
    <td colspan="5" align="center">
        {!! $data->links() !!}
    </td>
</tr>