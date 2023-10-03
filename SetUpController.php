<?php

class SetUpController extends \AdminBaseController {

    public function __construct() {
        parent::__construct();
        if (!Auth::admin()->check()) {
            header('Location:' . url('/admin'));
            exit();
        }
    }

    public function action_typeInfo() {
        $data = ActionType::all();
        return View::make('admin.setup.action_type-show')->with('data', $data);
    }

    public function action_typeAdd() {
        if (Request::method() == 'POST') {
            $rules = [
                'ACTION_TYPE' => 'required|unique:action_type',
                'effect' => 'required'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/action_type')->withErrors($validator);
            } else {
                $saveData = new ActionType();
                $saveData->ACTION_TYPE = Input::get('ACTION_TYPE');
                $saveData->effect = Input::get('effect');
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Action Type Information.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/action_type');
                }
            }
        }
    }

    public function branchTypeInfo() {
        $data = Branch::all();
        return View::make('admin.setup.branch-show')->with('data', $data);
    }

    public function branchTypeAdd() {
        if (Request::method() == 'POST') {
            $rules = [
                'BRANCH_NAME' => 'required'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/branchType')->withErrors($validator);
            } else {
                $saveData = new Branch;
                $saveData->BRANCH_NAME = Input::get('BRANCH_NAME');
                if ($saveData->save()) {
                  
                    Session::flash('flash_message', 'Successfully added Branch Type Information.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/branchType');
                }
            }
        }
    }

    public function regionInfo() {
        $data = Region::all();
        return View::make('admin.setup.region-show')->with('data', $data);
    }

    public function regionAdd() {
        if (Request::method() == 'POST') {
            $rules = [
                'region_name' => 'required|unique:region'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/region')->withErrors($validator);
            } else {
                $saveData = new Region;

                $saveData->region_name = Input::get('region_name');
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Region Info.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/region');
                }
            }
        }
    }

    public function departmentTypeInfo() {

        $data = DepartmentType::all();
        return View::make('admin.setup.departmentType-show')->with('data', $data);
    }

    public function departmentTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'DEPT_DESC' => 'required|unique:department_type'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/departmentType')->withErrors($validator);
            } else {
                $save = DB::table('department_type')->insert(array('DEPT_DESC' => Input::get('DEPT_DESC'), 'DEPT_SHORT_NAME' => Input::get('DEPT_DESC')));
                if ($save) {
                    Session::flash('flash_message', 'Successfully added Department Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/departmentType');
                }
            }
        }
    }

    public function designationtypeInfo() {

        $data = Designationtype::all();
        return View::make('admin.setup.designationtype-show')->with('data', $data);
    }

    public function designationtypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'DESG_DESC' => 'required|unique:designationtype',
                'DESG_GRADE' => 'required'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/designationtype')->withErrors($validator);
            } else {
                $saveData = new Designationtype;
                $saveData->DESG_DESC = Input::get('DESG_DESC');
//                $saveData->DESG_SHORT_NAME = Input::get('DESG_DESC');
                $saveData->DESG_GRADE = Input::get('DESG_GRADE');
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Designation Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/designationtype');
                }
            }
        }
    }

    public function domainInfo() {

        $data = Domain::all();
        return View::make('admin.setup.domain-show')->with('data', $data);
    }

    public function domainAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'DOMAIN_DESC' => 'required|unique:domai'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/domain')->withErrors($validator);
            } else {
                $saveData = new Domain;

                $saveData->DOMAIN_DESC = Input::get('DOMAIN_DESC');
                $saveData->DOMAIN_SHORT_NAME = Input::get('DOMAIN_DESC');
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Domain Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/domain');
                }
            }
        }
    }

    public function educationSubTypeInfo() {

        $data = EducationSubject::all();
        return View::make('admin.setup.educationSubType-show')->with('data', $data);
    }

    public function educationSubTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'SUBJECT_NAME' => 'required|unique:edu_subject'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/educationSubType')->withErrors($validator);
            } else {
                $saveData = new EducationSubject;

                $saveData->SUBJECT_NAME = Input::get('SUBJECT_NAME');

                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Education Subject Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/educationSubType');
                }
            }
        }
    }

    public function eduQualificationInfo() {

        $data = EducationQualification::all();
        return View::make('admin.setup.eduQualification-show')->with('data', $data);
    }

    public function eduQualificationAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'EDU_QUA_DESC' => 'required|unique:edu_qua'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/eduQualification')->withErrors($validator);
            } else {
                $saveData = new EducationQualification;

                $saveData->EDU_QUA_DESC = Input::get('EDU_QUA_DESC');
                $saveData->EDU_QUA_SHORT_NAME = Input::get('EDU_QUA_DESC');

                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Education Qualification Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/eduQualification');
                }
            }
        }
    }

    public function projectInfo() {

        $data = Project::all();
        return View::make('admin.setup.project-show')->with('data', $data);
    }

    public function projectAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'PROJECT_DESC' => 'required|unique:project'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/project')->withErrors($validator);
            } else {
                $saveData = new Project;

                $saveData->PROJECT_DESC = Input::get('PROJECT_DESC');
                $saveData->PROJECT_SHORT_NAME = Input::get('PROJECT_DESC');

                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Project Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/project');
                }
            }
        }
    }

    public function relationInfo() {
        $data = Relation::all();
        return View::make('admin.setup.relation-show')->with('data', $data);
    }

    public function relationAdd() {
        if (Request::method() == 'POST') {
            $rules = [
                'RELATION_NAME' => 'required|unique:relation'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/relation')->withErrors($validator);
            } else {
                $save = DB::table('relation')->insert(Input::all());
                if ($save) {
                    Session::flash('flash_message', 'Successfully added Relation Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/relation');
                }
            }
        }
    }

    public function universityInfo() {
        $data = University::all();
        return View::make('admin.setup.university-show')->with('data', $data);
    }

    public function universityAdd() {
        if (Request::method() == 'POST') {
            $rules = [
                'UNIVERSITY_NAME' => 'required|unique:university'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/university')->withErrors($validator);
            } else {
                $save = DB::table('university')->insert(Input::all());
                if ($save) {
                    Session::flash('flash_message', 'Successfully added University Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/university');
                }
            }
        }
    }

    public function zoneInfo() {

        $data = Zone::all();
        return View::make('admin.setup.zone-show')->with('data', $data);
    }

    public function zoneAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'ZONE_NAME' => 'required|unique:zon'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/zone')->withErrors($validator);
            } else {
                $saveData = new Zone;

                $saveData->ZONE_NAME = Input::get('ZONE_NAME');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Zone Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/zone');
                }
            }
        }
    }

    public function demotionReasonInfo() {

        $data = DemotionReason::all();
        return View::make('admin.setup.demotionReason-show')->with('data', $data);
    }

    public function demotionReasonAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'REASON' => 'required|unique:demotion_reason'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/demotionReason')->withErrors($validator);
            } else {
                $saveData = new DemotionReason;

                $saveData->REASON = Input::get('REASON');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Demotion Reasone.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/demotionReason');
                }
            }
        }
    }

    public function professionTypeInfo() {

        $data = Profession::all();
        return View::make('admin.setup.professionType-show')->with('data', $data);
    }

    public function professionTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'profession_name' => 'required|unique:profession'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/professionType')->withErrors($validator);
            } else {
                $saveData = new Profession;

                $saveData->profession_name = Input::get('profession_name');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Profession Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/professionType');
                }
            }
        }
    }

    public function areaTypeInfo() {

        $data = Area::all();
        return View::make('admin.setup.areaType-show')->with('data', $data);
    }

    public function areaTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'AREA_NAME' => 'required|unique:area'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/areaType')->withErrors($validator);
            } else {
                $saveData = new Area;

                $saveData->AREA_NAME = Input::get('AREA_NAME');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Area Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/areaType');
                }
            }
        }
    }

    public function instituteTypeInfo() {

        $data = Institute::all();
        return View::make('admin.setup.institute-show')->with('data', $data);
    }

    public function instituteTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'institute_name' => 'required|unique:institute'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/instituteType')->withErrors($validator);
            } else {
                $saveData = new Institute;

                $saveData->institute_name = Input::get('institute_name');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Institute Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/instituteType');
                }
            }
        }
    }

    public function jobStatusInfo() {

        $data = JobStatus::all();
        return View::make('admin.setup.jobStatus-show')->with('data', $data);
    }

    public function jobStatusAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'job_status' => 'required|unique:job_status'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/jobStatus')->withErrors($validator);
            } else {
                $saveData = new JobStatus;

                $saveData->job_status = Input::get('job_status');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Job Status.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/jobStatus');
                }
            }
        }
    }

    public function actionSubjectInfo() {

        $data = ActionSubject::all();
        return View::make('admin.setup.actionSubject-show')->with('data', $data);
    }

    public function actionSubjectAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'action_subject' => 'required|unique:action_subject'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/actionSubject')->withErrors($validator);
            } else {
                $saveData = new ActionSubject;

                $saveData->action_subject = Input::get('action_subject');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Action Subject.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/actionSubject');
                }
            }
        }
    }

    public function actionCategoryInfo() {

        $data = ActionCategory::all();
        return View::make('admin.setup.actionCategory-show')->with('data', $data);
    }

    public function actionCategoryAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'action_category' => 'required|unique:action_category'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/actionCategory')->withErrors($validator);
            } else {
                $saveData = new ActionCategory;

                $saveData->action_category = Input::get('action_category');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Action Category.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/actionCategory');
                }
            }
        }
    }

    public function presentFileInfo() {

        $data = PresentFileStatus::all();
        return View::make('admin.setup.presentFile-show')->with('data', $data);
    }

    public function presentFileAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'present_file_status' => 'required|unique:present_file_status'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/presentFile')->withErrors($validator);
            } else {
                $saveData = new PresentFileStatus;

                $saveData->present_file_status = Input::get('present_file_status');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Present File Status.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/presentFile');
                }
            }
        }
    }

    public function bankTypeInfo() {

        $data = BankType::all();
        return View::make('admin.setup.bankType-show')->with('data', $data);
    }

    public function bankTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'BANK_NAME' => 'required|unique:bank_type'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/bankType')->withErrors($validator);
            } else {
                $saveData = new BankType();
                $saveData->BANK_NAME = Input::get('BANK_NAME');
                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Bank Type Information.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/bankType');
                }
            }
        }
    }

    public function areaExpertiseInfo() {

        $data = AreaExpertise::all();
        return View::make('admin.setup.areaExpertise-show')->with('data', $data);
    }

    public function areaExpertiseAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'area_expertise' => 'required|unique:area_expertise'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/areaExpertise')->withErrors($validator);
            } else {
                $saveData = new AreaExpertise;

                $saveData->area_expertise = Input::get('area_expertise');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Area of Expertise.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/areaExpertise');
                }
            }
        }
    }

    public function functionalDesignationInfo() {

        $data = FunctionalDesignation::all();
        return View::make('admin.setup.functionalDesignation-show')->with('data', $data);
    }

    public function functionalDesignationAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'functional_designation' => 'required|unique:functional_designation'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/functionalDesignation')->withErrors($validator);
            } else {
                $saveData = new FunctionalDesignation;

                $saveData->functional_designation = Input::get('functional_designation');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Functional Designation.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/functionalDesignation');
                }
            }
        }
    }

    public function employeeTypeInfo() {

        $data = EmployeeType::all();
        return View::make('admin.setup.employeeType-show')->with('data', $data);
    }

    public function employeeTypeAdd() {

        if (Request::method() == 'POST') {
            $rules = [
                'employee_type' => 'required|unique:hrm_employee_type'
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('admin/employeeType')->withErrors($validator);
            } else {
                $saveData = new EmployeeType;

                $saveData->employee_type = Input::get('employee_type');


                if ($saveData->save()) {
                    Session::flash('flash_message', 'Successfully added Employee Type.');
                    Session::flash('flash_type', 'alert-success');
                    return Illuminate\Support\Facades\Redirect::to('admin/employeeType');
                }
            }
        }
    }

}
