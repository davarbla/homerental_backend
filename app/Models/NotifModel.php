<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifModel extends Model
{
    protected $table      = 'tb_notif';
    protected $primaryKey = 'id_notif';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'description', 'id_user', 'id_rent', 'is_from', 'flag', 'status', 'date_created', 'date_updated'];

    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    protected $skipValidation  = true;

    public function getTotal($flag='') {
        $sql = " SELECT count(id_notif) as total FROM tb_notif ";
        if ($flag != '') {
            $sql = " SELECT count(id_notif) as total FROM tb_notif WHERE flag='".$flag."'  ";
        }

        $query   = $this->query($sql);
        $results = $query->getResultArray();
        return $results;
    }

    public function allByLimitPanel($limit=100, $offset=0, $status='') {
        $getlimit = "$offset,$limit";
        $sql = " SELECT b.* FROM tb_notif b
        ORDER BY b.id_notif DESC LIMIT $getlimit ";

        if ($status != '') {
            $sql = " SELECT b.* FROM tb_notif b
            WHERE b.status='".$status."' 
            ORDER BY b.id_notif DESC LIMIT $getlimit ";
        }

        $query   = $this->query($sql);
        $result = $query->getResultArray();
        
        $arrNotifs = array();
        foreach ($result as $rowNotif) {
            $queryUser   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                WHERE b.id_install=c.id_install 
                AND b.id_user='".$rowNotif['id_user']."' LIMIT 1 ");
            $resultUser = $queryUser->getResultArray();
            $rowNotif['user'] =  $resultUser[0];

            $queryRent   = $this->query(" SELECT b.* FROM tb_rent b 
                WHERE b.id_rent='".$rowNotif['id_rent']."' ");
            $resultUser = $queryUser->getResultArray();
            $rowNotif['rent'] =  $resultUser[0];

            $arrNotifs[] = $rowNotif;
        }

        return $arrNotifs;
    }

    public function allByLimitByIdUser($idUser, $latitude='', $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        $sql = " SELECT b.* FROM tb_notif b
            WHERE b.status='".$status."' 
            AND b.id_user='".$idUser."'
            ORDER BY b.id_notif DESC LIMIT $getlimit ";
        
        //die($sql);

        $query   = $this->query($sql);
        $result = $query->getResultArray();
        
        $arrNotifs = array();
        foreach ($result as $rowNotif) {
            $queryUser   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                WHERE b.id_install=c.id_install 
                AND b.id_user='".$rowNotif['id_user']."' LIMIT 1 ");
            $resultUser = $queryUser->getResultArray();
            $rowNotif['user'] =  $resultUser[0];

            $queryRent   = $this->query(" SELECT b.* FROM tb_rent b 
                WHERE b.id_rent='".$rowNotif['id_rent']."' ");
            $resultUser = $queryUser->getResultArray();
            $rowRent = $resultUser[0];
            
            if ($latitude != '' && $rowRent['id_rent'] != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $rowRent['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $rowRent['distance'] = $distance;
            }
            
            $rowNotif['rent'] =  $rowRent;

            $arrNotifs[] = $rowNotif;
        }

        //print_r($arrNotifs);
        //die();

        return $arrNotifs;
    }

    public function getById($id) {
        return $this->where('id_notif', $id)
                    ->first();
    }

    public function update_byid($array) {
        $notif = $this->getById($array['id']);
        
        if ($notif['id_notif'] != '') {
            $array['id_notif'] = $array['id'];
            $this->save($array);
        }

        return $this->getById($array['id']);
    }

    public function saveUpdate($array) {

        $data = [
            'id_notif'          => $array['id'],
            'title'             => $array['tl'],
            'description'       => $array['ds'],
            'id_user'             => $array['iu'],
            'id_rent'       => $array['ir'],
            'flag'             => $array['fl'] ?? 1,
            'status'       => $array['st'] ?? 1
        ];

        $check = $this->getById($array['id']);

        if ($check['id_notif'] != '' && $check['id_notif'] != '0') { 
            $data['id_notif'] = $check['id_notif'];
        }
        $this->save($data);

        return $this->getById($array['id']);
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

/*
	id_notif, title, description, id_user, 
    id_rent, is_from, flag, status, date_created, date_updated
*/