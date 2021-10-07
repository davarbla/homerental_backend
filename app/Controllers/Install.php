<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\InstallModel;

class Install extends BaseController
{
	protected $postBody; 
	private $authModel;
	private $sessLogin;
	protected $userModel;

    private $installModel;

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

        $this->installModel = new InstallModel();
		$this->userModel = new UserModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeInstall" => "1" 
				],
			];
            
            $allDataCateg = $this->installModel->allByLimit(1000, 0);
            $data['result'] = $allDataCateg;
            return view('install_view', $data);
            
		}

		return view('login_view');
	}

	public function saveUpdate()
    {   
        $this->postBody = $this->authModel->authHeader($this->request);

        $arr = array();
        
        if ($this->postBody['tk'] != '') {
            $dataInstall = $this->installModel->saveUpdate($this->postBody);
            $arr = [$dataInstall];
        }

        if (count( $arr) < 1) {
            $json = array(
                "result" =>  $arr,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" =>  $arr,
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