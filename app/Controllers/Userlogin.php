<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;

class Userlogin extends BaseController
{

	private $authModel;
	private $sessLogin;


	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeAdmin" => "1" 
				],
			];
            
            $allData = $this->authModel->allByLimitPanel(1000, 0);
            $data['result'] = $allData;
            return view('admin_view', $data);
            
		}

		return view('login_view');
	}

}