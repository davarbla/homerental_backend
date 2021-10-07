<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\TransModel;
use App\Models\UserModel;
use App\Models\RentModel;
use App\Models\NotifModel;

class Trans extends BaseController
{

	private $authModel;
	private $sessLogin;

    private $transModel;
    private $userModel;
    private $rentModel;

    private $notifModel;

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
		$this->sessLogin = session();

        $this->transModel = new TransModel();
        $this->userModel = new UserModel();
        $this->rentModel = new RentModel();

        $this->notifModel = new NotifModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $data = [
				"menu" => [ 
					"activeTrans" => "1" 
				],
			];

            
            $allData = $this->transModel->allByLimitPanel(1000, 0, 1, '');
            $data['result'] = $allData;
            return view('trans_view', $data);
		}

		return view('login_view');
	}

    public function update_trans() {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['iu'] != '' && $this->postBody['it'] != '') {
            $dataTrans = $this->transModel->getById($this->postBody['it']);
            
            if ($dataTrans['id_trans'] != '') {

                if ($this->postBody['act'] == 'delete') {
                    $data = [
                        "no" => $dataTrans['no_trans'],
                        "is_deleted" => 1
                    ];
                    $this->transModel->update_byno($data);
                }
                else {
                    // status 2 = checkin, 3 = done, 4 = void/cancel
                    $data = [
                        "no" => $dataTrans['no_trans'],
                        "status" => $this->postBody['st']
                    ];
                    
                    $idRent = $dataTrans['id_rent'];
                    $duration = $dataTrans['duration'] . " (" . $dataTrans['payment'] . ")";
                    $this->transModel->update_byno($data);

                    $dataTrans = $this->transModel->getById($this->postBody['it']);
                    $arr = [$dataTrans];
                    
                    // send notif to user
                    $this->send_notif_trans($this->postBody['iu'], $idRent, $duration, true, $this->postBody['st']);
                }
            }
        }
        
        
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

    public function insert_trans() {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['iu'] != '' && $this->postBody['ir'] != '') {
            $dataTrans = $this->transModel->save_update($this->postBody);
            
            if ($dataTrans['id_trans'] != '') {
                
                $arr = [$dataTrans];
                
                $duration = $dataTrans['duration'] . " (" . $dataTrans['payment'] . ")";
                // send notif to user
                $this->send_notif_trans($this->postBody['iu'], $this->postBody['ir'], $duration, false, '1');    
            }
        }
        
        
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

    public function send_notif_trans($idUser, $idRent, $duration, $isUpdate = false, $status='1') {
        $singleRent = $this->rentModel->getById($idRent);
        $actionUser = $this->userModel->getTokenById($idUser);
        
        $desc = $singleRent['description'];
        $image = $singleRent['image'];

        $address = $singleRent['address'];
        $price = $singleRent['currency']. "." .$singleRent['price']." /".$singleRent['unit_price'];

        $titleNotif = "Book Rent at " . $singleRent['title'];
        if ($isUpdate) {
            if ($status == '2') {
                $titleNotif = "Checkin Rent at " . $singleRent['title'];
            }
            else if ($status == '3') {
                $titleNotif = "Finished Rent at " . $singleRent['title'];
            }
            else if ($status == '4') {
                $titleNotif = "Cancel Rent at " . $singleRent['title'];
            }
        }
        
        $descNotif =  "Duration: " . $duration .
            "\nAddress: " . $address .
            "\nPrice: " . $price .
            "\n\nHomeRent: " . $singleRent['title'] ."\n" . $desc;


        $dataFcm = array(
            'title'   => $titleNotif,
            'body'    => $descNotif,
            "image"   => $image,
            'payload' => array(
                "keyname" => 'book_rent',
                "rent" => $singleRent,
                "image"   => $image
            ),
        );

        $this->userModel->sendFCMMessage($actionUser['token_fcm'], $dataFcm);

        // insert notif user
        $dataNotif = array(
            "tl" => $titleNotif,
            "ds" => $descNotif,
            "iu"    => $idUser,
            "ir"    => $idRent ?? 0
        );

        $this->notifModel->saveUpdate($dataNotif);
        
    }

}