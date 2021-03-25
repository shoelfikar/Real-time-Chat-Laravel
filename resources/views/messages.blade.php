@foreach ($messages as $item)
    @if ($item->from == Auth::id())
        <div class="chat-message chat-sender">
            <span class="mr-2">{{date('H:i', strtotime($item->created_at))}}</span>
            <span class="message-content">{{$item->message}}</span>
        </div>
    @else
        <div class="chat-message">
            <span class="message-content">{{$item->message}}</span>
            <span class="ml-2">{{date('H:i', strtotime($item->created_at))}}</span>
        </div>
    @endif
@endforeach
