<?php

namespace App\Models;

use CodeIgniter\Model;

class LikedModel extends Model
{
    protected $table      = 'tb_liked';
    protected $primaryKey = 'id_liked';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['id_category', 'id_rent', 'id_user', 'is_liked', 'flag', 'status', 'date_created', 'date_updated'];
    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    
    protected $skipValidation     = true;

    public function allByLimit($limit=100, $offset=0, $status=1) {
        return $this->where('status', $status)
                    ->orderBy('date_created','desc')
                    ->findAll($limit, $offset);
    }

    public function allByLimitByIdUser($idUser, $limit=100, $offset=0, $status=1) {
        return $this->where('status', $status)
                    ->where('id_user', $idUser)
                    ->orderBy('date_created','desc')
                    ->findAll($limit, $offset);
    }

    public function allByLimitByIdCateg($idCateg, $limit=100, $offset=0, $status=1) {
        return $this->where('status', $status)
                    ->where('id_category', $idCateg)
                    ->orderBy('date_created','desc')
                    ->findAll($limit, $offset);
    }

    public function allByLimitByIdRent($idRent, $limit=100, $offset=0, $status=1) {
        return $this->where('status', $status)
                    ->where('id_rent', $idRent)
                    ->orderBy('date_created','desc')
                    ->findAll($limit, $offset);
    }

