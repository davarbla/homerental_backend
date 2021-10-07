<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\RentModel;
use App\Models\LikedModel;

use App\Models\CategoryModel;
use App\Models\UserModel;
use App\Models\CommentModel;

class Rent extends BaseController
{
    private $postBody;
	private $authModel;
	private $sessLogin;
    private $likedModel;
    protected $categModel;

    private $rentModel;
    private $userModel;
    private $commentModel;

    private $fcmTopics = 'homerentaltopic';

	public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); 
		$this->sessLogin = session();

        $this->rentModel = new RentModel();
        $this->likedModel = new LikedModel();
        $this->userModel = new UserModel();

        $this->commentModel = new CommentModel();
        $this->categModel = new CategoryModel();
    }

	public function index()
	{
            
        $ac = $this->request->getVar('ac');

		$this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {

            $dataSession = $this->authModel->getDataSession($this->sessLogin);
            $data = [
				"menu" => [ 
					"activeRent" => "1" 
				],
			];

            $data['dataSess'] = $dataSession;
            $dataCateg = $this->categModel->allByLimit(50, 0);
            $data['category'] = $dataCateg;

            if ($ac == 'add') {
                $id = $this->request->getVar('id');
                $data['row'] = $this->rentModel->getById($id);
                
                return view('editrent_view', $data);
            }
            else if ($ac == 'edit') {
                $id = $this->request->getVar('id');
                $data['row'] = $this->rentModel->getById($id);
                
                return view('editrent_view', $data);
            }
            else {
                $allData = $this->rentModel->allByLimitPanel(1000, 0);
                $data['result'] = $allData;
                return view('rent_view', $data);
            }
		}

		return view('login_view');
	}

    public function get_byid()
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
        
        $idCateg = $this->postBody['ic'];
        $dataRent = $this->rentModel->allByLimitByIdCateg($idCateg, $this->postBody['lat'], $limit, $offset);

        $arr = $dataRent;

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

    public function get_review()
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

        $idUser = $this->postBody['iu'];
        $idRent = $this->postBody['ir'];
        
        $dataComment = $this->commentModel->allByLimitByIdRent($idRent, $limit, $offset);
        
        if (count($dataComment) < 1) {
            $json = array(
                "result" => $dataComment,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => $dataComment,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function like_dislike()
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

        $idUser = $this->postBody['iu'];
        $idRent = $this->postBody['ir'];
        
        if ($this->postBody['act'] == 'like') {
            $this->rentModel->do_like($this->postBody);
            $this->likedModel->do_liked_rent($this->postBody);
           
            $this->send_notif_rent($idUser, $idRent, true, '');

        }
        else if ($this->postBody['act'] == 'dislike') {
            $this->rentModel->do_dislike($this->postBody);
            $this->likedModel->do_liked_rent($this->postBody);
        }
        else if ($this->postBody['act'] == 'remove_all') {
           $array_ids = $this->postBody['ids'];
           if ($array_ids != '') {
            $this->likedModel->remove_all_liked($this->postBody);
           }
        }
        
        $dataRent = [$this->rentModel->getById($idUser)];
        
        if (count($dataRent) < 1) {
            $json = array(
                "result" => $dataRent,
                "code" => "201",
                "message" => "Data not found",
            );
        }
        else {
            $json = array(
                "result" => $dataRent,
                "code" => "200",
                "message" => "Success",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function comment()
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

        $dataComment = array(); 
        
        $idUser = $this->postBody['iu'];
        $idRent = $this->postBody['ir'];
        $desc = $this->postBody['ds'];

        $getUser = $this->userModel->getById($idUser);

        if ($idUser != '' && $idRent != '' && trim($desc) != '' && $getUser['id_user'] != '') {
            
            $this->commentModel->do_comment_rent($this->postBody);
            $dataComment = $this->commentModel->allByLimitByIdRent($idRent, $limit, $offset);
            
            $this->send_notif_rent($idUser, $idRent, false, $desc);


        }

        $arr = $dataComment; 
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

    public function add_update() {
        $title = $this->request->getVar('title');
        $desc = $this->request->getVar('description');
        
        $image = $this->request->getVar('image');
        $image2 = $this->request->getVar('image2');
        $image3 = $this->request->getVar('image3');
        $image4 = $this->request->getVar('image4');
        $image5 = $this->request->getVar('image5');
        $image6 = $this->request->getVar('image6');

        $address = $this->request->getVar('address');
        $price = $this->request->getVar('price');
        $rating = $this->request->getVar('address');
        $latitude = $this->request->getVar('latitude');

        $beds = $this->request->getVar('beds');
        $baths = $this->request->getVar('baths');
        $sqft = $this->request->getVar('sqft');

        $status = $this->request->getVar('status');
        $rcomd = $this->request->getVar('recomm');
        $id = $this->request->getVar('id'); 
        $categ = $this->request->getVar('category');  //category

        $this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {
            if ($title != '' && $desc != '' && $image != '') {
                $dataModel = [
                    'id_rent' => $id,
                    'id_category' => $categ,
                    'owner' => 'HR',
                    'title' => $title,
                    'description' => $desc, 
                    'image' => $image,
                    'image2' => $image2,
                    'image3' => $image3,
                    'image4' => $image4,
                    'image5' => $image5,
                    'image6' => $image6,
                    'address' => $address,
                    'latitude' => $latitude,
                    'rating'    => $rating,
                    'beds' => $beds,
                    'baths' => $baths,
                    'sqft' => $sqft,
                    'price' => $price,
                    'unit_price' => 'month',
                    "is_recommend" => ($status == 'on' || $status == '1') ? 1 : 0,
                    'status' => ($status == 'on' || $status == '1') ? 1 : 0,
                ];

                $this->rentModel->save($dataModel);
            }
        }

        return redirect()->to(base_url() . '/rent'); 
    }

    public function delete() {
        $this->sessLogin = session();	
		$check = $this->authModel->checkSession($this->sessLogin);
		if ($check) {
            $dataSession = $this->authModel->getDataSession($this->sessLogin);

            if ($dataSession['user']['flag'] == '99') {
                $id = $this->request->getVar('id');
                $this->rentModel->delete($id);
            }
        }

        return redirect()->to(base_url() . '/rent'); 
    }

    public function send_notif_rent($idUser, $idRent, $isLiked = true, $comment = '') {
        $singleRent = $this->rentModel->getById($idRent);
        $actionUser = $this->userModel->getById($idUser);
        
        $desc = $singleRent['description'];
        $image = $singleRent['image'];

        $titleNotif = $isLiked ? "Rent liked by " . $actionUser['fullname'] : "Rent commented by " . $actionUser['fullname'];
        $descNotif =  $comment != '' ?  $comment : $desc;


        $dataFcm = array(
            'title'   => $titleNotif,
            'body'    => $descNotif,
            "image"   => $image,
            'payload' => array(
                "keyname" => $isLiked ? 'liked_rent' : 'commentted_rent',
                "rent" => $singleRent,
                "image"   => $image
            ),
        );

        $this->userModel->sendFCMMessage('/topics/' . $this->fcmTopics, $dataFcm);
        
    }

    
}