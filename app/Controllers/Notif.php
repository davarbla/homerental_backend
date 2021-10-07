<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\NotifModel;
use App\Models\UserModel;
use App\Models\RentModel;

class Notif extends BaseController
{

	private $authModel;
	private $sessLogin;

    private $notifModel;
    private $userModel;
    private $rentModel;

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

        $this->notifModel = new NotifModel();
        $this->userModel = new UserModel();
        $this->rentModel = new RentModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeNotif" => "1" 
				],
			];
            
            $allData = $this->notifModel->allByLimitPanel(1000, 0);
            $data['result'] = $allData;
            return view('notif_view', $data);
            
		}

		return view('login_view');
	}

    public function save_update()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $offset = 0;
        $limit = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $offset = (int) $exp[0];
            $limit = (int) $exp[1];
            
        }

        $dataNotif = array(); 
        
        $idUser = $this->postBody['iu'];
        $idRent = $this->postBody['ir'];
        $desc = $this->postBody['ds'];

        $getUser = $this->userModel->getById($idUser);
        $getRent = $this->rentModel->getById($idRent);

        if ($idUser != '' && $idRent != '' && trim($desc) != '' && $getUser['id_user'] != '' && $getRent['id_rent'] != '') {
            
            $this->notifModel->saveUpdate($this->postBody);
            $dataNotif = $this->notifModel->allByLimitByIdUser($idUser, $this->postBody['lat'], $limit, $offset);


        }

        $arr = $dataNotif; 
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

    public function update()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $offset = 0;
        $limit = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $offset = (int) $exp[0];
            $limit = (int) $exp[1];
            
        }

        $dataNotif = array(); 
        
        $idNotif = $this->postBody['id'];
        $idUser = $this->postBody['iu'];
        
        if ($idNotif != '' && $idUser != '') {
            
            $this->notifModel->update_byid($this->postBody);
            $dataNotif = $this->notifModel->allByLimitByIdUser($idUser, $this->postBody['lat'], $limit, $offset);
        }

        $arr = $dataNotif; 
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