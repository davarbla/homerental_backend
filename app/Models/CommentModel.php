<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table      = 'tb_comment';
    protected $primaryKey = 'id_comment';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['id_rent', 'id_user', 'title', 'description', 'latitude', 'rating', 'flag', 'status', 'date_created', 'date_updated'];
    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    //protected $deletedField  = 'deleted_at';
    //protected $validationRules    = [];
    //protected $validationMessages = [];
    protected $skipValidation     = true;

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.*, 
            (SELECT x.fullname FROM tb_user x WHERE x.id_user=a.id_user) as fullname,
            (SELECT x.image FROM tb_user x WHERE x.id_user=a.id_user) as image,
            (SELECT x.title FROM tb_rent x WHERE x.id_rent=a.id_rent) as title_rent
            FROM tb_comment a 
            ORDER BY a.id_comment DESC
            LIMIT ".$getlimit." ");

        return $query->getResultArray();
    }

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

    public function allByLimitByIdRent($idRent, $limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";

        $query3   = $this->query(" SELECT b.* FROM tb_comment b
        WHERE b.status='".$status."' 
        AND b.id_rent='".$idRent."' ORDER BY b.id_comment DESC LIMIT $getlimit ");
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

        return $arrComments;
    }

    public function do_comment_rent($array) {

        if ($array['iu']!='' && $array['ir'] != '' && $array['ds'] != '') {
            $idUser = $array['iu'];
            $idRent = $array['ir'];

            $data = [
                'id_user'       => $idUser,
                'id_rent'       => $idRent,
                'rating'      =>  $array['rt'],
                'latitude'      =>  $array['lat'],
                'description'   => $array['ds'],
            ];

            
            $this->save($data);

            //update user
            $sqlUpdate1 = " UPDATE tb_user SET total_comment=total_comment+1 WHERE id_user='".$idUser."' ";
            $this->query($sqlUpdate1);

            $sqlCount = " SELECT count(id_comment) as count_rating, sum(rating) as total_rating FROM tb_comment WHERE id_rent='".$idRent."' ";
            $query   = $this->query($sqlCount);
            $results = $query->getResultArray();

            $counterRating = $results[0]['total_rating'];
            $totRating = $results[0]['count_rating'];
            $avgRating = $counterRating / $totRating;
            $roundRating = round($avgRating, 1);
            
            //update rent
            $sqlUpdate2 = " UPDATE tb_rent SET rating='".$roundRating."', total_rating='".$totRating."' WHERE id_rent='".$idRent."' ";
            $this->query($sqlUpdate2);

        }

        return $this->getByIdUserRent($idUser, $idRent);
    }

    public function delete_comment($array) {

        if ($array['id']!='') {
            
            $data = [
                'id_comment'       => $array['id'],
                'id_category'   => $idCategory,
                'is_comment'      => 1,
                'flag'          => 2,
            ];
            
            $this->delete($data);

            //update user
            $sqlUpdate1 = " UPDATE tb_user SET total_like=total_like+1 WHERE id_user='".$idUser."' ";
            $this->query($sqlUpdate1);


        }

        return $this->getByIdUserRent($idUser, $idRent);
    }

    public function getById($id) {
        return $this->where('id_comment', $id)
                    ->first();
    }

    public function getByIdUserRent($idUser, $idRent) {
        return $this->where('id_user', $idUser)
                    ->where('id_rent', $idRent)
                    ->first();
    }

    
}