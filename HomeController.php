<?php

  class HomeController extends \BaseController {
    public $logData;

      public function __construct() {
          parent::__construct();
          $this->logData=Auth::admin()->get();
          $this->data['pageTitle'] = 'Home Page';
      }

      public function index() {
          $this->data['jobs'] = Job::where('status', '=', 'Active')->get();
          // echo "<pre/>"; print_r($this->data['jobs']); exit();
          // $this->data['applicant']    =    Applicant::find(Auth::applicant()->get()->id);
          return View::make('home', $this->data);
      }

      public function jodDetail($id) {
          $this->data['job'] = Job::where('jobID', '=', $id)->get()->first();
          // echo "<pre/>"; print_r($this->data); //exit();
          return View::make('front.job_detail', $this->data);
      }

      public function applicantRegister() {
          if (Auth::applicant()->check()) {
              return Redirect::route('dashboard.index');
          } else {
              return View::make('front.applicantregister', $this->data);
          }
      }

      public function store() {
          $validator = Validator::make($input = Input::all(), Applicant::rules('create'));

          if ($validator->fails()) {
              return Redirect::back()->withErrors($validator)->withInput();
          }

          DB::beginTransaction();
          try {

              $name = explode(' ', $input['fullName']);
              $firstName = ucfirst($name[0]);

              $applicantId = Applicant::create(Input::all())->id;

              $filename = null;
              // Profile Image Upload
              if (Input::hasFile('profileImage')) {
                  $path = public_path() . "/profileImages/";
                  File::makeDirectory($path, $mode = 0777, true, true);

                  $image = Input::file('profileImage');
                  $extension = $image->getClientOriginalExtension();
                  $filename = $input['mobileNumber'] . ".jpg";

                  //                Image::make($image->getRealPath())->resize('872','724')->save($path.$filename);
                  Image::make($image->getRealPath())
                          ->fit(872, 724, function ($constraint) {
                              $constraint->upsize();
                          })->save($path . $filename);
              }


              // -------------- UPLOAD THE DOCUMENTS  -----------------
              $documents = ['resume', 'IDProof', 'signature'];

              foreach ($documents as $document) {
                  if (Input::hasFile($document)) {

                      $path = public_path() . "/applicant_documents/{$document}/";
                      File::makeDirectory($path, $mode = 0777, true, true);

                      $file = Input::file($document);
                      $extension = $file->getClientOriginalExtension();
                      $filename = "{$document}_{$applicantId}_{$firstName}.$extension";

                      Input::file($document)->move($path, $filename);
                      Applicant_document::create([
                          'applicant_id' => $applicantId,
                          'fileName' => $filename,
                          'type' => $document
                      ]);
                  }
              }

              if ($this->data['setting']->employee_add == 1) {
                  $this->data['applicant_name'] = $input['fullName'];
                  $this->data['applicant_email'] = $input['email'];
                  $this->data['applicant_password'] = $input['password'];

                  //        Send Employee Add Mail
                  Mail::send('emails.applicant_register', $this->data, function ($message) use ($input) {
                      $message->from($this->data['setting']->email, $this->data['setting']->name);
                      $message->to($input['email'], $input['fullName'])
                              ->subject('Account Created - ' . $this->data['setting']->website);
                  });
              }

              //  ********** END UPLOAD THE DOCUMENTS**********
          } catch (\Exception $e) {
              DB::rollback();
              throw $e;
          }

          DB::commit();
          return Redirect::route('applicant.login')->with('success', "<strong>{$input['fullName']}</strong> registered successfully!");
      }
    
      public function check_name() {
          echo "hello";
          exit();
      }

      public function userList(){
         $title = "Admin List";
         if(Request::method('POST')){
          $search=Input::get('search');
            $data=DB::table('admins')
            ->where('email', 'LIKE', '%' . $search . '%')
             ->orWhere('name', 'LIKE', '%' . $search . '%')
            ->paginate(10);
         }else{

         $data=DB::table('admins')->paginate(10);
         }
         return View::make('sytem-admin-list')->with(['title' => $title,'data'=>$data]);

      }

      public function userRegistration(){
        $title = "Registration";
        if (Request::method() == 'POST'){
          $data=array(
            'name'=>Input::get('name'),
            'email'=>Input::get('email'),
            'role'=>Input::get('role'),
            'password'=>Hash::make(Input::get('password')),
            );
          $save = DB::table('admins')->insert($data);
            if ($save) {
                Session::flash('flash_message', 'Successfully Created.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('/admin/admin-list');
            }
        }else{
          return View::make('user-registration')->with(['title' => $title]);
        }
      }

      public function editUser($id=null){
        $title="Edit Admin";
        if (Request::method() == 'POST'){
          $id=Input::get('id');
          $data=array(
            'name'=>Input::get('name'),
            'email'=>Input::get('email'),
            'role'=>Input::get('role'),
            // 'password'=>Hash::make(Input::get('password')),
            );
          // $save = DB::table('admins')->insert($data);
           $save = DB::table('admins')->where('id',$id)->update($data);
           if($save){
             Session::flash('flash_message', 'Successfully upadetd.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('/admin/admin-list');
           }else{
            Session::flash('flash_message', 'Successfully upadetd.');
                Session::flash('flash_type', 'alert-success');
                return Redirect::to('/admin/admin-list');
           }
        }else{
          $data=  $data=DB::table('admins')->where('id',$id)->first();
          return View::make('user-registration-edit')->with(['title'=>$title,'data'=>$data]);
        }

      }

      public function deleteUser($id){
        $delete= $data=DB::table('admins')->where('id',$id)->delete();
        if($delete){
            Session::flash('flash_message', 'Successfully deleted.');
            Session::flash('flash_type', 'alert-danger');
            return Redirect::to('/admin/admin-list');
        }
      }

  }
  