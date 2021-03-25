@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="{{asset('css/home.css')}}">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card chat-realtime">
                <div class="card-header">
                  Realtime Chat App
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card" style="width: 18rem; height: 440px;">
                                <div class="card-header">
                                  Users
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($users as $item)
                                        <li class="list-group-item" id="{{$item->id}}">
                                            <img src="https://via.placeholder.com/150" alt="">
                                            <span class="username">{{$item->name}}</span>
                                            @if ($item->unread)
                                                <span class="notif">{{$item->unread}}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-8 user-to-chat d-none">
                            <div class="card" style="height: 400px">
                                <div class="card-header">
                                    A second item
                                </div>
                                <div class="card-body" style="overflow: auto">

                                </div>
                            </div>
                            <div class="w-100">
                                <div class="row mt-1">
                                  <div class="col-md-10">
                                    <input type="text" class="form-control send-message" placeholder="Send Message">
                                  </div>
                                  <div class="col-md-2">
                                    <button class="btn btn-primary btn-send-message">kirim</button>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
