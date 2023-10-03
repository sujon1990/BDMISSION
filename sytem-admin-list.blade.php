@extends('admin.pim.pim-layout')
@section('content')
<section class="content-header">
    <h1>
        Admin List
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('/admin/pim-dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"> Admin List</li>
    </ol>
</section>
<section class="content">

    <div class="row">
        <div class="col-md-12">
		    <section class="col-lg-12 connectedSortable">
			<div class="row">
		        <div class="col-md-10">
		            @if(Session::has('flash_message'))
		            <div class="alert {{Session::get('flash_type')}}">
		                <strong>{{Session::get('flash_message')}}</strong>
		            </div>
		            @endif
		        </div>
		        <div class="col-md-2">
		        </div>
		    </div>
            <!-- Custom tabs (Charts with tabs)-->
            <!-- TO DO List -->
            <div class="box box-primary">
                 <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-8">
                            <a href="{{url('/admin/registration')}}" class="btn btn-success btn-flat">
                                <i class="fa fa-plus fa-fw"></i> Add New
                            </a>
                        </div>
                        <div class="col-md-4">
                            <form action="{{url('admin/admin-list')}}" method="POST">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button type="submit"  id="search-btn" class="btn btn-success btn-flat">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                   
                        <table class="table table-striped table-bordered" id="essentialInfoTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Last Login</th>
                                    <th class="col-md-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($data))
                            @foreach($data as $user)
                            	<tr>
                            		<td>{{$user->id}}</td>
                            		<td>{{$user->name}}</td>
                            		<td>{{$user->email}}</td>
                            		<td>
                            			@if($user->role==1)
                            			<strong>Admin</strong>
                            			@elseif($user->role==2)
                            			<strong>User</strong>
                            			@else
                            			<strong>Unknown</strong>
                            			@endif
                            		</td>
      
                            		<td><?php echo $user->created_at?></td>
                            		<td>{{$user->last_login}}</td>
                            		<td class="text-right">
                            			<a href="{{url('/admin/user/edit',$user->id)}}" class="btn btn-primary btn-flat btn-sm">Edit</a>
                            			<a href="{{url('/admin/user/delete',$user->id)}}" onclick="return confirm('Are sure want to delete?');" class="btn btn-danger btn-flat btn-sm">Delete</a>
                            		</td>
                            	</tr>
                            @endforeach
                            @else 
                            	<tr>
                            		<td colspan="7" class="text-center"><strong>No User Found</strong></td>
                            	</tr>
                            @endif
                            </tbody>
                           
                           
                        </table>
                        <div class="pull-right"></div>
                   
                </div>
            </div>
        </section>
	    </div>
    </div>
</section>
@stop