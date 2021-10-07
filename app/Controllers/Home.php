<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\FeedbackModel;
use App\Models\UserModel;
use App\Models\TransModel;
use App\Models\InstallModel;
use App\Models\RentModel;

class Home extends BaseController
{

	private $authModel;
	private $sessLogin;

	private $feedbackModel;
	private $userModel;
	private $depositModel;
	private $transModel;
	private $rentModel;

	private $settingModel;
	private $BASE_URL_API = 'https://homerental.hobbbies.id/';

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

		$this->feedbackModel = new FeedbackModel();
		$this->userModel = new UserModel();
		$this->installModel = new InstallModel();

		$this->transModel = new TransModel();
		$this->rentModel = new RentModel();

    }

	public function index()
	{
		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {
			
			$offset = 0;
			$limit = 10;

			$getLimit = $this->request->getVar('lt');
			if ($getLimit != '') {
				$exp = explode(",", $getLimit);
				$offset = (int) $exp[0];
				$limit = (int) $exp[1];
				
			}

			$data = [
				"menu" => [ 
					"activeIndex" => "1" 
				],
			];

			$data['trans'] = $this->transModel->getTotal();
			$data['user'] = $this->userModel->getTotal();
			$data['rent'] = $this->rentModel->getTotal();

			$data['latest'] = $this->transModel->allByLimitPanel(5,0);
			$data['feedback'] = $this->feedbackModel->allByLimitPanel(5,0);
			
			return view('home_view', $data);
		}

		return view('login_view');
	}

	public function login()
	{
		$this->sessLogin = session();
		$em = $this->request->getVar('email');
		$ps = $this->request->getVar('password');

		if ($em != '' && $ps != ''){
			$passwd = $this->generatePassword($ps);
			$userLogin = $this->authModel->loginByEmail($em, $passwd);
			//print_r($userLogin);
			//die();
			
			//for admin@gmail.com password:  adminhobb2021     demo@gmail.com   password: userdemo2021
			//id_userlogin] => 1 [username] => admin [email] => admin@gmail.com [password] => bb245162d456bbab0c88ec8f781253d5 [flag] => 1 [status] => 1 [date_created] => 2021-07-01 14:07:40 [date_updated] => 2021-07-01 14:07:40
			if ($userLogin['id_userlogin'] != '') {
				$newdata = [
					'fullname_ss'  => $userLogin['fullname'],
					'username_ss'  => $userLogin['username'],
					'email_ss'     => $userLogin['email'],
					'user'		   => $userLogin,
					'logged_in'    => TRUE
				];
			}

			$this->authModel->addSession($this->sessLogin, $newdata);
		}
		
		$check = $this->authModel->getDataSession($this->sessLogin);
		
		return redirect()->to(base_url()); 
	}

	public function logout()
	{
		$this->sessLogin = session();
		$this->authModel->removeSession($this->sessLogin);
		return redirect()->to(base_url()); 
	}

	public function alluser()
	{
		return view('alluser_view');
	}

	

	public function hash_password() {
        $this->postBody = $this->authModel->authHeader($this->request);
		if ($this->postBody['ps'] != '') {
        	print_r($this->generatePassword($this->postBody['ps']));
		}
		else {
			$json = array(
                "result" =>  array(),
                "code" => "201",
                "message" => "Error, Required parameter!",
            );

			//add the header here
			header('Content-Type: application/json');
			echo json_encode($json);
			die();
		}
    }

    private function generatePassword($password) {
        return md5(sha1(hash("sha256", $password)));
    }

}