    public function getAllByIdUser($idUser, $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        $sql = " SELECT a.* FROM tb_rent a, tb_liked b 
            WHERE a.id_rent=b.id_rent
            AND b.flag=1 AND b.status=1 
            AND b.is_liked=1 
            AND a.status='".$status."' 
            AND b.id_user='".$idUser."'
            ORDER BY b.date_updated ASC 
            LIMIT ".$getlimit." ";

        //print_r($sql);
        //die();
        
        $query   = $this->query($sql);


        $results = $query->getResultArray();
        
        $return_array = array();
        $i = 0;
        foreach ($results as $row) {
            $query1   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c WHERE b.id_install=c.id_install 
                AND b.id_user='".$idUser."' ");
            $result1 = $query1->getResultArray();
            $row['user'] =  $result1[0];

            
            $query0   = $this->query(" SELECT a.is_liked FROM tb_liked a WHERE a.id_rent='".$row['id_rent']."'  
                AND a.id_user='".$idUser."' ");
            $result0 = $query0->getResultArray();
            $row['is_liked'] =  $result0[0]['is_liked'];

            
            //get comment by idpost
            $query3   = $this->query(" SELECT b.* FROM tb_comment b
            WHERE b.status='".$status."' 
            AND b.id_rent='".$row['id_rent']."' ORDER BY b.id_comment DESC LIMIT 0,10 ");
            $result3 = $query3->getResultArray();
            
            $arrComments = array();
            foreach ($result3 as $rowComm) {
                $queryUser   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                    WHERE b.id_install=c.id_install 
                    AND b.id_user='".$rowComm['id_user']."' ");
                $resultUser = $queryUser->getResultArray();
                $rowComm['user'] =  $resultUser[0];
                $arrComments[] = $rowComm;
            }
            $row['comments'] = $arrComments;

            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function getAllByIdUserLatitude($latitude, $idUser, $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        $sql = " SELECT a.* FROM tb_rent a, tb_liked b 
            WHERE a.id_rent=b.id_rent
            AND b.flag=1 AND b.status=1 
            AND b.is_liked=1 
            AND a.status='".$status."' 
            AND b.id_user='".$idUser."'
            ORDER BY b.date_updated DESC 
            LIMIT ".$getlimit." ";

        //print_r($sql);
        //die();
        
        $query   = $this->query($sql);
        $results = $query->getResultArray();
        
        $return_array = array();
        $i = 0;
        foreach ($results as $row) {

            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $row['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $row['distance'] = $distance;
            }
            
            $query1   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c WHERE b.id_install=c.id_install 
                AND b.id_user='".$idUser."' ");
            $result1 = $query1->getResultArray();
            $row['user'] =  $result1[0];

            
            $query0   = $this->query(" SELECT a.is_liked FROM tb_liked a WHERE a.id_rent='".$row['id_rent']."'  
                AND a.id_user='".$idUser."' ");
            $result0 = $query0->getResultArray();
            $row['is_liked'] =  $result0[0]['is_liked'];

            
            //get comment by idpost
            $query3   = $this->query(" SELECT b.* FROM tb_comment b
            WHERE b.status='".$status."' 
            AND b.id_rent='".$row['id_rent']."' ORDER BY b.id_comment DESC LIMIT 0,10 ");
            $result3 = $query3->getResultArray();
            
            $arrComments = array();
            foreach ($result3 as $rowComm) {
                $queryUser   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                    WHERE b.id_install=c.id_install 
                    AND b.id_user='".$rowComm['id_user']."' ");
                $resultUser = $queryUser->getResultArray();
                $rowComm['user'] =  $resultUser[0];
                $arrComments[] = $rowComm;
            }
            $row['comments'] = $arrComments;

            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function getAllByIdCateg($idCateg, $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        
        $sql = " SELECT a.* FROM tb_rent a, tb_liked b 
        WHERE a.id_category=b.id_category
        AND b.flag=2 AND b.status=1  
        AND b.is_liked=1 
        AND a.status='".$status."' 
        AND a.id_category='".$idCateg."'
        ORDER BY a.id_rent DESC, a.total_like DESC, a.total_rating DESC, a.title ASC 
        LIMIT ".$getlimit." ";

        $query   = $this->query($sql);


        $results = $query->getResultArray();
        $return_array = array();
        $i = 0;
        foreach ($results as $row) {
            $query1   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c WHERE b.id_install=c.id_install 
                AND  b.id_user='".$row['id_user']."' ");
            $result1 = $query1->getResultArray();
            $row['user'] =  $result1[0];

            $query0   = $this->query(" SELECT a.is_liked FROM tb_liked a WHERE a.id_rent='".$row['id_rent']."'  
                AND a.id_user='".$row['id_user']."' ");
            $result0 = $query0->getResultArray();
            $row['is_liked'] =  $result0[0]['is_liked'];
            
            //get other user post
            $query2   = $this->query(" SELECT b.* FROM tb_rent a, tb_user b
            WHERE a.id_user=b.id_user
            AND a.status='".$status."' 
            AND a.id_user != '".$row['id_user']."'
            AND a.id_category='".$idCateg."'
            AND b.status=1 ");
            $result2 = $query2->getResultArray();
            $row['other_users'] =  $result2;
            
           //get comment by idpost
           $query3   = $this->query(" SELECT b.* FROM tb_comment b
           WHERE b.status='".$status."' 
           AND b.id_rent='".$row['id_rent']."' ORDER BY b.id_comment DESC LIMIT 0,10 ");
           $result3 = $query3->getResultArray();
           
           $arrComments = array();
            foreach ($result3 as $rowComm) {
                $queryUser   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                    WHERE b.id_install=c.id_install 
                    AND b.id_user='".$rowComm['id_user']."' ");
                $resultUser = $queryUser->getResultArray();
                $rowComm['user'] =  $resultUser[0];
                $arrComments[] = $rowComm;
            }
            $row['comments'] = $arrComments;

            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function do_liked_rent($array) {
       
         if ($array['iu'] !='' && $array['ir'] != '') {
            
            $idUser = $array['iu'];
            $idRent = $array['ir'];

            $data = [
                'id_user'       => $idUser,
                'id_rent'       => $idRent,
                'is_liked'      => 1,
                'flag'          => 1,
            ];

            $idLiked = '';
            $check1 = $this->getByIdUserRent($idUser, $idRent);
            if ($check1['id_liked'] != '') {
                $idLiked = $check1['id_liked'];

                $data['id_liked'] = $idLiked;
                $data['is_liked'] = $check1['is_liked'] == '1' ?  0 : 1;

            }
            //print_r($data);die();
            $this->save($data);

        }

        return $this->getByIdUserRent($idUser, $idRent);
    }

    public function remove_all_liked($array) {
        // like or dislike category
        if ($array['iu']!='' && $array['ids'] != '') {
            $idUser = $array['iu'];
            $array_ids = $array['ids'];

            foreach ($array_ids as $id) {
                $idRent = $id;
                
                // total_liked - 1 per id rent
                $this->query(" UPDATE tb_rent SET total_liked=total_liked-1 WHERE id_rent='".$idRent."' ");
                
                // total_like - 1 per id user
                $this->query(" UPDATE tb_user SET total_like=total_like-1 WHERE id_user='".$idUser."' ");

                // is like tb liked
                $this->query(" UPDATE tb_liked SET is_liked=0 WHERE id_user='".$idUser."' AND id_rent='".$idRent."' ");
            }

        }

        return $this->getByIdUserRent($idUser, $idRent);
    }

    public function getById($id) {
        return $this->where('id_liked', $id)
                    ->first();
    }

    public function getByIdUserRent($idUser, $idRent) {
        return $this->where('id_user', $idUser)
                    ->where('id_rent', $idRent)
                    ->first();
    }

    public function getByIdUserCateg($idUser, $idCateg) {
        return $this->where('id_user', $idUser)
                    ->where('id_category', $idCateg)
                    ->where('flag', '2')
                    ->first();
    }

    public function distance($lat1, $lon1, $lat2, $lon2) { 
        
        $pi80 = M_PI / 180; 
        $lat1 *= $pi80; 
        $lon1 *= $pi80; 
        $lat2 *= $pi80; 
        $lon2 *= $pi80; 
        $r = 6372.797; // mean radius of Earth in km 
        $dlat = $lat2 - $lat1; 
        $dlon = $lon2 - $lon1; 
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
        $km = $r * $c; 
        //echo ' '.$km; 
        return $km; 
    }
    
}