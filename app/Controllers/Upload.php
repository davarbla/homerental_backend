<?php

namespace App\Controllers;

use App\Models\AuthHeaderModel;
use App\Models\UserModel;
use App\Models\RentModel;

use App\Models\CategoryModel;

class Upload extends BaseController
{
	protected $postBody; 
    protected $authModel;
    protected $rentModel;

    protected $userModel;
    protected $categModel;

    private   $URL_BASE = 'https://homerental.fboys.app/';
    private   $PATH = '/home/u439050121/domains/fboys.app/public_html/homerental/upload/'; // echo getcwd() php script
    private   $TOPIC_FCM = '/topics/homerentaltopic';

    public function __construct()
    {
        $this->authModel = new AuthHeaderModel(); 
        $this->rentModel = new RentModel();
        $this->userModel = new UserModel();

        $this->categModel = new CategoryModel();
    }

    public function index()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        $limit = 0;
        $offset = 10;

        $getLimit = $this->request->getVar('lt');
        if ($getLimit != '') {
            $exp = explode(",", $getLimit);
            $limit = (int) $exp[1];
            $offset = (int) $exp[0];
        }
        
        //master user
        $dataUser = $this->userModel->allByLimit($limit, $offset);
        
        $json = array(
            "result" => $dataUser ,
            "code" => "200",
            "message" => "Success",
        );

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function upload_rent()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        $dataRent = array();
        if ($this->postBody['img'] != '' && $this->postBody['iu'] != '' && $this->postBody['ic'] != '' && $this->postBody['ds'] != '') {
            $dataRent = $this->rentModel->saveUpdate($this->postBody);
        }

        if (count($dataRent)>0) {

            $idUser = $this->postBody['iu'];
            $dataUser = $this->userModel->getById($idUser);
            // update total_rent user + 1
            $data = [
                'id_user'     => $idUser,
                'total_rent'  => $dataUser['total_rent'] + 1,
            ];
            $this->userModel->save($data);
            
            $idCateg = $this->postBody['ic'];
            $dataCateg = $this->categModel->getById($idCateg);
            
            // update total_rent category + 1
            $data2 = [
                'id_category'     => $idCateg,
                'total_rent'  => $dataCateg['total_rent'] + 1
            ];
            $this->categModel->save($data2);

            //send notif fcm to topics
            //check file theme.dart  var fcmTopicName
            $dataTokenFCM = $this->userModel->getTokenById($dataRent[0]['id_user']);

            $desc = $this->postBody['ds'];
            $image = $this->postBody['img'];
            $dataFcm = array(
                'title'   => "New Rent by " . $dataUser['fullname'],
                'body'    => $desc . "\n#" . $dataCateg['title'],
                "image"   => $image,
                'payload' => array(
                    "keyname" => 'new_rent',
                    "rent" => $dataRent[0],
                    "image"   => $image
                ),
            );
            $this->userModel->sendFCMMessage($dataTokenFCM['token_fcm'], $dataFcm);
            //send notif fcm to topics

            $json = array(
                "result" => $dataRent,
                "code" => "200",
                "message" => "Success",
            );
        }
        else {
            $json = array(
                "result" => $dataUser ,
                "code" => "208",
                "message" => "Data required parameter",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function upload_image_rent()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        $filename = $this->postBody['filename'];
        $baseEncodeImage = $this->postBody['image'];
        
        $id = $this->postBody['id'];
        $dataUser = $this->userModel->getById($id);
        
        $binary = base64_decode($baseEncodeImage);
        $namefile = $filename;
        $ext = pathinfo($namefile, PATHINFO_EXTENSION);
        

        if ($namefile != '') {
            $target_dir = $this->PATH;
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $url_path = $this->URL_BASE . "upload/";
            

            $target_path = $target_dir;
            $now = date('YmdHis');
            $rand = rand(1111, 9999);
            $generatefile = $id . "_" . $now . "_" .$rand;
            $namefile = $generatefile . "." . $ext;

            $target_path = $target_path . $namefile;
            
            
            $fh = fopen($target_path, 'w') or die("can't open file " . getcwd());
            chmod($target_path, 0777);
            fwrite($fh, $binary);
            fclose($fh);

            sleep(1);

            $foto = $url_path . $namefile;
            
            $json = array(
                "result" => array("file" => $foto),
                "code" => "200",
                "file" => $foto,
                "message" => "Upload share successful..."
            );
        }
        else {

            $json = array(
                "result" => array(),
                "code" => "209",
                "message" => "Upload failed",
            );
        }
        
        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function upload_image_user()
    {
        $this->postBody = $this->authModel->authHeader($this->request);

        $filename = $this->postBody['filename'];
        $baseEncodeImage = $this->postBody['image'];
        
        $id = $this->postBody['id'];
        $dataUser = $this->userModel->getById($id);
        
        $binary = base64_decode($baseEncodeImage);
        $namefile = $filename;
        $ext = pathinfo($namefile, PATHINFO_EXTENSION);
        
        if ($namefile != '') {
            $target_dir = $this->PATH;
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $url_path = $this->URL_BASE . "upload/";

            $target_path = $target_dir;
            $now = date('YmdHis');
            $rand = rand(1111, 9999);
            $generatefile = $id . "_photo_" . $now . "_" .$rand;
            $namefile = $generatefile . "." . $ext;
            $target_path = $target_path . $namefile;
            
            $fh = fopen($target_path, 'w') or die("can't open file " . getcwd());
            chmod($target_path, 0777);
            fwrite($fh, $binary);
            fclose($fh);

            sleep(1);
            $foto = $url_path . $namefile;

            //update photo member
            $dataUpdate = [
                "id_user" => $id,
                "image"   => $foto,
                "date_updated" => date('YmdHis'),
            ];

            $this->userModel->save($dataUpdate);
            
            $json = array(
                "result" => array("file" => $foto),
                "code" => "200",
                "file" => $foto,
                "message" => "Upload share successful..."
            );
        }
        else {

            $json = array(
                "result" => array(),
                "code" => "209",
                "message" => "Upload failed",
            );
        }
        
        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

    public function delete_file()
    {
        $this->postBody = $this->authModel->authHeader($this->request);
        
        if ( $this->postBody['fl'] != '') {
            $file_path = $this->PATH . $this->postBody['fl'];
            unlink($file_path);
            $json = array(
                "result" => $file_path ,
                "code" => "200",
                "message" => "File $file_path Deleted",
            );
        }
        else {
            $json = array(
                "result" => $file_path ,
                "code" => "208",
                "message" => "Error File Error Unlink cannot be deleted due to an error",
            );
        }

        //add the header here
        header('Content-Type: application/json');
        echo json_encode($json);
        die();
    }

}