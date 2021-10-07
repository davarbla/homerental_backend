<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\AuthHeaderModel;
use App\Models\RentModel;

use App\Models\UserModel;
use App\Models\LikedModel;
use App\Models\CommentModel;

use App\Models\TransModel;

use App\Models\NotifModel;

class Api extends BaseController
{
	protected $postBody; 
    protected $authModel;
    protected $categModel;
    protected $rentModel;

    protected $userModel;
    protected $likedModel;
    protected $commentModel;

    protected $transModel;
    protected $notifModel;

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); //Consider using CI's way of initialising models
        $this->categModel = new CategoryModel();
        $this->rentModel = new RentModel();

        $this->userModel = new UserModel();
        $this->likedModel = new LikedModel();
        $this->commentModel = new CommentModel();

        $this->transModel = new TransModel();
        $this->notifModel = new NotifModel();
    }


    public function index()
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
        
        //master category
        $dataCateg = $this->categModel->allByLimit($limit, $offset);

        //master rent
        $dataRent = $this->rentModel->allByLimit($this->postBody['lat'], $limit, $offset);

        //master rent
        $dataRentRecommend = $this->rentModel->allByLimitRecommended($this->postBody['lat'], $limit, $offset);

        //nearby rent
        $dataNearby = $this->rentModel->allByLimitNearby($this->postBody['lat'], $limit, $offset);

        //latest rent
        $dataLatest = $this->rentModel->allByLimitLatest($this->postBody['lat'], $limit, $offset);
        
        $idUser = $this->postBody['iu'];

        //myliked
        $dataMyLiked =  $this->likedModel->getAllByIdUserLatitude($this->postBody['lat'], $idUser, $limit, $offset);

        //user trans
        $dataTrans = $this->transModel->getByUserAll($idUser, $this->postBody['lat'], $limit, $offset);

        //mynotif
        $dataMyNotif =  $this->notifModel->allByLimitByIdUser($idUser, $this->postBody['lat'], $limit, $offset);
       
        //get all user 
        $dataUser = $this->userModel->allByLimit($limit, $offset);
 
        $results = array();
        $results['category'] = $dataCateg;
        $results['my_trans'] = $dataTrans;
        $results['recommended'] = $dataRentRecommend;   
        $results['latest'] = $dataLatest;    
        $results['rent'] = $dataRent;  
        $results['my_liked'] = $dataMyLiked;    
        $results['my_notif'] = $dataMyNotif;    
        $results['nearby'] = $dataNearby;  
        $results['all_user'] = $dataUser;  


        $json = array(
            "result" => $results,
            "code" => "200",
            "message" => "Success",
        );

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function send_notif_user($idUser, $title, $desc) {
        $actionUser = $this->userModel->getTokenById($idUser);
        $titleNotif = $title;
        $descNotif =  $desc;

        $dataFcm = array(
            'title'   => $titleNotif,
            'body'    => $descNotif,
            "image"   => $image,
            'payload' => array(
                "keyname" => 'user_act',
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
            "ir"    => 0
        );

        $this->notifModel->saveUpdate($dataNotif);
        
    }

    public function send_fcm() {
        $this->postBody = $this->authModel->authHeader($this->request);
         //test send fcm message
         $results = array();
         if ($this->postBody['token'] != '') {
            $results = $this->userModel->sendFCMMessage($this->postBody['token'], $this->postBody['data']);
        }

        $arr = $results;

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Required paramater",
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

    public function users()
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

        $arr = array();
        
        $idUser = $this->postBody['iu'];
        if ($idUser != '') {
            $getUserById = $this->userModel->getById($idUser);
            if ($getUserById['id_user'] != '') {
                $arr = [$getUserById];
            }
        }
        else {
            $arr = $this->userModel->allByLimit($limit, $offset);
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

    
    public function get_user()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        if ($this->postBody['is'] != '' &&  $this->postBody['iu'] != '') {
            $this->postBody['id'] = $this->postBody['iu'];
            $this->userModel->updateUser($this->postBody);
        }
        
        $dataUser = $this->userModel->getById($this->postBody['iu']);

        if ($dataUser['id_user'] == '') {
            $json = array(
                "result" => array(),
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $arr = array();
            $arr['user'] = $dataUser;

            $offset = 0;
            $limit = 10;

            $getLimit = $this->request->getVar('lt');
            if ($getLimit != '') {
                $exp = explode(",", $getLimit);
                $offset = (int) $exp[0];
                $limit = (int) $exp[1];
                
            }

            //myliked
            $dataMyLiked =  $this->likedModel->getAllByIdUserLatitude($this->postBody['lat'], $dataUser['id_user'], $limit, $offset);
            $arr['my_liked'] = $dataMyLiked;

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

    public function update_user_byid()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $arr = array();
        $idUser = $this->postBody['iu'];

        $message = "Data not found";

        if ($idUser != '') {
            
            $dataUser = $this->userModel->getById($idUser);
            $arr = [$dataUser]; 
            
            $action = $this->postBody['act'];
            if ($action == 'update_about' && $this->postBody['ab'] != '') {
                $data = [
                    'id_user'     => $idUser,
                    'about'  => $this->postBody['ab'],
                    'latitude'  => $this->postBody['lat'],
                    'location'  => $this->postBody['loc'],
                ];
                $this->userModel->save($data);
            }
            else if ($action == 'update_phone' && $this->postBody['ph'] != '') {
                $data = [
                    'id_user'     => $idUser,
                    'phone'  => $this->postBody['ph'],
                    'latitude'  => $this->postBody['lat'],
                    'location'  => $this->postBody['loc'],
                ];
                $this->userModel->save($data);
            }
            else if ($action == 'update_about_fullname' && $this->postBody['ab'] != '' && $this->postBody['fn'] != '') {
                $data = [
                    'id_user'     => $idUser,
                    'fullname'  => $this->postBody['fn'],
                    'about'  => $this->postBody['ab'],
                    'latitude'  => $this->postBody['lat'],
                    'location'  => $this->postBody['loc'],
                ];
                $this->userModel->save($data);
            }
            else if ($action == 'update_location' && $this->postBody['lat'] != '') {
                $data = [
                    'id_user'     => $idUser,
                    'location'  => $this->postBody['loc'],
                    'latitude'  => $this->postBody['lat'],
                    'location'  => $this->postBody['loc'],
                ];
                $this->userModel->save($data);
            } 
            else if ($action == 'change_password') {
                if ($this->postBody['ps'] != '' && $this->postBody['np'] != '') {
                    $oldpasswrd = $this->generatePassword($this->postBody['ps']);
                    $newpasswrd = $this->generatePassword($this->postBody['np']);
                    if ($oldpasswrd == $dataUser['password_user']) {
                        $data = [
                            'id_user'     => $idUser,
                            'password_real' => $this->postBody['np'],
                            'password_user' => $newpasswrd,
                            'location'  => $this->postBody['loc'],
                            'latitude'  => $this->postBody['lat'],
                            'location'  => $this->postBody['loc'],
                        ];
                        $this->userModel->save($data);
                    }
                    else {
                        $arr = array();
                        $message = "Old Password invalid...";
                    }
                }
                else {
                    $arr = array();
                    $message = "Data parameter required...";
                }
            }

            
        }
        
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => $message,
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

    public function category()
    {
        //$authModel = new AuthHeaderModel();
        $this->postBody = $this->authModel->authHeader($this->request);
        
        //print_r($this->postBody);
        //die();
        // limit 0, 4 ==> limit 4, offset 0
        // limit 4, 8 ===> limit 4, offset 4,

        $offset = 0;
        $limit = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $offset = (int) $exp[0];
            $limit = (int) $exp[1];
            
        }
        
        $dataCateg = $this->categModel->allByLimit($limit, $offset);
        
        $arr = $dataCateg; //array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 5, 'date' => date('Y-m-d H:i:s'),);
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Data no found",
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

    public function login()
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
        
        //123456    cfc5902918296762903710e9c9a65580
        $passwrd = $this->generatePassword($this->postBody['ps']);
        $dataUser = $this->userModel->loginByEmail($this->postBody['em'], $passwrd);

        if ($this->postBody['is'] != '' && $dataUser['id_user'] != '') {
            $this->postBody['id'] = $dataUser['id_user'];
            $this->userModel->updateUser($this->postBody);
        }
        
        $arr = $dataUser; 
        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Email/Username & Password invalid",
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

    public function register()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['ps'] != '' && $this->postBody['em'] != '') {
            
            $checkExist = $this->userModel->getByEmail($this->postBody['em']);
            

            if ($checkExist['id_user'] == '') {
                $this->postBody['rp'] = $this->postBody['ps'];
                $this->postBody['ps'] = $this->generatePassword($this->postBody['ps']);
                
                $dataUser = $this->userModel->register($this->postBody);
                $arr = [$dataUser]; 

                //send notif user
                $idUser = $dataUser['id_user'];
                $title = 'Welcome to HomeRental!';
                $desc = "Hi, " . $this->postBody['fn']
                    ."\nYou are signin up with Registered Email : " . $this->postBody['em'] . " success."
                    ."\nPassword : " . $this->postBody['rp'] 
                    ."\n\nCongratulations and best wishes for your next holiday. HomeRental - Find Your Perfect Holiday with Us. Thank you.";

                $this->send_notif_user($idUser, $title, $desc);
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Email/Username already exist",
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

    public function registerPhone()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['ps'] != '' && $this->postBody['ph'] != '') {
            $checkExist = $this->userModel->getByPhone($this->postBody['ph']);
            
            if ($checkExist['id_user'] == '') {
                $this->postBody['ps'] = $this->generatePassword($this->postBody['ps']);
                $dataUser = $this->userModel->registerByPhone($this->postBody);
                
                $arr = [$dataUser]; 
            }
        }

        if (count($arr) < 1) {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Phone number already exist",
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

    public function checkEmailPhone() {
        $this->postBody = $this->authModel->authHeader($this->request);
        $arr = array();

        if ($this->postBody['em'] == '' && $this->postBody['ph'] == '') {
            
        }
        else {
            $checkExist = null;
            if ($this->postBody['ph'] != '') {
                $checkExist = $this->userModel->getByPhone($this->postBody['ph']);
            }
            else if ($this->postBody['em'] != '') {
                $checkExist = $this->userModel->getByEmail($this->postBody['em']);
            }

            if ($checkExist != null) {
                $arr = [$checkExist]; 
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

    public function hash_password() {
        $this->postBody = $this->authModel->authHeader($this->request);
        if ($this->postBody['ps'] != '') {
            print_r($this->generatePassword($this->postBody['ps']));
        }
        else {
            $json = array(
                "result" => $arr,
                "code" => "201",
                "message" => "Required parameter",
            );

            header('Content-Type: application/json');
            echo json_encode($json);
            die();
        }
    }

    private function generatePassword($password) {
        return md5(sha1(hash("sha256", $password)));
    }
}