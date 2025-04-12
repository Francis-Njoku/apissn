@foreach($data as $row)
<tr>
    <td>{{ $row->category}}</td>
    <td>{{ $row->topic_count}}</td>
    <td>{{ $row->reply_count }}</td>
    <td>{{ $row->track_count}}</td>
    <td>
        <!--<button class="update-forum-modal btn-sm btn btn-success" data-id="{{$row->id}}" 
            data-category="{{$row->category}}" data-description="{{$row->description}}"
            data-status="{{$row->status}}">
           Edit
        </button>-->
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"
        data-id="{{$row->id}}" 
        data-category="{{$row->category}}" data-description="{{$row->description}}"
        data-status="{{$row->status}}" data-title="Update Category">
           Edit
        </button>
    </td>
</tr>
@endforeach
<tr>
    <td colspan="5" align="center">
        {!! $data->links() !!}
    </td>
</tr>