@extends('front.layouts.homelayout')

@section('head')

{{HTML::style("assets/global/css/components.css")}}
{{HTML::style("assets/global/css/plugins.css")}}
{{HTML::style("assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css")}}
{{HTML::style("assets/global/css/joblist.css")}}

@stop

@section('mainarea')
<div class="col-md-10 col-md-offset-1">
            <div class="row margin-bottom-20">
                <!--Profile Post-->
                <div class="col-sm-12">

		            <div class="panel panel-grey">
		                <div class="panel-heading">
		                    <h3 class="panel-title"><i class="fa fa-tasks"></i> Available Jobs</h3>
		                </div>
		                <div class="panel-body">

                            @foreach($jobs as $job)
                                <div class="media search-media">
                                    <div class="media-body">
                                        <div class="media-detail">
                                            <h4 class="media-heading">
                                                <a href="#" class="blue">{{ $job->jobTitle.' ('. $job->jobType .')' }}</a>
                                            </h4>
                                            <p>{{ implode(' ', array_slice(explode(' ', $job->jobDescription), 0, 40)) }}...
                                            <a href="{{ URL::to('jod_detail/'.$job->jobID)}}" class="btn btn-link btn-xs"> View details </a>
                                            </p>
                                        </div>

                                        <div class="search-actions text-center">
                                            Salary: <span class="blue bolder bigger-150">{{ $job->salary }}</span> tk <br/>
                                            Location: <span class="blue bolder bigger-150">{{ $job->jobLocation }}</span> <br/>
                                            Vacancy: <span class="blue bolder bigger-150">{{ $job->vacancy }}</span> <br/>
                                            Dead Line: <span class="blue bolder bigger-150">{{ date('jS M y', strtotime($job->deadLine) )}}</span> <br/>
                                            <a href="{{ URL::to('jod_detail/'.$job->jobID)}}" class="search-btn-action btn btn-sm btn-block btn-info">Apply this job</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

		                </div>

            	</div>

            <hr>

        </div>
        <!--End Profile Body-->
    </div>

</div>

@stop