@extends('admin.layouts.master')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Create Brand</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/all-category') }}">Brands</a>
                </li>
                <li class="active">
                    <strong>Create</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
            <div class="ibox-tools">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><i
                            class="fa fa-plus"></i> <strong>Create</strong></a>
            </div>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Create brand</h5>
                    </div>
                    <div class="ibox-content">

                        <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data"
                              class="form-horizontal">
                            @csrf()

                            @include('admin.brand.element')

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-warning t m-t-n-xs"><strong>Cancel</strong></a>
                                    <button class="btn btn-sm btn-primary m-t-n-xs" type="submit">
                                        <strong>Submit</strong></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()