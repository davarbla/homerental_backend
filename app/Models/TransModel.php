<?php

namespace App\Models;

use CodeIgniter\Model;

class TransModel extends Model
{
    protected $table      = 'tb_trans';
    protected $primaryKey = 'id_trans';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['no_trans', 'duration', 'id_user', 'id_rent', 'currency', 'total', 'unit_price', 'payment', 
    'desc_payment', 'is_deleted', 'flag', 'status', 'date_created', 'date_updated'];

    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    
    protected $skipValidation     = true;

    public function getTotal($status=1) {
        $query   = $this->query(" SELECT count(id_trans) as count, sum(total) as total FROM tb_trans WHERE status != '4' ");
        $results = $query->getResultArray();
        return $results;
    }

    public function getByUserAll($idUser='', $latitude='', $limit=100, $offset=0, $flag=1, $deleted=0) {
        $getlimit = "$offset,$limit";

        $sql = " SELECT a.*
            FROM tb_trans a
            WHERE a.is_deleted='".$deleted."'
            AND a.flag='".$flag."'
            ORDER BY a.id_trans DESC 
            LIMIT ".$getlimit." ";

        if ($idUser != '') {
            $sql = " SELECT a.*
            FROM tb_trans a
            WHERE a.is_deleted='".$deleted."'
            AND a.flag='".$flag."'
            AND a.id_user='".$idUser."'
            ORDER BY a.id_trans DESC 
            LIMIT ".$getlimit." ";
        }  
        
        $query  = $this->query($sql);
        $datas = $query->getResultArray();

        $return_array = array();
        foreach ($datas as $row) {
            $queryUser  = $this->query("SELECT * FROM tb_user WHERE id_user='".$row['id_user']."' ");
            $dataUser = $queryUser->getResultArray();
            $row['user'] = $dataUser[0];

            $queryRent  = $this->query("SELECT * FROM tb_rent WHERE id_rent='".$row['id_rent']."' ");
            $dataRent = $queryRent->getResultArray();
            $rowRent = $dataRent[0];
            
            //counting distance
            if ($latitude != '') {
                $getLatitude = explode(",", $latitude);
                $rentLatitude = explode(",", $rowRent['latitude']);

                $distance = $this->distance($rentLatitude[0], $rentLatitude[1], $getLatitude[0], $getLatitude[1]);
                $rowRent['distance'] = $distance;
            }

            $row['rent'] = $rowRent;

            $return_array[] = $row;
        }

        return  $return_array;
    }

    public function allByLimitPanel($limit=100, $offset=0, $flag=1, $status='') {
        $getlimit = "$offset,$limit";

        $sql = " SELECT a.*
            FROM tb_trans a
            WHERE a.flag='".$flag."'
            ORDER BY a.id_trans DESC 
            LIMIT ".$getlimit." ";

        if ($status != '') {
            $sql = " SELECT a.*
            FROM tb_trans a
            WHERE a.flag='".$flag."'
            AND a.status='".$status."'
            ORDER BY a.id_trans DESC 
            LIMIT ".$getlimit." ";
        }    

        
        $query  = $this->query($sql);
        $datas = $query->getResultArray();

        $return_array = array();
        foreach ($datas as $row) {
            $queryUser  = $this->query("SELECT * FROM tb_user WHERE id_user='".$row['id_user']."' ");
            $dataUser = $queryUser->getResultArray();
            $row['user'] = $dataUser[0];

            $queryRent  = $this->query("SELECT * FROM tb_rent WHERE id_rent='".$row['id_rent']."' ");
            $dataRent = $queryRent->getResultArray();
            $row['rent'] = $dataRent[0];
            
            $return_array[] = $row;
        }

        return  $return_array;
    }
    
    public function allByLimit($limit=100, $offset=0) {
        return $this->where('status','1')
                    ->orderBy('id_trans','desc')
                    ->findAll($limit, $offset);
    }

    public function getLastId() {
        return $this->orderBy('id_trans','desc')
                    ->first();
    }
    
    public function update_byno($array) {
        $trans = $this->getByNo($array['no']);
        
        if ($trans['id_trans'] != '') {
            $array['id_trans'] = $trans['id_trans'];
            $this->save($array);
        }

        return $this->getByNo($array['no']);
    }

    public function save_update($array) {

        if ($array['iu'] == '' || $array['ir'] == '') {
            return null;
        }

        $idUser = $array['iu'];

        $getRand = "" . str_replace(".","",microtime(true)).rand(11,99);
        $getNow  = "" . date("mdy");
        $noTrans = $array['no'] ?? 'HR' . $getNow . substr($getRand, 10);

        if ($idUser != '' && $idUser != '0') {

            $data = [
                'id_trans'       => $array['id'],
                'id_user'        => $array['iu'],
                'no_trans'         => $array['no'] ?? $noTrans,
                'duration'         => $array['dr'],
                'id_rent'      => $array['ir'],
                'currency'       => $array['cr'],
                'unit_price'       => $array['up'],
                'total'        => $array['tt'],
                'payment'          => $array['py'],
                'desc_payment'       => $array['dp'],
                'is_deleted'       => $array['del'] ?? 0,
                'flag'        => $array['fl'] ?? 1, 
                'status'        => $array['st'] ?? 1,
            ];

            if ($array['id'] == '') {
                $check = $this->getByNo($array['no']);
                if ($check['id_trans'] != '' && $check['id_trans'] != '0') { 
                    $data['id_trans'] = $check['id_trans'];
                }

                // update user
                $sqlUpdate1 = " UPDATE tb_user SET total_rent=total_rent+1 WHERE id_user='".$idUser."' ";
                $this->query($sqlUpdate1);

                // update rent
                $sqlUpdate1 = " UPDATE tb_rent SET total_rent=total_rent+1 WHERE id_rent='".$array['ir']."' ";
                $this->query($sqlUpdate1);
                
            }
            
            $this->save($data);
            
            $check = $this->getByNo($noTrans);
            // update category
            $sqlUpdate1 = " UPDATE tb_category SET total_rent=total_rent+1 WHERE id_category='".$check['id_category']."' ";
            $this->query($sqlUpdate1);
            
        }

        return $this->getByNo($noTrans);
    }

    public function getById($id) {
        return $this->where('id_trans', $id)
                    ->first();
    }

    public function getByNo($no) {
        return $this->where('no_trans', $no)
                    ->first();
    }

    public function searchByQuery($query, $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        $sql = " SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
            WHERE b.id_install=c.id_install 
            ORDER BY b.fullname ASC 
            LIMIT ".$getlimit." ";

        if (trim($query) != '') {
            $sql = " SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
                WHERE b.id_install=c.id_install 
                AND (b.fullname LIKE '%".$query."%' OR b.email LIKE '%".$query."%' )
                ORDER BY b.fullname ASC 
                LIMIT ".$getlimit." ";
        }
        $query1   = $this->query($sql);
        $result1 = $query1->getResultArray();
        return $result1;
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