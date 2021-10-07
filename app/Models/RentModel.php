<?php

namespace App\Models;

use CodeIgniter\Model;

class RentModel extends Model
{
    protected $table      = 'tb_rent';
    protected $primaryKey = 'id_rent';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['id_category', 'owner', 'title', 'description', 'image', 'image2', 'image3', 'image4', 'image5', 'image6', 
        'address', 'currency', 'price', 'unit_price', 'beds', 'baths', 'sqft', 'rating', 'total_rating', 'total_rent', 'total_liked', 'latitude', 'country', 
        'is_recommend', 'is_deleted', 'flag', 'status', 'date_created', 'date_updated'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    protected $skipValidation     = true;

    public function getTotal() {
        $query   = $this->query(" SELECT count(id_rent) as total FROM tb_rent ");
        $results = $query->getResultArray();
        return $results;
    }

    public function allByLimit($latitude, $limit=100, $offset=0, $status=1, $idDeleted=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            WHERE a.status='".$status."' 
            AND a.is_deleted='".$idDeleted."' 
            ORDER BY a.total_liked DESC, a.title ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        foreach ($results as $row) {
            
            //counting distance
            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $row['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $row['distance'] = $distance;
            }
            
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function allByLimitRecommended($latitude, $limit=100, $offset=0, $status=1, $idDeleted=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            WHERE a.status='".$status."' 
            AND a.is_deleted='".$idDeleted."' 
            AND a.is_recommend=1 
            ORDER BY a.total_liked DESC, a.title ASC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        foreach ($results as $row) {
            
            //counting distance
            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $row['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $row['distance'] = $distance;
            }
            
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function allByLimitLatest($latitude, $limit=100, $offset=0, $status=1, $idDeleted=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            WHERE a.status='".$status."' 
            AND a.is_deleted='".$idDeleted."' 
            ORDER BY a.id_rent DESC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        foreach ($results as $row) {
            
            //counting distance
            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $row['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $row['distance'] = $distance;
            }
            
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function allByLimitNearby($latitude, $limit=100, $offset=0, $status=1, $idDeleted=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            WHERE a.status='".$status."' 
            AND a.is_deleted='".$idDeleted."' 
            ORDER BY a.latitude DESC 
            LIMIT ".$getlimit." ");

            $results = $query->getResultArray();
            $return_array = array();
    
            foreach ($results as $row) {
                
                //counting distance
                if ($latitude != '') {
                    $getLatitude = explode(",", $latitude);
                    $rentLatitude = explode(",", $row['latitude']);

                    $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                    $row['distance'] = $distance;
                }
                
                $return_array[] = $row;
            }
            
            return $return_array;
    }

    public function allByLimitByIdCateg($idCateg, $latitude, $limit=100, $offset=0, $status=1, $idDeleted=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            WHERE a.status='".$status."' 
            AND a.is_deleted='".$idDeleted."' 
            AND a.id_category='".$idCateg."'
            ORDER BY a.id_rent DESC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        foreach ($results as $row) {
            
            //counting distance
            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $row['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $row['distance'] = $distance;
            }
            
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.* FROM tb_rent a 
            ORDER BY a.id_rent DESC 
            LIMIT ".$getlimit." ");

        $results = $query->getResultArray();
        $return_array = array();

        $i = 0;
        foreach ($results as $row) {
            
            $return_array[] = $row;
        }
        
        return $return_array;
    }

    public function getById($id) {
        return $this->where('id_rent', $id)
                    ->first();
    }

    public function do_like($array) {

        if ($array['iu']!='' && $array['ir'] != '') {
            $idUser = $array['iu'];
            $idRent = $array['ir'];

            $dataRent = $this->getById($idRent);

            $data = [
                'id_rent'       => $idRent,
                'total_liked'    => $dataRent['total_liked'] + 1,
            ];

            $this->save($data);

            //update user
            $sql = " UPDATE tb_user SET total_like=total_like+1 WHERE id_user='".$idUser."' ";
            $this->query($sql);

            //update user
            $sql = " UPDATE tb_category SET total_like=total_like+1 WHERE id_category='".$dataRent['id_category']."' ";
            $this->query($sql);

            $dataRent = $this->getById($idRent);
            return [$dataRent];
        }

        return [];
    }

    public function do_dislike($array) {

        if ($array['iu']!='' && $array['ir'] != '') {
            $idUser = $array['iu'];
            $idRent = $array['ir'];

            $dataRent = $this->getById($idRent);

            $data = [
                'id_rent'       => $idRent,
                'total_liked'    => $dataRent['total_liked'] - 1,
            ];

            $this->save($data);

            //update user
            $sql = " UPDATE tb_user SET total_like=total_like-1 WHERE id_user='".$idUser."' ";
            $this->query($sql);

            $dataRent = $this->getById($idRent);
            return [$dataRent];
        }

        return [];
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