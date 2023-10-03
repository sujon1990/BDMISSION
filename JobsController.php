<?php

/**
 * Class EmployeesController
 * This Controller is for the all the related function applied on employees
 */

class JobsController extends \AdminBaseController {

	/**
	 * Constructor for the Employees
	 */

	public function __construct()
	{
		parent::__construct();
		$this->data['recruitmentsOpen'] =   'active open';
		$this->data['pageTitle']     =   'Jobs';
	}

	public function index()
	{
		$this->data['jobs'] =	Job::all();
		$this->data['jobsActive'] = 'active';
		return View::make('admin.jobs.index', $this->data);
	}

	/**
	 * Show the form for creating a new employee
	 */
	public function create()
	{
		$this->data['jobsActive'] =   'active';
		$this->data['department_id']      =     Department::lists('deptName','id');

		return View::make('admin.jobs.create',$this->data);
	}

	/**
	 * Store a newly created employee in storage
	 */
	public function store()
	{
		$validator = Validator::make($input = Input::all(), Job::rules('create'));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}
			// echo "<pre/>"; print_r(Input::all()); exit();

		Job::create(Input::all());

		$filename   =   null;
		// Profile Image Upload
		if (Input::hasFile('circular')) {
			$path       = public_path()."/circular/";
			File::makeDirectory($path, $mode = 0777, true, true);

			$image 	    = Input::file('circular');
			$extension  = $image->getClientOriginalExtension();
			$filename	= "{$input['jobID']}.jpg";
			// $filename	= "{$input['jobID']}.".strtolower($extension);

			//                Image::make($image->getRealPath())->resize('872','724')->save($path.$filename);
			Image::make($image->getRealPath())
			     ->fit(872, 724, function ($constraint) {
				     $constraint->upsize();
			     })->save($path.$filename);
		}

		return Redirect::route('admin.jobs.index')->with('success',"<strong>{$input['jobTitle']}</strong> successfully added to the Database");
	}


	/**
	 * Show the form for editing the specified employee
	 */
	public function edit($id)
	{
		$this->data['jobsActive']  =   'active';
		$this->data['department_id']       =   Department::lists('deptName','id');
		$this->data['job']         		=   Job::where('jobID', '=' ,$id)->get()->first();
		$this->data['designation_id']   =   Designation::find($this->data['job']->designation_id);

		// echo "<pre/>"; print_r($this->data); exit();
		return View::make('admin.jobs.edit', $this->data);
	}

	/**
	 * Update the specified job in storage.
	 */
	public function update($id)
	{
		$job   =   Job::where('jobID','=',$id)->get()->first();

		$validator = Validator::make($input = Input::all(), Job::rules('update', $job->id));

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$job->update(Input::all());

		$filename   =   null;
		// Profile Image Upload
		if (Input::hasFile('circular')) {
			$path       = public_path()."/circular/";
			File::makeDirectory($path, $mode = 0777, true, true);

			$image 	    = Input::file('circular');
			$extension  = $image->getClientOriginalExtension();
			$filename	= "{$input['jobID']}.jpg";
			// $filename	= "{$input['jobID']}.".strtolower($extension);

			//                Image::make($image->getRealPath())->resize('872','724')->save($path.$filename);
			Image::make($image->getRealPath())
			     ->fit(872, 724, function ($constraint) {
				     $constraint->upsize();
			     })->save($path.$filename);
		}

		return Redirect::route('admin.jobs.edit',$id)->with('success',"<strong>{$input['jobTitle']}</strong> Updated Successfully");

	}

	public function export(){
		$job   =   Job::join('designation', 'jobs.designation_id', '=', 'designation.id')
		                        ->join('department', 'department.id', '=', 'designation.deptID')
		                        ->select('jobs.id','jobs.jobID',
			                        'jobs.jobTitle','department.deptName as Department',
			                        'designation.designation as Designation','jobs.level','jobs.jobDescription','jobs.jobRequirement',
			                        'jobs.deadLine','jobs.joiningDate','jobs.jobLocation','jobs.ageLimit','jobs.vacancy','jobs.salary',
			                        'jobs.jobType','jobs.status'
		                        )->orderBy('id','asc')
		                        ->get()->toArray();

		$data = $job;

		Excel::create('jobs'.time(), function($excel) use($data) {

			$excel->sheet('Jobs', function($sheet) use($data) {

				$sheet->fromArray($data);

			});

		})->store('xls')->download('xls');
	}

	/**
	 * Remove the specified employee from storage.
	 */

	public function destroy($id)
	{
		Job::where('jobID', '=', $id)->delete();
		$output['success']  =   'deleted';
		return Response::json($output, 200);
	}


	/**
	 * Application functions.
	 */
	public function apply($id)
	{
		$job = Job::find($id);
		$applicant = Auth::applicant()->get()->id;

		if ($job->applicants->contains($applicant)) {
			return Redirect::route('home')->with('error', 'You already applied this job!');
		}

		$job->applicants()->sync([$applicant], false);
		// $job->applicants()->attach();
		return Redirect::route('home')->with('success', 'Your application successfully saved.');

  //       $this->data['data']              =  Input::all();
		// $this->data['updated_by']        =  Auth::applicant()->get()->id;
		// $leave_application = Attendance::findOrFail($id);

			// echo "<pre/>"; print_r($id); exit();
	}

	public function job_applications()
	{
		$this->data['jobs'] =	Job::active()->with('applicants')->get();
			// echo "<pre/>"; print_r($this->data['jobs']); exit();
		$this->data['jobApplicationActive'] = 'active';
		return View::make('admin.jobs.applicants', $this->data);
	}

	public function show_applications($id)
	{
		$this->data['jobs'] =	Job::where('jobID', '=', $id)->with('applicants')->get()->first();
		$this->data['jobApplicationActive'] = 'active';
		return View::make('admin.jobs.whowApplicants', $this->data);
	}

	public function update_marks()
	{
		$job_id  = Input::get('job');
		$applicant_id  = Input::get('applicant');
		$marks  = Input::get('marks');

		Job::find($job_id)->applicants()->updateExistingPivot($applicant_id, ['marks' => $marks]);
		
		$output['success'] = 'success';

		return Response::json($output, 200);

	}

	public function ajax_update_sortlist()
	{
		$job_id  = Input::get('job');
		$applicant_id  = Input::get('applicant');
		$value  = Input::get('value');

		Job::find($job_id)->applicants()->updateExistingPivot($applicant_id, ['sorted' => $value]);

		$output['success'] = 'success';

		return Response::json($output, 200);
	}

	public function ajax_remove_applicant()
	{
		$job_id  = Input::get('job');
		$applicant_id  = Input::get('applicant');

		Job::find($job_id)->applicants()->detach($applicant_id);

		// Job::where('jobID', '=', $job_id)->delete();
		$output['success']  =   'deleted';
		return Response::json($output, 200);
	}


}
