<?php

//use App\Classes\ServicesLength;


class PimController extends \AdminBaseController {

    public function __construct() {
        parent::__construct();
        if (!Auth::admin()->check()) {
            header('Location:' . url('/admin'));
            exit();
        }
    }

    public function saveEmpid() {
        if (Request::ajax()) {
            Session::set('emp_id', Input::get('emp_id'));
            return json_encode(array('status' => 'OK', 'empid' => Input::get('emp_id')));
        }
    }

    public function dashboard() {
        $title = "PIM Dashboard";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $all_emp = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%') 
           
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(8);
        } else {
            $all_emp = BasicinfoModel::paginate(8);
        }
        $total_emp = BasicinfoModel::count();
        $total_male_emp = BasicinfoModel::where('PBI_SEX', 'Male')->count();
        $total_female_emp = BasicinfoModel::where('PBI_SEX', 'Female')->count();
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $new_employee = BasicinfoModel::whereBetween('PBI_DOJ', array($start_date, $end_date))->count();
        $total_zone = Zone::count();
        $total_dept = DepartmentType::count();
        $total_branch = Branch::count();
        $total_project = Project::count();
        return View::make('admin.pim.pim-dashboard')->with([
                    'title' => $title,
                    'all_emp' => $all_emp,
                    'total_emp' => $total_emp,
                    'total_male_emp' => $total_male_emp,
                    'total_female_emp' => $total_female_emp,
                    'total_zone' => $total_zone,
                    'total_department' => $total_dept,
                    'total_branch' => $total_branch,
                    'total_project' => $total_project,
                    'new_employee' => $new_employee
        ]);
    }

    public function updateLength() {
        $basic_data = BasicinfoModel::where('PBI_JOB_STATUS', 'In Service')->get();
        foreach ($basic_data as $value) {
            $services = new App\Classes\ServicesLength();
            $update = BasicinfoModel::find($value->PBI_ID);
            $servicesLength = $services->getLength($value->PBI_DOJ);
            $update->PBI_SERVICE_LENGTH_YEAR = $servicesLength['years'];
            $update->PBI_SERVICE_LENGTH = $servicesLength['services_l'];
            $serv_pp = $services->getLengthPP($value->PBI_DOJ_PP);
            $update->PBI_SERVICE_LENGTH_PP = $serv_pp['services_l'];
            $update->PBI_SERVICE_LENGTH_PP_YEAR = $serv_pp['years'];
            $age_length = $services->getAge($value->PBI_DOB);
            $update->PBI_PRESENT_AGE = $age_length['services_l'];
            $update->PBI_PRESENT_AGE_YEAR = $age_length['years'];
            $update->save()or die('Opps ! Something went wrong.');
        }
    }

    public function searchGrade() {
        if (Request::ajax()) {
            $status = "KO";
            $designation = Designationtype::where('DESG_DESC', Input::get('designation'))->first();
            if ($designation && !empty($designation)) {
                $status = "OK";
            }
            return json_encode(array('status' => $status, 'grade' => $designation->DESG_GRADE));
        }
    }

    public function getBasicinfo() {
        $title = "Basic Information";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        return View::make('admin.pim.show-basic-info')->with(['data' => $data, 'title' => $title]);
    }

    public function addBasicInfo() {
        $title = "Basic Information";
        if (Request::method() == 'POST') {
            $rules = [
                'PBI_ID' => 'required|unique:personnel_basic_info'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/add-basicinfo')->withErrors($validator);
            } else {
                $saveData = new BasicinfoModel;
                $saveData->PBI_ID = Input::get('PBI_ID');
                //$saveData->PBI_NAME = "Sujon";
                $saveData->PBI_NAME = Input::get('pbi_name');
                $saveData->PBI_FATHER_NAME = 2;
                $saveData->PBI_MOTHER_NAME = Input::get('pbi_mother_name');
                $saveData->PBI_DOMAIN = Input::get('PBI_DOMAIN');
                $saveData->PBI_DEPARTMENT = Input::get('PBI_DEPARTMENT');
                $saveData->PBI_PROJECT = Input::get('pbi_project');
                $saveData->PBI_DESIGNATION = "na";
                $saveData->PBI_DESG_GRADE = 10;
                $saveData->PBI_AREA = Input::get('pbi_area');
                $saveData->PBI_ZONE = Input::get('pbi_zone');
                $saveData->PBI_BRANCH = Input::get('pbi_branch');
                $saveData->PBI_REGION = Input::get('PBI_REGION');
                $saveData->PBI_APPOINTMENT_LETTER_NO = Input::get('webaddress');
               // $saveData->PBI_DOJ = Input::get('pbi_doj');
                $saveData->PBI_DOJ = Input::get('pbi_joining_date');
                $saveData->PBI_DOB = Input::get('pbi_date_of_birth');
                $saveData->PBI_POB = Input::get('pbi_place_birth');
                $saveData->PBI_DOC = Input::get('pbi_confirm_date');
                $saveData->PBI_DOJ_PP = Input::get('pbi_join_date');
                $saveData->PBI_EDU_QUALIFICATION = Input::get('pbi_edu_qualification');
                $saveData->PBI_SEX = Input::get('PBI_SEX');
                $saveData->PBI_MARITAL_STA = Input::get('PBI_MARITAL_STA');
                $saveData->PBI_RELIGION = Input::get('PBI_RELIGION');
                $saveData->PBI_NATIONALITY = Input::get('PBI_NATIONALITY');
                $saveData->PBI_PERMANENT_ADD = Input::get('permanentaddress');
                $saveData->PBI_PRESENT_ADD = Input::get('pbi_present_add');
                $saveData->PBI_PHONE = Input::get('pbi_phone');
                $saveData->PBI_MOBILE = Input::get('pbi_mobile');
                $saveData->PBI_SPECIALTY = Input::get('PBI_SPECIALTY');
                $saveData->PBI_EMAIL = Input::get('pbi_email');
                $saveData->PBI_PRIMARY_JOB_STATUS = Input::get('PBI_PRIMARY_JOB_STATUS');
                $saveData->PBI_JOB_STATUS = Input::get('PBI_JOB_STATUS');
                $saveData->ESSENTIAL_BLOOD_GROUP = Input::get('ESSENTIAL_BLOOD_GROUP');
                $saveData->personal_file_status = Input::get('personal_file_status');
                $saveData->JOB_LOCATION = Input::get('pbi_job_location');
                $saveData->ESSENTIAL_VOTER_ID = Input::get('pbi_national_id');
                $saveData->pbi_staffid = Input::get('pbi_staffid');
                $saveData->functional_designation = 2;

                $saveData->PBI_intelligence = Input::get('PBI_intelligence');
                $saveData->pbi_travel = Input::get('pbi_travel');
                $saveData->pbi_predisact = Input::get('pbi_predisact');
                $saveData->pbi_dualcitizen = Input::get('pbi_dualcitizen');
                $saveData->pbi_civillaw = Input::get('pbi_civillaw');
                 $saveData->pbi_postdisact = Input::get('pbi_postdisact');
                $saveData->pbi_feedback = Input::get('pbi_feedback');
                $saveData->pbi_remarks = Input::get('pbi_remarks');
                $saveData->PBI_HUSBAND_NAME = Input::get('PBI_HUSBAND_NAME');
                $target_dir = "assets/img/";
            
     if (Input::hasFile('file')) {
                    $desination = "assets/img/";
                    $file = Input::file('file');
                    $extention = $file->getClientOriginalExtension();
                    $emp_name = Input::get('pbi_name');
                    $name = strtolower(str_replace(' ', '_', $emp_name)) . '.' . $extention;

                    if (($file->move($desination, $name))) {
                        $saveData->image = $name;
                    } else {
                        Session::flash('flash_message', 'Oh know ! Some thing went wrong.');
                        Session::flash('flash_type', 'alert-danger');
                        return Redirect::to('admin/pim/basicinfo');
                    }
                }

                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added basic info.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/pim/basicinfo');
                }
            }
        } else {
            $domainType = Domain::all();
            //$ActionsubjectType = ActionSubject::all();
            $departmentType = DepartmentType::all();
            $projectType = Project::all();
            $zoneType = Zone::all();
            $areaType = Area::all();
            $designationType = Designationtype::all();
            $branchType = Branch::all();
            $regionType = Region::all();
            $areaExpertise = AreaExpertise::all();
            $institute = Institute::all();
            $presentFile = PresentFileStatus::all();
            $jobStatus = JobStatus::all();
            $employeeType = EmployeeType::all();
            $funcDesignation = FunctionalDesignation::all();
            return View::make('admin.pim.basicinfo-add')
                            ->with('domainType', $domainType)
                            ->with('departmentType', $departmentType)
                            ->with('projectType', $projectType)
                            ->with('zoneType', $zoneType)
                            ->with('areaType', $areaType)
                            ->with('designationType', $designationType)
                            ->with('branchType', $branchType)
                            ->with('regionType', $regionType)
                            ->with('areaExpertise', $areaExpertise)
                            ->with('institute', $institute)
                            ->with('presentFile', $presentFile)
                            ->with('jobStatus', $jobStatus)
                            ->with('employeeType', $employeeType)
                            ->with('funcDesignation', $funcDesignation)
                            ->with('title', $title);
        }
    }

    public function editBasicinfo($id = NULL) {
        $title = "Basic Information";

        $data = BasicinfoModel::where('PBI_ID', $id)->first();
        $domainType = Domain::all();
        $departmentType = DepartmentType::all();
        $projectType = Project::all();
        $zoneType = Zone::all();
        $areaType = Area::all();
        $designationType = Designationtype::all();
        $branchType = Branch::all();
        $regionType = Region::all();
        $areaExpertise = AreaExpertise::all();
        $institute = Institute::all();
        $presentFile = PresentFileStatus::all();
        $jobStatus = JobStatus::all();
        $employeeType = EmployeeType::all();
        $funcDesignation = FunctionalDesignation::all();

        return View::make('admin.pim.basicinfo-edit')
                        ->with('data', $data)
                        ->with('domainType', $domainType)
                        ->with('departmentType', $departmentType)
                        ->with('projectType', $projectType)
                        ->with('zoneType', $zoneType)
                        ->with('areaType', $areaType)
                        ->with('designationType', $designationType)
                        ->with('branchType', $branchType)
                        ->with('regionType', $regionType)
                        ->with('areaExpertise', $areaExpertise)
                        ->with('institute', $institute)
                        ->with('presentFile', $presentFile)
                        ->with('jobStatus', $jobStatus)
                        ->with('employeeType', $employeeType)
                        ->with('funcDesignation', $funcDesignation);
    }

    public function saveEditBasicinfo() {
        if (Request::method() == 'POST') {
            $rules = [
//                'PBI_ID' => 'required|unique:personnel_basic_info'
            ];
            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to('admin/pim/add-basicinfo')->withErrors($validator);
            } else {
                $saveData = BasicinfoModel::find(Input::get('PBI_ID'));
                $saveData->PBI_NAME = Input::get('PBI_NAME');
                $saveData->PBI_FATHER_NAME = Input::get('pbi_father_name');
                $saveData->PBI_MOTHER_NAME = Input::get('pbi_mother_name');
                $saveData->PBI_DOMAIN = Input::get('PBI_DOMAIN');
                $saveData->PBI_DEPARTMENT = Input::get('PBI_DEPARTMENT');
                $saveData->PBI_PROJECT = Input::get('pbi_project');
                $saveData->PBI_DESIGNATION = Input::get('pbi_designation');
                $saveData->pbi_staffid = Input::get('pbi_staffid');
                $saveData->PBI_AREA = Input::get('pbi_area');
                $saveData->PBI_ZONE = Input::get('pbi_zone');
                $saveData->PBI_BRANCH = Input::get('pbi_branch');
                $saveData->PBI_REGION = Input::get('PBI_REGION');
                $saveData->PBI_APPOINTMENT_LETTER_NO = Input::get('webaddress');
                $saveData->PBI_DOJ = Input::get('pbi_joining_date');

                $saveData->PBI_DOJ_PP = Input::get('pbi_joining_date');

                $saveData->PBI_DOB = Input::get('pbi_date_of_birth');
                $saveData->PBI_POB = Input::get('pbi_place_birth');
                $saveData->PBI_DOC = Input::get('pbi_confirm_date');
                $saveData->PBI_DOJ_PP = Input::get('pbi_join_date');
                $saveData->PBI_EDU_QUALIFICATION = Input::get('pbi_edu_qualification');
                $saveData->PBI_SEX = Input::get('PBI_SEX');
                $saveData->PBI_MARITAL_STA = Input::get('PBI_MARITAL_STA');
                $saveData->PBI_RELIGION = Input::get('PBI_RELIGION');
                $saveData->PBI_NATIONALITY = Input::get('PBI_NATIONALITY');
                $saveData->PBI_PERMANENT_ADD = Input::get('permanentaddress');
                $saveData->PBI_PRESENT_ADD = Input::get('pbi_present_add');
                $saveData->PBI_PHONE = Input::get('pbi_phone');
                $saveData->PBI_MOBILE = Input::get('pbi_mobile');
                $saveData->PBI_SPECIALTY = Input::get('PBI_SPECIALTY');
                $saveData->PBI_EMAIL = Input::get('pbi_email');
                $saveData->PBI_PRIMARY_JOB_STATUS = Input::get('PBI_PRIMARY_JOB_STATUS');
                 $saveData->PBI_JOB_STATUS = Input::get('PBI_JOB_STATUS');
                $saveData->ESSENTIAL_BLOOD_GROUP = Input::get('ESSENTIAL_BLOOD_GROUP');
                $saveData->personal_file_status = Input::get('personal_file_status');
                $saveData->JOB_LOCATION = Input::get('pbi_job_location');
                $saveData->ESSENTIAL_VOTER_ID = Input::get('pbi_national_id');
                $saveData->PBI_intelligence = Input::get('PBI_intelligence');
                $saveData->pbi_travel = Input::get('pbi_travel');
                $saveData->pbi_predisact = Input::get('pbi_predisact');
                $saveData->pbi_dualcitizen = Input::get('pbi_dualcitizen');
                $saveData->pbi_civillaw = Input::get('pbi_civillaw');
                 $saveData->pbi_postdisact = Input::get('pbi_postdisact');
                $saveData->pbi_feedback = Input::get('pbi_feedback');
                $saveData->pbi_remarks = Input::get('pbi_remarks');
                $saveData->PBI_HUSBAND_NAME = Input::get('PBI_HUSBAND_NAME');
              //  print_r(expression)
                if (Input::hasFile('file')) {
                    $desination = "assets/img/";
                    $file = Input::file('file');
                    $extention = $file->getClientOriginalExtension();
                    $emp_name = Input::get('pbi_name');
                    $name = strtolower(str_replace(' ', '_', $emp_name)) . '.' . $extention;

                    if (($file->move($desination, $name))) {
                        $saveData->image = $name;
                    } else {
                        Session::flash('flash_message', 'Oh know ! Some thing went wrong.');
                        Session::flash('flash_type', 'alert-danger');
                        return Redirect::to('admin/pim/basicinfo');
                    }
                }
                $saveData->employee_type = 2;
                $saveData->functional_designation = 2;
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully Updated basic info.');
                    Session::flash('flash_type', 'alert-success');
                    return Redirect::to('admin/pim/basicinfo');
                } else {
                    Session::flash('flash_message', 'Successfully Updated basic info.');
                    Session::flash('flash_type', 'alert-success');
                    return Redirect::to('admin/pim/basicinfo');
                }
            }
        }
    }

    public function deleteBasicinfo($param) {
        $data = BasicinfoModel::find($param);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted basic info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/basicinfo');
        }
    }

    public function viewBasicInfo($param) {
        $title = "Basic Information";
        $data = BasicinfoModel::where('PBI_ID', $param)->first();
        return View::make('admin.pim.basicinfo-view')->with('data', $data);
    }

    public function getEssentialinfo() {
        $title = "Essential Information";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
         BasicinfoModel::where('PBI_JOB_STATUS', 'In Service');
            $data = DB::table('essential_info')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'essential_info.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('essential_info.ESSENTIAL_BANK_NAME', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = DB::table('essential_info')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'essential_info.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.essentialinfo-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addEssentialinfo() {
        $title = "Essential Information";
        if (Request::method() == 'POST') {
            $save = DB::table('essential_info')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully added essential info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/esentialinfo');
            }
        } else {
            $bankType = BankType::all();
            return View::make('admin.pim.essentialinfo-add')->with(['bankType' => $bankType, 'title' => $title]);
        }
    }

    public function editEssentialinfo($id = NULL) {
        $title = "Essentail Information";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('essential_info')->where('ESSENTIAL_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated essential info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/esentialinfo');
            } else {
                Session::flash('flash_message', 'Failed updated essential info.');
                Session::flash('flash_type', 'alert-danger');
                return Redirect::to('admin/pim/esentialinfo');
            }
        } else {
            $data = EssentialInfo::where('ESSENTIAL_ID', $id)->first();
            $bankType = BankType::all();
            return View::make('admin.pim.essentialinfo-edit')->with('data', $data)->with('bankType', $bankType)->with('title', $title);
        }
    }

    public function deleteEssentialinfo($param) {
        $data = EssentialInfo::find($param);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted essential info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/esentialinfo');
        }
    }

    public function education() {
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = DB::table('education_detail')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'education_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('education_detail.EDUCATION_NOE', 'LIKE', '%' . $search . '%')
                    ->orWhere('education_detail.EDUCATION_BU', 'LIKE', '%' . $search . '%')
                    ->orWhere('education_detail.EDUCATION_GROUP', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = Education::orderBy('EDUCATION_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'education_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.education-show')->with('data', $data);
    }

    public function addEducation() {
        if (Request::method() == 'POST') {
            $save = DB::table('education_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted education info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/education');
            }
        } else {
            $eduSubject = EducationSubject::all();
            $eduQua = EducationQualification::all();
            $university = University::all();
            return View::make('admin.pim.education-add')
                            ->with('eduSubject', $eduSubject)
                            ->with('eduQua', $eduQua)
                            ->with('university', $university);
        }
    }

    public function editEducation($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('edu_id');
            $update = DB::table('education_detail')->where('EDUCATION_D_ID', Input::get('edu_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated education info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/education');
            } else {
                Session::flash('flash_message', 'Successfully updated education info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/education');
            }
        } else {
            $data = Education::where('EDUCATION_D_ID', $id)->first();
            $eduSubject = EducationSubject::all();
            $eduQua = EducationQualification::all();
            $university = University::all();
            return View::make('admin.pim.education-edit')
                            ->with('data', $data)
                            ->with('eduSubject', $eduSubject)
                            ->with('eduQua', $eduQua)
                            ->with('university', $university);
        }
    }

    public function deleteEducation($id) {
        $data = Education::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully delete education info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/education');
        }
    }

    public function courseDiploma() {
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = CourseDiploma::orderBy('CD_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'course_diploma_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = CourseDiploma::orderBy('CD_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'course_diploma_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.course-diploma-show')->with('data', $data);
    }

    public function addCoursediploma() {
        if (Request::method() == 'POST') {
            $save = DB::table('course_diploma_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully insert Course/Diploma info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/course-diploma');
            }
        } else {
            return View::make('admin.pim.course-diploma-add');
        }
    }

    public function editCoursediploma($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('cd_id');
            $save = DB::table('course_diploma_detail')->where('CD_D_ID', Input::get('cd_id'))->update($input);
            if ($save) {
                Session::flash('flash_message', 'Successfully updated Course/Diploma Information.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/course-diploma');
            } else {
                Session::flash('flash_message', 'Failed updated Course/Diploma Information.');
                Session::flash('flash_type', 'alert-danger');
                return Redirect::to('admin/pim/course-diploma');
            }
        } else {
            $data = CourseDiploma::where('CD_D_ID', $id)->first();
            return View::make('admin.pim.course-diploma-edit')->with('data', $data);
        }
    }

    public function deleteCoursediploma($id) {
        $data = CourseDiploma::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Course/Diploma info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/course-diploma');
        }
    }

    public function experience() {
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = Experience::orderBy('EXPERIENCE_DETAIL_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'experience_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = Experience::orderBy('EXPERIENCE_DETAIL_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'experience_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.experience-show')->with('data', $data);
    }

    public function addExperience() {
        if (Request::method() == 'POST') {
            $save = DB::table('experience_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted experience info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/experience');
            }
        } else {
            return View::make('admin.pim.experience-add');
        }
    }

    public function editExperience($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('exp_id');
            $update = DB::table('experience_detail')->where('EXPERIENCE_DETAIL_ID', Input::get('exp_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated experience info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/experience');
            } else {
                Session::flash('flash_message', 'Successfully updated experience info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/experience');
            }
        } else {
            $data = Experience::find($id);
            return View::make('admin.pim.experience-edit')->with('data', $data);
        }
    }

    public function deleteExperience($param) {
        $data = Experience::find($param);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted experience info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/experience');
        }
    }

    public function nominie() {
        $title = "PIM || Nominee";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = Nominie::orderBy('NOMINEE_DETAIL_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'nominee_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = Nominie::orderBy('NOMINEE_DETAIL_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'nominee_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.nominie-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addNominie() {
        $title = "PIM || Nominee";
        if (Request::method() == 'POST') {
            $save = DB::table('nominee_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted nominie info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/nominie');
            }
        } else {
            $relation = Relation::all();
            return View::make('admin.pim.nominie-add')->with(['relation' => $relation, 'title' => $title]);
        }
    }

    public function editNominie($id = NULL) {
        $title = "PIM || Nominee";
        if (Request::method() == 'POST') {
            $input = Input::except('n_id');
            $update = DB::table('nominee_detail')->where('NOMINEE_DETAIL_ID', Input::get('n_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated nominie info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/nominie');
            } else {
                Session::flash('flash_message', 'Successfully updated nominie info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/nominie');
            }
        } else {
            $data = Nominie::find($id);
            $relation = Relation::all();
            return View::make('admin.pim.nominie-edit')->with(['data' => $data, 'title' => $title])->with('relation', $relation);
        }
    }

    public function deleteNominie($id) {
        $data = Nominie::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted nominie info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/nominie');
        }
    }

    public function familySpouse() {
        $title = "PIM || Family/Spouse";
        if (Request::method() == "POST") {
            $data = FamilySpouse::orderBy('FAMILY_M_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'family_master.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(8);
        } else {
            $data = FamilySpouse::orderBy('FAMILY_M_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'family_master.PBI_ID')
                    ->paginate(8);
        }
        return View::make('admin.pim.family-spouse-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addFamilyspouse() {
        $title = "PIM || Family/Spouse";
        if (Request::method() == 'POST') {
            $save = DB::table('family_master')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted family/spouse info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-spouse');
            }
        } else {
            $profession = Profession::all();
            return View::make('admin.pim.family-spouse-add')->with(['profession' => $profession, 'title' => $title]);
        }
    }

    public function editFamilyspouse($id = NULL) {
        $title = "PIM || Family/Spouse";
        if (Request::method() == 'POST') {
            $input = Input::except('fs_id');
            $update = DB::table('family_master')->where('FAMILY_M_ID', Input::get('fs_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated family/spouse info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-spouse');
            } else {
                Session::flash('flash_message', 'Successfully updated family/spouse info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-spouse');
            }
        } else {
            $data = FamilySpouse::find($id);
            $profession = Profession::all();
            return View::make('admin.pim.family-spouse-edit')->with(['data' => $data, 'title' => $title])->with('profession', $profession);
        }
    }

    public function deleteFamilyspouse($id) {
        $data = FamilySpouse::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted family/spouse info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/family-spouse');
        }
    }

    public function familyChild() {
        $title = "PIM || Family Children";
        if (Request::method() == 'POST') {
            $data = FamilyChild::orderBy('FAMILY_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'child_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = FamilyChild::orderBy('FAMILY_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'child_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.family-child-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addFamilychild() {
        $title = "PIM || Family Children";
        if (Request::method() == 'POST') {
            $save = DB::table('child_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted family/child info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-child');
            }
        } else {
            $profession = Profession::all();
            return View::make('admin.pim.family-child-add')->with(['profession' => $profession, 'title' => $title]);
        }
    }

    public function editFamilychild($id = NULL) {
        $title = "PIM || Family Children";
        if (Request::method() == 'POST') {
            $input = Input::except('fc_id');
            $update = DB::table('child_detail')->where('FAMILY_D_ID', Input::get('fc_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated family/child info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-child');
            } else {
                Session::flash('flash_message', 'Successfully updated family/child info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/family-child');
            }
        } else {
            $data = FamilyChild::find($id);
            $profession = Profession::all();
            return View::make('admin.pim.family-child-edit')->with('data', $data)->with(['profession' => $profession, 'title' => $title]);
        }
    }

    public function deleteFamilychild($id) {
        $data = FamilyChild::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted family/child info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/family-child');
        }
    }

    public function brotherSister() {
        $title = "PIM || Brother & Sister";
        if (Request::method() == 'POST') {
            $data = BrotherSister::orderBy('FAMILY_BS_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'brother_sister_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = BrotherSister::orderBy('FAMILY_BS_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'brother_sister_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.brother-sister-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addBrothersister() {
        $title = "PIM || Brother & Sister";
        if (Request::method() == 'POST') {
            $save = DB::table('brother_sister_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted brother/sister info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/brother-sister');
            }
        } else {
            $relation = Relation::all();
            $profession = Profession::all();
            return View::make('admin.pim.brother-sister-add')
                            ->with('relation', $relation)
                            ->with('profession', $profession)
                            ->with('title', $title);
        }
    }

    public function editBrothersister($id = NULL) {
        $title = "PIM || Brother & Sister";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('brother_sister_detail')->where('FAMILY_BS_DID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated brother/sister info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/brother-sister');
            } else {
                Session::flash('flash_message', 'Successfully updated brother/sister info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/brother-sister');
            }
        } else {
            $data = BrotherSister::find($id);
            $relation = Relation::all();
            $profession = Profession::all();
            return View::make('admin.pim.brother-sister-edit')
                            ->with('data', $data)
                            ->with('relation', $relation)
                            ->with('profession', $profession)
                            ->with('title', $title);
        }
    }

    public function deleteBrothersister($id) {
        $data = BrotherSister::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted brother/sister info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/brother-sister');
        }
    }

    public function guardian() {
        $title = "PIM || Guardain";
        if (Request::method() == 'POST') {
            $data = Gurdian::orderBy('GUARDIAN_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'guardian.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Gurdian::orderBy('GUARDIAN_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'guardian.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.guardian-show')->with(['data' => $data, 'title' => $title]);
    }

    public function guardianAdd() {
        $title = "PIM || Guardain";
        if (Request::method() == 'POST') {
            $save = DB::table('guardian')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted guardian info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/guardian');
            }
        } else {
            $relation = Relation::all();
            $profession = Profession::all();
            return View::make('admin.pim.guardian-add')->with(['relation' => $relation, 'title' => $title, 'profession' => $profession]);
        }
    }

    public function guardianEdit($id = NULL) {
        $title = "PIM || Guardain";
        if (Request::method() == 'POST') {
            $input = Input::except('GUARDIAN_ID');
            $update = DB::table('guardian')->where('GUARDIAN_ID', Input::get('GUARDIAN_ID'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated guardian info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/guardian');
            } else {
                Session::flash('flash_message', 'Successfully updated guardian info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/guardian');
            }
        } else {
            $data = Gurdian::where('GUARDIAN_ID', $id)->first();
            $relation = Relation::all();
            $profession = Profession::all();
            return View::make('admin.pim.guardian-edit')
                            ->with(['data' => $data, 'title' => $title])
                            ->with('relation', $relation)
                            ->with('profession', $profession);
        }
    }

    public function guardianDelete($id) {
        $data = Gurdian::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Gurdian info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/guardian');
        }
    }

    public function postingShow() {
        $title = "PIM || Posting";
        if (Request::method() == 'POST') {
            $data = Posting::orderBy('POSTING_CODE', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'posting.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Posting::orderBy('POSTING_CODE', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'posting.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.posting-show')->with(['data' => $data, 'title' => $title]);
    }

    public function postingAdd() {
        $title = "PIM || Posting";
        if (Request::method() == 'POST') {
            $save = DB::table('posting')->insert(Input::all());
            if ($save) {
                $pbi_id=Input::get('PBI_ID');
                $Basic=BasicinfoModel::find($pbi_id);
                $updateBasic=BasicinfoModel::find($pbi_id);
                $updateBasic->PBI_DESIGNATION= Input::has('POSTING_DESIGNATION') ? Input::get('POSTING_DESIGNATION'): $Basic->PBI_DESIGNATION;
                $updateBasic->PBI_ZONE= Input::has('POSTING_ZONE') ? Input::get('POSTING_ZONE') : $Basic->PBI_ZONE;
                $updateBasic->PBI_DOMAIN= Input::has('POSTING_DOMAIN') ? Input::get('POSTING_DOMAIN') : $Basic->PBI_DOMAIN;
                $updateBasic->PBI_DEPARTMENT= Input::has('POSTING_DEPARTMENT') ? Input::get('POSTING_DEPARTMENT') : $Basic->PBI_DEPARTMENT;
                $updateBasic->PBI_PROJECT= Input::has('PROJECT_DESC') ? Input::get('PROJECT_DESC') : $Basic->PBI_PROJECT;
                $updateBasic->PBI_BRANCH= Input::has('POSTING_BRANCH') ? Input::get('POSTING_BRANCH') : $Basic->PBI_BRANCH;
                $updateBasic->PBI_AREA= Input::has('POSTING_AREA') ? Input::get('POSTING_AREA') : $Basic->PBI_AREA;
                $updateBasic->PBI_REGION= Input::has('POSTING_REGION') ? Input::get('POSTING_REGION') : $Basic->PBI_REGION;
                $updateBasic->save();
                Session::flash('flash_message', 'Successfully inserted posting info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/posting');
            }
        } else {
            $domain = Domain::all();
            $departmental = DepartmentType::all();
            $designation = Designationtype::all();
            $project = Project::all();
            $region = Region::all();
            $zone = Zone::all();
            $area = Area::all();
            $branch = Branch::all();
            return View::make('admin.pim.posting-add')->with([
                        'domain' => $domain,
                        'departmental' => $departmental,
                        'designation' => $designation,
                        'project' => $project,
                        'region' => $region,
                        'zone' => $zone,
                        'area' => $area,
                        'branch' => $branch,
                        'title' => $title
            ]);
        }
    }

    public function postingEdit($id = NULL) {
        $title = "PIM || Posting";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('posting')->where('POSTING_CODE', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated posting info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/posting');
            } else {
                Session::flash('flash_message', 'Successfully updated posting info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/posting');
            }
        } else {
            $data = Posting::where('POSTING_CODE', $id)->first();
            $domain = Domain::all();
            $departmental = DepartmentType::all();
            $designation = Designationtype::all();
            $project = Project::all();
            $region = Region::all();
            $zone = Zone::all();
            $area = Area::all();
            $branch = Branch::all();

            return View::make('admin.pim.posting-edit')
                            ->with('data', $data)
                            ->with('domain', $domain)
                            ->with('departmental', $departmental)
                            ->with('designation', $designation)
                            ->with('project', $project)
                            ->with('region', $region)
                            ->with('zone', $zone)
                            ->with('area', $area)
                            ->with('branch', $branch)
                            ->with('title', $title);
        }
    }

    public function postingDelete($id) {
        $data = Posting::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted posting info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/posting');
        }
    }

    public function trainingShow() {
        $title = "PIM || Training";
        if (Request::method() == 'POST') {
            $data = Training::orderBy('TRAINING_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'training_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Training::orderBy('TRAINING_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'training_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.training-show')->with(['data' => $data, 'title' => $title]);
    }

    public function trainingAdd() {
        $title = "PIM || Training";
        if (Request::method() == 'POST') {
            $save = DB::table('training_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted posting information.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/training');
            }
        } else {
            return View::make('admin.pim.training-add')->with('title', $title);
        }
    }

    public function trainingDelete($id) {
        $data = Training::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted training information.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/training');
        }
    }

    public function trainingEdit($id = NULL) {
        $title = "PIM || Training";
        if (Request::method() == 'POST') {
            $input = Input::except('TRAINING_D_ID');
            $update = DB::table('training_detail')->where('TRAINING_D_ID', Input::get('TRAINING_D_ID'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated training information.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/training');
            } else {
                Session::flash('flash_message', 'Successfully updated training information.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/training');
            }
        } else {
            $data = Training::where('TRAINING_D_ID', $id)->first();
            return View::make('admin.pim.training-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function notInservice() {
        $title = "Not In Services";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        return View::make('admin.pim.not-inservices')->with([
                    'title' => $title,
                    'data' => $data,
        ]);
    }

    public function notInserviceAdd($pbi_id = NULL) {
        $title = "Not In Services";
        if (Request::method() == 'POST') {
            $update = BasicinfoModel::find(Input::get('PBI_ID'));
            $update->PBI_JOB_STATUS = Input::get('services_status');
            $update->PBI_separation_type = Input::get('separation_type');
            $update->resign_date = Input::get('resign_date');
            $update->save();
            Session::flash('flash_message', 'Successfully saved.');
            Session::flash('flash_type', 'alert-success');
            return Redirect::to('/admin/pim/notinservice');
        } else {
            $notinservices = JobStatus::all();
            $data = BasicinfoModel::where('PBI_ID', $pbi_id)->first();
            return View::make('admin.pim.not-inservices-add')->with([
                        'title' => $title,
                        'data' => $data,
                        'notinservices' => $notinservices
            ]);
        }
    }

    public function quater() {
        $title = "PIM || Quater Management";
        if (Request::method() == 'POST') {
            $data = Quater::orderBy('QUARTER_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'quarter_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Quater::orderBy('QUARTER_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'quarter_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.quater-show')->with(['data' => $data, 'title' => $title]);
    }

    public function addQuater() {
        $title = "PIM || Quater Management";
        if (Request::method() == 'POST') {
            $save = DB::table('quarter_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted quater/dor info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/quater');
            }
        } else {
            return View::make('admin.pim.quater-add')->with('title', $title);
        }
    }

    public function editQuater($id = NULL) {
        $title = "PIM || Quater Management";
        if (Request::method() == 'POST') {
            $input = Input::except('q_id');
            $update = DB::table('quarter_detail')->where('QUARTER_DID', Input::get('q_id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated quater/dor info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim//quater');
            } else {
                Session::flash('flash_message', 'Successfully updated quater/dor info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/quater');
            }
        } else {
            $data = Quater::find($id);
            return View::make('admin.pim.quater-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function deleteQuater($id) {
        $data = Quater::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted quater/dor info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/quater');
        }
    }

    public function motorCycle() {
        $title = "PIM || Motor Cycle Management";
        if (Request::method() == 'POST') {
            $data = MotorCycle::orderBy('MOTOR_CYCLE_D_CODE', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'motor_cycle_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = MotorCycle::orderBy('MOTOR_CYCLE_D_CODE', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'motor_cycle_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.motor-cycle-view')->with(['data' => $data, 'title' => $title]);
    }

    public function addMotorcycle() {
        $title = "PIM || Motor Cycle Management";
        if (Request::method() == 'POST') {
            $save = DB::table('motor_cycle_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted Motor/Cycle info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/motor-cycle');
            }
        } else {
            return View::make('admin.pim.motor-cycle-add')->with('title', $title);
        }
    }

    public function editMotorcycle($id = NULL) {
        $title = "PIM || Motor Cycle Management";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('motor_cycle_detail')->where('MOTOR_CYCLE_D_CODE', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Motor/Cycle info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/motor-cycle');
            } else {
                Session::flash('flash_message', 'Successfully updated Motor/Cycle info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/motor-cycle');
            }
        } else {
            $data = MotorCycle::where('MOTOR_CYCLE_D_CODE', $id)->first();
            return View::make('admin.pim.motor-cycle-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function deleteMotorcycle($id) {
        $data = MotorCycle::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted quater/dor info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/quater');
        }
    }

    public function healthCheckupShow() {
        $title = "PIM || Health Checkup";
         if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = HealthCheckup::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('year', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('test_date', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = HealthCheckup::orderBy('id', 'desc')
                ->paginate(25);
        }
        return View::make('admin.pim.healthCheckup-show')->with(['data' => $data, 'title' => $title]);
    }

    public function healthCheckupAdd() {
        $title = "PIM || Health Checkup";
        if (Request::method() == 'POST') {
            $saveData = new HealthCheckup;
                $saveData->PBI_ID = Input::get('PBI_ID');
                $saveData->year = Input::get('year');
                  $saveData->description = Input::get('description');
                   $saveData->test_date=date("Y/m/d");
           
$PBI_ID = Input::get('PBI_ID');
             // $saveData = new RelativeDetails;
             //    $saveData->PBI_ID = Input::get('PBI_ID');
             //$saveData->RELATIVE_ID = Input::get('RELATIVE_ID');


            if (Input::file('status')->isValid()) {
$file=Input::get('year');
      $destinationPath = 'assets/miscellaneous_file/'; // upload path
      $extension = Input::file('status')->getClientOriginalExtension(); // getting image extension
     
      $fileName = $file.$PBI_ID.'.'.$extension; // renameing image
            $saveData->status = $fileName;
      Input::file('status')->move($destinationPath, $fileName); // uploading file to given path
      // sending back with message
      
}
else {

  // sending back with error message.
  Session::flash('error', 'uploaded file is not valid');
  return Redirect::back();
}
            if ($saveData->save()) {
                Session::flash('flash_message', 'Successfully inserted  info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/healthCheckup');
            }
        } else {
            return View::make('admin.pim.healthCheckup-add')->with('title', $title);
        }
    }

    public function healthCheckupEdit($id = NULL) {
        $title = "PIM || Health Checkup";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('health_checkup_information')->where('id', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated health checkup info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/healthCheckup');
            } else {
                Session::flash('flash_message', 'Successfully updated health checkup info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/healthCheckup');
            }
        } else {
            $data = HealthCheckup::where('id', $id)->first();
            return View::make('admin.pim.healthCheckup-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function healthCheckupDelete($id) {
        $data = HealthCheckup::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted health checkup info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/healthCheckup');
        }
    }

    public function reinstatementShow() {
        $title = "PIM || Reinstatement";
        if (Request::method() == 'POST') {
            $data = ReinstatementStatus::orderBy('id', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'reinstatement_status.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = ReinstatementStatus::orderBy('id', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'reinstatement_status.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.reinstatement-show')->with(['data' => $data, 'title' => $title]);
    }

    public function reinstatementAdd() {
        $title = "PIM || Reinstatement";
        if (Request::method() == 'POST') {
            $save = DB::table('reinstatement_status')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted reinstatement status info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/reinstatement');
            }
        } else {
            return View::make('admin.pim.reinstatement-add')->with('title', $title);
        }
    }

    public function reinstatementEdit($id = NULL) {
        $title = "PIM || Reinstatement";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('reinstatement_status')->where('id', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated health checkup info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/reinstatement');
            } else {
                Session::flash('flash_message', 'Successfully updated health checkup info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/reinstatement');
            }
        } else {
            $data = ReinstatementStatus::where('id', $id)->first();
            return View::make('admin.pim.reinstatement-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function reinstatementDelete($id) {
        $data = ReinstatementStatus::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Reinstatement Status info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/reinstatement');
        }
    }

   public function personalAssetShow() {
        $title = "PIM || Personal Asset";
       if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = PersonalAsset::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('year', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('date', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = PersonalAsset::orderBy('id', 'desc')
                ->paginate(25);
        }

       

              
       // return View::make('admin.pim.relativeDetails-show')->with('data', $data);
        return View::make('admin.pim.personalAsset-show')->with(['data' => $data, 'title' => $title]);
    }
   


    public function personalAssetAdd() {
        $title = "PIM || Personal Asset";
        if (Request::method() == 'POST') {
            $saveData = new PersonalAsset;
                $saveData->PBI_ID = Input::get('PBI_ID');
                $saveData->year = Input::get('year');
                  $saveData->description = Input::get('description');
                   $saveData->date=date("Y/m/d");
           
$PBI_ID = Input::get('PBI_ID');
             // $saveData = new RelativeDetails;
             //    $saveData->PBI_ID = Input::get('PBI_ID');
             //$saveData->RELATIVE_ID = Input::get('RELATIVE_ID');


            if (Input::file('status')->isValid()) {
$file=Input::get('year');
      $destinationPath = 'assets/vip_file/'; // upload path
      $extension = Input::file('status')->getClientOriginalExtension(); // getting image extension
     
      $fileName = $file.$PBI_ID.'.'.$extension; // renameing image
            $saveData->status = $fileName;
      Input::file('status')->move($destinationPath, $fileName); // uploading file to given path
      // sending back with message
      
}
else {

  // sending back with error message.
  Session::flash('error', 'uploaded file is not valid');
  return Redirect::back();
}
            if ($saveData->save()) {
                Session::flash('flash_message', 'Successfully inserted  info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/personalAsset');
            }
        } else {
            return View::make('admin.pim.personalAsset-add')->with('title', $title);
        }
    }

    public function personalAssetEdit($id = NULL) {
        $title = "PIM || Personal Asset";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('personal_asset_information')->where('id', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated prsonal asset status info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/personalAsset');
            } else {
                Session::flash('flash_message', 'Successfully updated prsonal asset status info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/personalAsset');
            }
        } else {
            $data = PersonalAsset::where('id', $id)->first();
            return View::make('admin.pim.personalAsset-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function personalAssetDelete($id) {
        $data = PersonalAsset::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted prsonal asset status info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/personalAsset');
        }
    }

    public function relativeDetailsShow() {
         if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = RelativeDetails::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('RELATIVE_D_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('RELATIVE_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('RELATIVE_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('RELATIVE_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
             $data = RelativeDetails::orderBy('RELATIVE_D_ID', 'desc')
                ->paginate(25);
        }
       

              
        return View::make('admin.pim.relativeDetails-show')->with('data', $data);
    }


    public function relativeDetailsAdd() {
        if (Request::method() == 'POST') {
                $saveData = new RelativeDetails;
                $saveData->PBI_ID = Input::get('PBI_ID');
                $saveData->RELATIVE_ID = Input::get('RELATIVE_ID');
                $saveData->RELATIVE_DEPARTMENT=date("Y/m/d");
           $PBI_ID = Input::get('PBI_ID');

             // $saveData = new RelativeDetails;
             //    $saveData->PBI_ID = Input::get('PBI_ID');
             //$saveData->RELATIVE_ID = Input::get('RELATIVE_ID');


            if (Input::file('RELATIVE_NAME')->isValid()) {
$file=Input::get('RELATIVE_ID');
      $destinationPath = 'assets/personal_file/'; // upload path
      $extension = Input::file('RELATIVE_NAME')->getClientOriginalExtension(); // getting image extension
     
      $fileName = $file.$PBI_ID.'.'.$extension; // renameing image
            $saveData->RELATIVE_NAME = $fileName;
      Input::file('RELATIVE_NAME')->move($destinationPath, $fileName); // uploading file to given path
      // sending back with message
      
}
else {

  // sending back with error message.
  Session::flash('error', 'uploaded file is not valid');
  return Redirect::back();
}

           // $save = DB::table('relative_detail')->insert(Input::all());
if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added basic info.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/pim/relativeDetails');
                }
            // if ($save) {
            //     Session::flash('flash_message', 'Successfully inserted relative details info.');
            //     Session::flash('flash_type', 'alert-success');
            //     return Redirect::to('admin/pim/relativeDetails');
            // }
       
        } else {
            $departmental = DepartmentType::all();
            $designation = Designationtype::all();
            $jobStatus = JobStatus::all();
            $relation = Relation::all();
            $zone = Zone::all();

            return View::make('admin.pim.relativeDetails-add')
                            ->with('departmental', $departmental)
                            ->with('designation', $designation)
                            ->with('jobStatus', $jobStatus)
                            ->with('relation', $relation)
                            ->with('zone', $zone);
        }
    }

    public function relativeDetailsEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('relative_detail')->where('RELATIVE_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated relative details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/relativeDetails');
            } else {
                Session::flash('flash_message', 'Successfully updated relative details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/relativeDetails');
            }
        } else {
            $data = RelativeDetails::where('RELATIVE_D_ID', $id)->first();
            $departmental = DepartmentType::all();
            $designation = Designationtype::all();
            $relation = Relation::all();
            $jobStatus = JobStatus::all();
            $zone = Zone::all();

            return View::make('admin.pim.relativeDetails-edit')
                            ->with('data', $data)
                            ->with('departmental', $departmental)
                            ->with('relation', $relation)
                            ->with('jobStatus', $jobStatus)
                            ->with('zone', $zone)
                            ->with('designation', $designation);
        }
    }

    public function relativeDetailsDelete($id) {
        $data = RelativeDetails::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted relative details info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/relativeDetails');
        }
    }

    public function referencePersonShow() {
        $title = "PIM || Reference Person";
        if (Request::method() == 'POST') {
            $data = ReferencePerson::orderBy('RPERSON_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'reference_person.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = ReferencePerson::orderBy('RPERSON_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'reference_person.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.referencePerson-show')->with(['data' => $data, 'title' => $title]);
    }

    public function referencePersonAdd() {
        $title = "PIM || Reference Person";
        if (Request::method() == 'POST') {
            $save = DB::table('reference_person')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully inserted reference Person.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/referencePerson');
            }
        } else {
            return View::make('admin.pim.referencePerson-add')->with('title', $title);
        }
    }

    public function referencePersonDelete($id) {
        $data = ReferencePerson::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Reference Person.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/referencePerson');
        }
    }

    public function referencePersonEdit($id = NULL) {
        $title = "PIM || Reference Person";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('reference_person')->where('RPERSON_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated relative details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/referencePerson');
            } else {
                Session::flash('flash_message', 'Successfully updated relative details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/referencePerson');
            }
        } else {
            $data = ReferencePerson::where('RPERSON_ID', $id)->first();
            return View::make('admin.pim.referencePerson-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function empTransfer() {
        if (Request::method() == 'POST' && !empty(Input::get('search'))) {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        return View::make('admin.pim.transfer-emp-add')->with([
                    'title' => 'Tranfer Employee',
                    'data' => $data
        ]);
    }

    public function transferShow($pbi_id) {
        $data = TransferDetails::orderBy('TRANSFER_D_ID', 'desc')
                ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'transfer_detail.PBI_ID')
                ->where('transfer_detail.PBI_ID', $pbi_id)
                ->get();
        return View::make('admin.pim.transfer-show')->with('data', $data);
    }

    public function transferAdd($id = NULL) {
        if (Request::method() == 'POST') {
            $save = DB::table('transfer_detail')->insert(Input::all());
            if ($save) {
                $updateTransfer = BasicinfoModel::find(Input::get('PBI_ID'));
                $updateTransfer->PBI_DOMAIN = Input::has('TRANSFER_PRESENT_DOMAIN') ? Input::get('TRANSFER_PRESENT_DOMAIN') : Input::get('TRANSFER_PAST_DOMAIN');
                $updateTransfer->PBI_DEPARTMENT = Input::has('TRANSFER_PRESENT_DEPT') ? Input::get('TRANSFER_PRESENT_DEPT') : Input::get('TRANSFER_PAST_DEPT');
                $updateTransfer->PBI_PROJECT = Input::has('TRANSFER_PRESENT_PROJECT') ? Input::get('TRANSFER_PRESENT_PROJECT') : Input::get('TRANSFER_PAST_PROJECT');
                $updateTransfer->PBI_REGION = Input::has('TRANSFER_PRESENT_REGION') ? Input::get('TRANSFER_PRESENT_REGION') : Input::get('TRANSFER_PAST_REGION');
                $updateTransfer->PBI_ZONE = Input::has('TRANSFER_PRESENT_ZONE') ? Input::get('TRANSFER_PRESENT_ZONE') : Input::get('TRANSFER_PAST_ZONE');
                $updateTransfer->PBI_AREA = Input::has('TRANSFER_PRESENT_AREA') ? Input::get('TRANSFER_PRESENT_AREA') : Input::get('TRANSFER_PAST_AREA');
                $updateTransfer->PBI_BRANCH = Input::has('TRANSFER_PRESENT_BRANCH') ? Input::get('TRANSFER_PRESENT_BRANCH') : Input::get('TRANSFER_PAST_BRANCH');
                $updateTransfer->save();
                Session::flash('flash_message', 'Successfully Inserted Transfer Info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-transfer');
            }
        } else {
            $empData = BasicinfoModel::where('PBI_ID', $id)->first();
            $domain = Domain::all();
            $departmental = DepartmentType::all();
            $project = Project::all();
            $region = Region::all();
            $zone = Zone::all();
            $area = Area::all();
            $branch = Branch::all();
            return View::make('admin.pim.transfer-add')
                            ->with('empdata', $empData)
                            ->with('domain', $domain)
                            ->with('departmental', $departmental)
                            ->with('project', $project)
                            ->with('region', $region)
                            ->with('zone', $zone)
                            ->with('area', $area)
                            ->with('branch', $branch);
        }
    }

    public function transferDelete($id) {
        $data = TransferDetails::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted transfer management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/emp-transfer');
        }
    }

    public function transferEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('transfer_detail')->where('TRANSFER_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated transfer info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/transfer');
            } else {
                Session::flash('flash_message', 'Successfully updated transfer info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-transfer');
            }
        } else {
            $data = TransferDetails::where('TRANSFER_D_ID', $id)->first();
            $domain = Domain::all();
            $departmental = DepartmentType::all();
            $project = Project::all();
            $region = Region::all();
            $zone = Zone::all();
            $area = Area::all();
            $branch = Branch::all();

            return View::make('admin.pim.transfer-edit')
                            ->with('data', $data)
                            ->with('domain', $domain)
                            ->with('departmental', $departmental)
                            ->with('project', $project)
                            ->with('region', $region)
                            ->with('zone', $zone)
                            ->with('area', $area)
                            ->with('branch', $branch);
        }
    }

    public function valuesfShow() {
        $title = "PIM || Values";
        if (Request::method() == 'POST') {
            $data = ValuesEntry::orderBy('VALUES_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'values_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = ValuesEntry::orderBy('VALUES_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'values_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.values-show')->with(['data' => $data, 'title' => $title]);
    }

    public function valuesAdd() {
        if (Request::method() == 'POST') {
            $save = DB::table('values_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Values Entry.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/values');
            }
        } else {
            return View::make('admin.pim.values-add');
        }
    }

    public function valuesDelete($id) {
        $data = ValuesEntry::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Values.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/values');
        }
    }

    public function projectStaffShow() {
        $title = "PIM || Project Staff Management";
        if (Request::method() == 'POST') {
            $data = ProjectStaff::orderBy('PS_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'project_staff_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = ProjectStaff::orderBy('PS_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'project_staff_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.projectStaff-show')->with(['data' => $data, 'title' => $title]);
    }

    public function projectStaffAdd() {
        $title = "PIM || Project Staff Management";
        if (Request::method() == 'POST') {
            $save = DB::table('project_staff_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Project Staff Information.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/projectStaff');
            }
        } else {
            return View::make('admin.pim.projectStaff-add')->with('title', $title);
        }
    }

    public function projectStaffDelete($id) {
        $data = ProjectStaff::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted project staff info.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/projectStaff');
        }
    }

    public function projectStaffEdit($id = NULL) {
        $title = "PIM || Project Staff Management";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('project_staff_detail')->where('PS_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated project staff info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/projectStaff');
            } else {
                Session::flash('flash_message', 'Successfully updated project staff info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/projectStaff');
            }
        } else {
            $data = ProjectStaff::where('PS_D_ID', $id)->first();
            return View::make('admin.pim.projectStaff-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function valuesEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('values_detail')->where('VALUES_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated values details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/values');
            } else {
                Session::flash('flash_message', 'Successfully updated values details info.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/values');
            }
        } else {
            $data = ValuesEntry::where('VALUES_D_ID', $id)->first();
            return View::make('admin.pim.values-edit')->with('data', $data);
        }
    }

    public function pfCheckListShow() {
        $title = "PIM || Persoanal File Check List";
        if (Request::method() == 'POST') {
            $data = PFCheckStatus::orderBy('PF_STATUS_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'pf_status.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = PFCheckStatus::orderBy('PF_STATUS_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'pf_status.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.pfCheckList-show')->with(['data' => $data, 'title' => $title]);
    }

    public function pfCheckListAdd() {
        $title = "PIM || Persoanal File Check List";
        if (Request::method() == 'POST') {
            $save = DB::table('pf_status')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Personal File Check Status.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/pfCheckList');
            }
        } else {
            return View::make('admin.pim.pf-checkList-add')->with('title', $title);
        }
    }

    public function pfCheckListDelete($id) {
        $data = PFCheckStatus::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully deleted Personal File Check Status.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/pfCheckList');
        }
    }

    public function pfCheckListEdit($id = NULL) {
        $title = "PIM || Persoanal File Check List";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('pf_status')->where('PF_STATUS_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Personal File Check Status.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pfCheckList');
            } else {
                Session::flash('flash_message', 'Successfully updated Personal File Check Status.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/pfCheckList');
            }
        } else {
            $data = PFCheckStatus::where('PF_STATUS_ID', $id)->first();
            return View::make('admin.pim.pfCheckList-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function incrementShow() {
        $title = "PIM || Increment";
        if (Request::method() == 'POST') {
            $data = Increment::orderBy('INCREMENT_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'increment_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Increment::orderBy('INCREMENT_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'increment_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.increment-show')->with(['data' => $data, 'title' => $title]);
    }

    public function incrementAdd() {
        $title = "PIM || Increment";
        if (Request::method() == 'POST') {
            $save = DB::table('increment_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Increment Entry Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/increment');
            }
        } else {
            $designation = Designationtype::all();
            return View::make('admin.pim.increment-add')->with(['designation' => $designation, 'title' => $title]);
        }
    }

    public function incrementDelete($id) {
        $data = Increment::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Increment Entry Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/increment');
        }
    }

    public function incrementEdit($id = NULL) {
        $title = "PIM || Increment";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('increment_detail')->where('INCREMENT_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Increment Entry Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/increment');
            } else {
                Session::flash('flash_message', 'Successfully updated Increment Entry Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/increment');
            }
        } else {
            $data = Increment::where('INCREMENT_D_ID', $id)->first();
            $designation = Designationtype::all();
            return View::make('admin.pim.increment-edit')->with([
                        'data' => $data,
                        'title' => $title,
                        'designation' => $designation
            ]);
        }
    }

    public function empPromotion() {
        $title = "Promotion Management";
        if (Request::method() == 'POST' && !empty(Input::get('search'))) {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        $designation = Designationtype::all();
        return View::make('admin.pim.promotion-add-emp')->with([
                    'title' => $title,
                    'designation' => $designation,
                    'data' => $data
        ]);
    }

    public function promotionShow($pbi_id) {
        $data = PromotionDetails::orderBy('PROMOTION_D_ID', 'desc')
                ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'promotion_detail.PBI_ID')
                ->where('promotion_detail.PBI_ID', $pbi_id)
                ->get();
        return View::make('admin.pim.promotion-show')->with('data', $data);
    }

    public function promotionAdd($pbi_id = NULL) {
        $title = "Promotion Management";
        if (Request::method() == 'POST') {
            $save = DB::table('promotion_detail')->insert(Input::all());
            if ($save) {
                $desg = Input::get('PROMOTION_PRESENT_DESG');
                $promotion_grade = Designationtype::where('DESG_DESC', $desg)->first();
                $updateBasic = BasicinfoModel::find(Input::get('PBI_ID'));
                $updateBasic->PBI_DESIGNATION = Input::has('PROMOTION_PRESENT_DESG') ? Input::get('PROMOTION_PRESENT_DESG') : Input::get('PROMOTION_PAST_DESG');
                $updateBasic->PBI_DOJ_PP = Input::get('PROMOTION_DATE');
                $updateBasic->PBI_DESG_GRADE = $promotion_grade->DESG_ID;
                $updateBasic->save();
                Session::flash('flash_message', 'Successfully Inserted Promotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-promotion');
            }
        } else {
            $data = BasicinfoModel::where('PBI_ID', $pbi_id)->first();
            $designation = Designationtype::all();
            return View::make('admin.pim.promotion-add')->with(['designation' => $designation, 'title' => $title, 'data' => $data]);
        }
    }

    public function promotionDelete($id) {
        $data = PromotionDetails::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Promotion Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/emp-promotion');
        }
    }

    public function promotionEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('promotion_detail')->where('PROMOTION_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Promotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/promotion');
            } else {
                Session::flash('flash_message', 'Successfully updated Promotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-promotion');
            }
        } else {
            $data = PromotionDetails::where('PROMOTION_D_ID', $id)->first();
            $designation = Designationtype::all();
            return View::make('admin.pim.promotion-edit')->with('data', $data)->with('designation', $designation);
        }
    }

    public function empDemotion() {
        $title = "Demotion";
        $demotionReason = DemotionReason::all();
        $designation = Designationtype::all();
        if (Request::method() == 'POST' && !empty(Input::get('search'))) {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        return View::make('admin.pim.demotion-emp-add')->with([
                    'title' => $title,
                    'demotionReason' => $demotionReason,
                    'designation' => $designation,
                    'data' => $data
        ]);
    }

    public function demotionShow($pbi_id) {
        $data = DemotionDetails::orderBy('DEMOTION_D_ID', 'desc')
                ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'demotion_detail.PBI_ID')
                ->where('demotion_detail.PBI_ID', $pbi_id)
                ->get();
        return View::make('admin.pim.demotion-show')->with('data', $data);
    }

    public function demotionAdd($pbi_id = NULL) {

        if (Request::method() == 'POST') {
            $save = DB::table('demotion_detail')->insert(Input::all());
            if ($save) {
                $desg = Input::get('DEMOTION_PRESENT_DESG');
                $promotion_grade = Designationtype::where('DESG_DESC', $desg)->first();

                $updatebasic = BasicinfoModel::find(Input::get('PBI_ID'));
//
                $updatebasic->PBI_DESIGNATION = Input::has('DEMOTION_PRESENT_DESG') ? Input::get('DEMOTION_PRESENT_DESG') : Input::get('DEMOTION_PAST_DESG');
                $updatebasic->PBI_DOJ_PP = Input::get('DEMOTION_DATE');
                $updatebasic->PBI_DESG_GRADE = $promotion_grade->DESG_ID;
                $updatebasic->save();
                Session::flash('flash_message', 'Successfully Inserted Demotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-demotion');
            }
        } else {
            $demotionReason = DemotionReason::all();
            $designation = Designationtype::all();
            $data = BasicinfoModel::where('PBI_ID', $pbi_id)->first();
            return View::make('admin.pim.demotion-add')
                            ->with('demotionReason', $demotionReason)
                            ->with('designation', $designation)
                            ->with('data', $data);
        }
    }

    public function demotionDelete($id) {

        $data = DemotionDetails::find($id);

        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Demotion Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/emp-demotion');
        }
    }

    public function demotionEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('demotion_detail')->where('DEMOTION_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Demotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-demotion');
            } else {
                Session::flash('flash_message', 'Successfully updated Demotion Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/emp-demotion');
            }
        } else {
            $data = DemotionDetails::where('DEMOTION_D_ID', $id)->first();
            $demotionReason = DemotionReason::all();
            $designation = Designationtype::all();

            return View::make('admin.pim.demotion-edit')
                            ->with('data', $data)
                            ->with('demotionReason', $demotionReason)
                            ->with('designation', $designation);
        }
    }

    public function leavenworthShow() {
        $title = "PIM || Leave Management";
        if (Request::method() == 'POST') {
            $data = Leavenworth::orderBy('LEAVE_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'leave_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Leavenworth::orderBy('LEAVE_D_ID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'leave_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.leavenworth-show')->with(['data' => $data, 'title' => $title]);
    }

    public function leavenworthAdd() {
        $title = "PIM || Leave Management";
        if (Request::method() == 'POST') {
            $save = DB::table('leave_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Leave Input Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/leavenworth');
            }
        } else {
            return View::make('admin.pim.leavenworth-add')->with('title', $title);
        }
    }

    public function leavenworthDelete($id) {
        $data = Leavenworth::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Leave Input Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/leavenworth');
        }
    }

    public function leavenworthEdit($id = NULL) {
        $title = "PIM || Leave Management";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('leave_detail')->where('LEAVE_D_ID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Leave Input Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/leavenworth');
            } else {
                Session::flash('flash_message', 'Successfully updated Leave Input Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/leavenworth');
            }
        } else {
            $data = Leavenworth::where('LEAVE_D_ID', $id)->first();
            return View::make('admin.pim.leavenworth-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function eidActionShow() {
        $title = "PIM || Ed Active Management";
        if (Request::method() == 'POST') {
            $data = EdAction::orderBy('ED_ACTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'ed_action_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orwhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = EdAction::orderBy('ED_ACTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'ed_action_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.eidAction-show')->with(['data' => $data, 'title' => $title]);
    }

    public function eidActionAdd() {
        if (Request::method() == 'POST') {
            $save = DB::table('ed_action_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted ED Active Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/eidAction');
            }
        } else {
            return View::make('admin.pim.eidAction-add');
        }
    }

    public function eidActionDelete($id) {
        $data = EdAction::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted ED Active Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/eidAction');
        }
    }

    public function eidActionEdit($id = NULL) {
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('ed_action_detail')->where('ED_ACTION_DID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated ED Active Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/eidAction');
            } else {
                Session::flash('flash_message', 'Successfully updated ED Active Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/eidAction');
            }
        } else {
            $data = EdAction::where('ED_ACTION_DID', $id)->first();
            return View::make('admin.pim.eidAction-edit')->with('data', $data);
        }
    }

    public function departmentalShow() {
        $title = "PIM || Department Action";
        if (Request::method() == 'POST') {
            $data = Departmental::orderBy('DEPARTMENTAL_OBJ_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'departmental_objection_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = Departmental::orderBy('DEPARTMENTAL_OBJ_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'departmental_objection_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.departmental-show')->with(['data' => $data, 'title' => $title]);
    }

    public function departmentalAdd() {
        $title = "PIM || Department Action";
        if (Request::method() == 'POST') {
            $save = DB::table('departmental_objection_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Departmental Action Management');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/departmental');
            }
        } else {
            return View::make('admin.pim.departmental-add')->with('title', $title);
        }
    }

    public function departmentalDelete($id) {

        $data = Departmental::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Departmental Action Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/departmental');
        }
    }

    public function departmentalEdit($id = NULL) {
        $title = "PIM || Department Action";
        if (Request::method() == 'POST') {

            $input = Input::except('id');
            $update = DB::table('departmental_objection_detail')->where('DEPARTMENTAL_OBJ_DID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Departmental Action Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/departmental');
            } else {
                Session::flash('flash_message', 'Successfully updated Departmental Action Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/departmental');
            }
        } else {
            $data = Departmental::where('DEPARTMENTAL_OBJ_DID', $id)->first();
            return View::make('admin.pim.departmental-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function adminActionShow() {
        $title = "PIM || Admin Action";
        if (Request::method() == 'POST') {
            $data = AdminSection::orderBy('ADMIN_ACTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'admin_action_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = AdminSection::orderBy('ADMIN_ACTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'admin_action_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.adminAction-show')->with(['data' => $data, 'title' => $title]);
    }

    public function adminActionAdd() {
        $title = "PIM || Admin Action";
        if (Request::method() == 'POST') {
            $save = DB::table('admin_action_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Administration Action Management');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/adminAction');
            }
        } else {
            $actionType = ActionType::all();
            return View::make('admin.pim.adminAction-add')->with(['actionType' => $actionType, 'title' => $title]);
        }
    }

    public function adminActionDelete($id) {
        $data = AdminSection::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Administration Action Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/adminAction');
        }
    }

    public function adminActionEdit($id = NULL) {
        $title = "PIM || Admin Action";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('admin_action_detail')->where('ADMIN_ACTION_DID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated  Administration Action Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/adminAction');
            } else {
                Session::flash('flash_message', 'Successfully updated Administration Action Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/adminAction');
            }
        } else {
            $data = AdminSection::where('ADMIN_ACTION_DID', $id)->first();
            $actionType = ActionType::all();
            return View::make('admin.pim.adminAction-edit')->with(['data' => $data, 'title' => $title])->with('actionType', $actionType);
        }
    }

    public function finObjectionShow() {
        $title = "PIM || Financial Objection";
        if (Request::method() == 'POST') {
            $data = FinObjection::orderBy('FINANCIAL_OBJECTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'financial_objection_detail.PBI_ID')
                    ->where('personnel_basic_info.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->orWhere('personnel_basic_info.PBI_NAME', 'LIKE', '%' . Input::get('search') . '%')
                    ->paginate(25);
        } else {
            $data = FinObjection::orderBy('FINANCIAL_OBJECTION_DID', 'desc')
                    ->join('personnel_basic_info', 'personnel_basic_info.PBI_ID', '=', 'financial_objection_detail.PBI_ID')
                    ->paginate(25);
        }
        return View::make('admin.pim.finObjection-show')->with(['data' => $data, 'title' => $title]);
    }

    public function finObjectionAdd() {
        $title = "PIM || Financial Objection";
        if (Request::method() == 'POST') {
            $save = DB::table('financial_objection_detail')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Inserted Financial Objection Management');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/finObjection');
            }
        } else {
            return View::make('admin.pim.finObjection-add')->with('title', $title);
        }
    }

    public function finObjectionDelete($id) {
        $data = FinObjection::find($id);
        if ($data->delete()) {
            Session::flash('flash_message', 'Successfully Deleted Financial Objection Management.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/finObjection');
        }
    }

    public function finObjectionEdit($id = NULL) {
        $title = "PIM || Financial Objection";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $update = DB::table('financial_objection_detail')->where('FINANCIAL_OBJECTION_DID', Input::get('id'))->update($input);
            if ($update) {
                Session::flash('flash_message', 'Successfully updated Deleted Financial Objection Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/finObjection');
            } else {
                Session::flash('flash_message', 'Successfully updated Deleted Financial Objection Management.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/finObjection');
            }
        } else {
            $data = FinObjection::where('FINANCIAL_OBJECTION_DID', $id)->first();
            return View::make('admin.pim.finObjection-edit')->with(['data' => $data, 'title' => $title]);
        }
    }

    public function delail_report_selection() {
        return View::make('admin.report.delail_report_selection');
    }

    public function aprAction() {
        $title = "APR & PROMOTION ACTION";
        if (Request::method() == 'POST') {
            $search = Input::get('search');
            $data = BasicinfoModel::where('PBI_ID', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_NAME', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DESIGNATION', 'LIKE', '%' . $search . '%')
                    ->orWhere('PBI_DEPARTMENT', 'LIKE', '%' . $search . '%')
                    ->paginate(25);
        } else {
            $data = BasicinfoModel::paginate(25);
        }
        return View::make('admin.apr.apr-promotion')->with([
                    'title' => $title,
                    'employee' => $data
        ]);
    }

    public function aprPromotion($pbi_id = NULL) {
        $title = "APR & PROMOTION";
        if (Request::method() == "POST") {
            $data = array(
                'APR_YEAR' => Input::get('apr_year'),
                'd_r_increment' => Input::get('dept_increment'),
                'd_r_promotion' => Input::get('hrm_increment'),
                'hr_r_increment' => Input::get('dept_promotion'),
                'APR_MARKS' => Input::get('social_mark') + Input::get('evaluation'),
                'APR_STATUS' => Input::get('status'),
                'APR_RESULT' => Input::get('result'),
                'social_work_marks' => Input::get('social_mark'),
                'evaluation_marks' => Input::get('evaluation'),
                'PBI_ID' => Input::get('PBI_ID'),
            );
            $query = DB::table('apr_detail_old')->insert($data);
            if ($query) {
                Session::flash('flash_message', 'Successfully Saved');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/apr/apr-action');
            }
        } else {
            $employee = BasicinfoModel::where('PBI_ID', $pbi_id)->first();
            return View::make('admin.apr.appr-promotion-add')->with([
                        'title' => $title,
                        'employee' => $employee
            ]);
        }
    }

    public function aprPromotionDetails($pbi_id) {
        $title = "APR & PROMOTION DETAILS";
        $employee = DB::table('apr_detail_old')->where('PBI_ID', '=', $pbi_id)->get();
        return View::make('admin.apr.app-promotion-details')->with([
                    'title' => $title,
                    'employee' => $employee
        ]);
    }

    public function actionManagement() {
        $title = "Action Management";
        if (Request::method() == 'POST') {
            $data = DB::table('action_management')
                    ->join('action_subject', 'action_subject.id', '=', 'action_management.subject_id')
                    ->join('action_category', 'action_category.id', '=', 'action_management.category_id')
                    ->where('action_management.PBI_ID', 'LIKE', '%' . Input::get('search') . '%')
                    ->select('action_management.*', 'action_subject.action_subject', 'action_category.action_category')
                    ->paginate(25);
        } else {
            $data = DB::table('action_management')
                    ->join('action_subject', 'action_subject.id', '=', 'action_management.subject_id')
                    ->join('action_category', 'action_category.id', '=', 'action_management.category_id')
                    ->select('action_management.*', 'action_subject.action_subject', 'action_category.action_category')
                    ->paginate(25);
        }
        return View::make('admin.pim.action-management-show')->with([
                    'title' => $title,
                    'data' => $data
        ]);
    }

    public function add_actionManagement() {
        $title = "Action Management";
        if (Request::method() == 'POST') {
            $save = DB::table('action_management')->insert(Input::all());
            if ($save) {
                Session::flash('flash_message', 'Successfully Saved');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/action-management');
            }
        } else {
            $action_type = ActionType::all();
            $action_subject = ActionSubject::all();
            $action_category = ActionCategory::all();
            return View::make('admin.pim.action-management-add')->with([
                        'title' => $title,
                        'action_type' => $action_type,
                        'action_subject' => $action_subject,
                        'action_category' => $action_category
            ]);
        }
    }

    public function edit_actionManagement($id = NULL) {
        $title = "Action Management";
        if (Request::method() == 'POST') {
            $input = Input::except('id');
            $save = DB::table('action_management')->where('id', Input::get('id'))->update($input);
            if ($save) {
                Session::flash('flash_message', 'Successfully Updated');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('admin/pim/action-management');
            }
        } else {
            $data = DB::table('action_management')->where('id', $id)->first();
            $action_subject = ActionSubject::all();
            $action_category = ActionCategory::all();
            return View::make('admin.pim.action-management-edit')->with([
                        'title' => $title,
                        'action_subject' => $action_subject,
                        'action_category' => $action_category,
                        'data' => $data
            ]);
        }
    }

    public function delete_actionManagement($id) {

        $delete = DB::table('action_management')->where('id', $id)->delete();
        if ($delete) {
            Session::flash('flash_message', 'Successfully Updated');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('admin/pim/action-management');
        }
    }

}
