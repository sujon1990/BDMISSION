<?php

/**
 * Class ApplicantsController
 * This Controller is for the all the related function applied on applicants
 */

class ApplicantsController extends \AdminBaseController {

	/**
	 * Constructor for the applicants
	 */

	public function __construct()
	{
		parent::__construct();
		$this->data['recruitmentsOpen'] =   'active open';
		$this->data['pageTitle']     =   'Applicants';
	}

	public function index()
	{
		$this->data['applicants']       =    Applicant::all();
		$this->data['applicantsActive'] =   'active';
	// echo "<pre/>"; print_r($this->data['applicants']); exit();
		return View::make('admin.applicants.index', $this->data);
	}

	public function show($id)
	{
		$this->data['applicant']       =    Applicant::findOrFail($id);
		$this->data['applicantsActive'] =   'active';


		// $doc = [];
		// foreach($this->data['applicant']->getDocuments as $document)
		// {
		// 	$doc[$document->type] =  $document->fileName ;
		// }
		// $this->data['documents']  =   $doc;
			// echo "<pre/>"; print_r(count($this->data['applicant']->getDocuments)); exit();
		return View::make('admin.applicants.show', $this->data);

	}

	public function export(){
		$applicant   =   Applicant::select('applicants.id','applicants.fullName','applicants.fatherName',
							'applicants.mobileNumber','applicants.date_of_birth','applicants.localAddress',
							'applicants.permanentAddress','applicants.status','applicants.permanentAddress'
		                        )->orderBy('id','asc')
		                        ->get()->toArray();

		$data = $applicant;

		Excel::create('applicants'.time(), function($excel) use($data) {

			$excel->sheet('applicants', function($sheet) use($data) {

				$sheet->fromArray($data);

			});

		})->store('xls')->download('xls');


	}
	/**
	 * Remove the specified applicant from storage.
	 */

	public function destroy($id)
	{
		Applicant::find($id)->delete();
		$output['success']  =   'deleted';
		return Response::json($output, 200);
	}

}
