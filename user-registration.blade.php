@extends('admin.pim.pim-layout')
@section('content')
<section class="content-header">
    <h1>
        Registration
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('/admin/pim-dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"> Registration</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
		    <div class="panel panel-info">
		        <div class="panel-heading">
		            <strong>User Registration</strong>
		        </div>
		        <form action="{{url('/admin/registration')}}" class="form-horizontal" method="post">
			        <div class="panel-body">
			            <div class="form-group">
			      			<label class="control-label col-sm-2" for="email">Name:</label>
						    <div class="col-sm-10">
						        <input type="text" class="form-control" name="name" id="email" placeholder="Enter Name" required="" >
						     </div>
						</div>
						<div class="form-group">
						    <label class="control-label col-sm-2" for="email">Role:</label>
						    <div class="col-md-8">
								<label class="radio-inline"><input type="radio" value="1" name="role">Admin</label>
								<label class="radio-inline"><input type="radio" value="2" name="role">User</label>
							</div>
						</div>
			          	<div class="form-group">
			      			<label class="control-label col-sm-2" for="email">Email:</label>
						    <div class="col-sm-10">
						        <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required="">
						     </div>
						</div>
						<div class="form-group">
						    <label class="control-label col-sm-2" for="pwd">Password:</label>
						    <div class="col-sm-10">          
						        <input type="password" class="form-control" name="password" id="pwd" placeholder="Enter password" required="">
						    </div>
						</div>
						<div class="form-group">
						    <label class="control-label col-sm-2" for="pwd"></label>
						    <div class="col-sm-10">          
						       <input type="submit" class="btn btn-info" value="Registration"> 
						    </div>
						</div>
			        </div>
		        </form>
		    </div>
	    </div>
    </div>
</section>
@stop