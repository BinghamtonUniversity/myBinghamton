@extends('layouts.main')

@section('content')
    <div style="background:url('/assets/img/5.jpg')">
                <!-- ngView:  --><section data-ng-view="" class="view-container animate-fade-up"><div class="page-err ng-scope">
    <div class="err-container text-center">
        <div class="err">
            <h1>401</h1>
            <h2>Sorry, you are not authorized to view this page</h2>
        </div>

        <div class="err-body">
            <a href="/" class="btn btn-lg btn-goback">
                <span class="ti-home"></span>
                <span class="space"></span>
                Go Back to Home Page
            </a>
        </div>
    </div>
</div>
@stop