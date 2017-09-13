@extends('layouts.main')

@section('content')
    <div style="background:url('/assets/img/5.jpg')">
                <!-- ngView:  --><section data-ng-view="" class="view-container animate-fade-up"><div class="page-err ng-scope">
    <div class="err-container text-center">
        <div class="err">
            <center><h3>You are not currently a member of any groups.  Please contact the help desk if you believe this is in error.</h3></center>
        </div>

    </div>
</div>
@stop