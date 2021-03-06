@extends('layouts.app')
@section('page-title')
    Profile
@endsection
@section('profile-css')
    {{asset('css/profile.css')}}
@endsection
@section('content')
    <div class="container bootstrap snippet">
        <div class="row ng-scope">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <div class="pv-lg">
                            <img class="center-block img-responsive img-circle img-thumbnail thumb96"
                                 src="https://api.adorable.io/avatars/285/{{ $currentUser['firstName'] }}{{ $currentUser['lastName'] }}">
                        </div>
                        <h3 class="m0 text-bold">{{ $currentUser['firstName'] }} {{ $currentUser['lastName'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="h4 text-center">Personal Information</div>
                        <div class="row pv-lg">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <form id="delete-form" class="form-horizontal ng-pristine ng-valid" action="/deleteAccount" method="post" role="form">
                                    {{ csrf_field() }}
                                    <input type="hidden" value="{{$currentUser['id']}}" name="current-user-id" />
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="inputContact1">Name</label>
                                        <div class="col-sm-10"> <input class="form-control" id="inputContact1" type="text" placeholder="" value="{{ $currentUser['firstName'] }} {{ $currentUser['lastName'] }}" readonly></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="inputContact2">Email</label>
                                        <div class="col-sm-10"> <input class="form-control" id="inputContact2" type="email" value="{{ $currentUser['email'] }}" readonly></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="inputContact3">Phone</label>
                                        <div class="col-sm-10"> <input class="form-control" id="inputContact3" type="text" value="{{ $currentUser['phoneNumber'] }}" readonly></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="inputContact4">Postal Code</label>
                                        <div class="col-sm-10"> <input class="form-control" id="inputContact" type="text" value="{{ $currentUser['postalCode'] }}" readonly></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="inputContact5">Address</label>
                                        <div class="col-sm-10">
                                            <pre>{{ $currentUser['doorNumber'] }} {{ $currentUser['street'] }}, {{ $currentUser['city'] }}, {{ $currentUser['province'] }}, {{ $currentUser['country'] }}</pre>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10"> 
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#myModal">Delete My Account</button>
                                        </div>
                                    </div>
                                            <!-- modal-->
                                            <!--<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" style="margin:12px 12px 0;">Return</button>-->
                                            <div id="myModal" class="modal fade" role="dialog">
                                              <div class="modal-dialog">
                                                <!-- Modal content-->
                                                <div class="modal-content" style="text-align:left;">
                                                  <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                      <h4 class="modal-title">Delete My Account</h3>
                                                  </div>
                                                  <div class="modal-body" style=>
                                                    <p>Are you sure you want to delete your account? You will lose all your purchases!</p>
                                                  </div>
                                                  <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                        <!--<input type="submit" name="delete-submit" id="delete-submit" class="col-sm-offset-2 col-sm-10" value="Delete My Account">-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection