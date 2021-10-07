<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\FeedbackModel;

class User extends BaseController
{

	private $authModel;
	private $sessLogin;

    private $userModel;
	private $feedbackModel;

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

        $this->userModel = new UserModel();
		$this->feedbackModel = new FeedbackModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeUser" => "1" 
				],
			];
            
            $allData = $this->userModel->allByLimitPanel(1000, 0);
            $data['result'] = $allData;
            return view('user_view', $data);
            
		}

		return view('login_view');
	}

	public function log()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeLog" => "1" 
				],
			];
            
            $allData = $this->paylogModel->allByLimit(1000, 0);
            $data['result'] = $allData;
            return view('alllog_view', $data);
            
		}

		return view('login_view');
	}

	public function feedback()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $dataPush = $this->feedbackModel->do_feedback($this->postBody);
        
        $arr = $dataPush;
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function push_fcm()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $dataPush = $this->userModel->sendFCMMessage($this->postBody['token'], $this->postBody['data']);
        
        $arr = $dataPush;
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

}