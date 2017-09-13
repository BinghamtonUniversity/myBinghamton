@extends('layouts.main')

@section('content')
    <div style="background:url('/assets/img/5.jpg')">
                <!-- ngView:  --><section data-ng-view="" class="view-container animate-fade-up"><div class="page-err ng-scope">
    <div class="err-container text-center">
        <div class="err">
					<center><h2>Unable to Authenticate User against Banner / DataProxy Data.</h2><h3>Please contact the help desk and provide them with your username and BNumber so they can attempt to remedy the situation.</h3></center>
					<center><a href="/logout">Click here to log out</a></center>
        </div>
    </div>
</div>
@stop
