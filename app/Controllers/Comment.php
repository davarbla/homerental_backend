<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\CommentModel;

class Comment extends BaseController
{

	private $authModel;
	private $sessLogin;

    private $commentModel;

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

        $this->commentModel = new CommentModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeReview" => "1" 
				],
			];
            
            $allData = $this->commentModel->allByLimitPanel(1000, 0);
            $data['result'] = $allData;
            return view('review_view', $data);
            
		}

		return view('login_view');
	}

}