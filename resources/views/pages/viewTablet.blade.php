@extends('layouts.app')
@section('content')


<div class="row row-offcanvas row-offcanvas-right">
    <div class="col-xs-12 col-sm-9">
        <p class="pull-right visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        @if(empty($id))
        <div class="row">
            <div class="col-lg-12">
               <h1> <small>Here are some weekly hot sellers!</small></h1>
            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Samsung Tablet</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <i class="fa fa-tablet fa-5x"></i>
                            </div>
                            <div class="col-md-6">
                                <p >Price: $199.99</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <p>Item Info</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <span><a class="btn btn-default" href="/view/tablet/1" role="button">View details »</a></span>
                        <span><a class="btn btn-default" href="#" role="button">Add to Cart »</a></span>
                    </div>
                </div>
            </div><!--/.col-xs-6.col-lg-4-->
            <div class="col-xs-6 col-lg-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Samsung Tablet</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <i class="fa fa-tablet fa-5x"></i>
                            </div>
                            <div class="col-md-6">
                                <p >Price: $199.99</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <p>Item Info</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer"
                        <span><a class="btn btn-default" href="/view/tablet/1" role="button">View details »</a></span>
                        <span><a class="btn btn-default" href="#" role="button">Add to Cart »</a></span>
                    </div>
                </div>
            </div><!--/.col-xs-6.col-lg-4-->
            <div class="col-xs-6 col-lg-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Samsung Tablet</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <i class="fa fa-tablet fa-5x"></i>
                            </div>
                            <div class="col-md-6">
                                <p >Price: $199.99</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <p>Item Info</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <span><a class="btn btn-default" href="/view/tablet/1" role="button">View details »</a></span>
                        <span><a class="btn btn-default" href="#" role="button">Add to Cart »</a></span>
                    </div>
                </div>
            </div><!--/.col-xs-6.col-lg-4-->
        </div><!--/row-->
        @else
            <div class="col-xs-12 col-lg-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Samsung Tablet</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <i class="fa fa-tablet fa-5x"></i>
                            </div>
                            <div class="col-md-8">
                                <p>Price: $199.99</p>
                                <p>Brand: Samsung</p>
                                <p>quantity: 2</p>
                                <p>Brand: Samsung</p>
                                <p>Camera: Yes</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <span><a class="btn btn-default" href="#" role="button">Add to Cart »</a></span>
                    </div>
                </div>
            </div><!--/.col-xs-6.col-lg-4-->
        @endif
    </div><!--/.col-xs-12.col-sm-9-->

    <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
        <div class="list-group">
            <a href="/view/monitor" class="list-group-item">Monitor</a>
            <a href="/view/desktop" class="list-group-item">Desktop</a>
            <a href="/view/laptop" class="list-group-item">Laptop</a>
            <a href="/view/tablet" class="list-group-item active">Tablet</a>
        </div>
        <!-- advanced search -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Advanced Search</h3>
            </div>
            <div class="panel-body">
                <form id="tablet-form" class="form-horizontal" action="" method="POST">
                    <div class="col-md-12">
                        <div class="form-group">
                            Brand:
                            <select required="" name="tablet-brand" id="tablet-brand" class="form-control">
                                <option title="Select brands" value="">Select brands</option>
                            </select>
                        </div>
                        <div class="form-group">
                            Hard Drive Size (GB):
                            <select required="" name="tablet-storage-capacity" id="tablet-storage-capacity" class="form-control">
                                <option title="Select storage qty" value="">Select storage size</option>
                            </select>
                        </div>
                        <div class="form-group">
                            Price: <br>
                            min:<input type="number" min="1" step="0.01" placeholder="0.00" max="99999" name="tablet-price" id="laptop-price" class="form-control">
                            max:<input type="number" min="1" step="0.01" placeholder="0.00" max="99999" name="tablet-price" id="laptop-price" class="form-control" >
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-sm" name="search-tablet-form" id="search-tablet-form">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!--/.sidebar-offcanvas-->
</div>

    @endsection