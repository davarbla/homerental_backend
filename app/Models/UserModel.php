<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'tb_user';
    protected $primaryKey = 'id_user';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['fullname', 'username', 'phone', 'email', 'about', 
    'image', 'location', 'latitude', 'country', 'id_install', 'uid_fcm', 'total_rent', 
    'total_like', 'total_comment', 'total_follower', 'total_following', 
    'password_user', 'password_real', 'timestamp', 'flag', 'status', 
    'date_created', 'date_updated'];

    protected $useTimestamps = true;
    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    
    protected $skipValidation     = true;

    private $keyServerFCM = 'AAAAFldyrLE:APA91bG3goVYUIx5hJaepqveT4uWj6FhNG47mNaPqaQfcrmY2Uh3EnjY8KuL2PUNt88FB3WaX7r_fZnESu4v2cw44zGtgTgB7kjIxX4vDLVzKirjiV0mFZC-t8Arc2B2ivFK0nnMXnhx';
        
    
    public function getTotal($os='', $group='') {
        $sql = " SELECT count(id_user) as total FROM tb_user ";
        if ($os != '') {
            $sql = " SELECT count(a.id_user) as total FROM tb_user a, tb_install b
                WHERE a.id_install=b.id_install
                AND b.os_platform='".$os."' ";
        }
        else if ($group != '') {
            $sql = " SELECT country, timestamp, count(id_user) as total FROM tb_user 
                GROUP BY country 
                ORDER BY total DESC LIMIT 8";
        }

        $query   = $this->query($sql);
        $results = $query->getResultArray();
        return $results;
    }

    public function getCountAll($limit=100, $offset=0, $status=1) {
        $getlimit = "$offset,$limit";
        
        $sql = " SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
            WHERE b.id_install=c.id_install 
            ORDER BY b.fullname ASC 
            LIMIT ".$getlimit." ";

        $query = $this->query($sql);
        $allUser = $query->getResultArray();

        $sql = " SELECT count(b.id_user) as counter, sum(b.total_rent) as total_rent FROM tb_user b ";
        $query = $this->query($sql);
        $countUser = $query->getResultArray();


        return array(
            "total" => $countUser,
            "data" => $allUser
        );
    }

    public function getByDateAll($group='') {
        $sql = " select DATE_FORMAT(date_created,'%d/%m/%Y') AS regDate , count(*) as total
            from tb_user
            group by DATE_FORMAT(date_created,'%d/%m/%Y')
            order by date_created ";
        if ($group != '') {
            $sql = " select DATE_FORMAT(date_created,'%d/%m/%Y') AS regDate , count(*) as total
                from tb_user
                group by DATE_FORMAT(date_created,'%d/%m/%Y')
                order by date_created ";
        }
        $query   = $this->query($sql);

        return $query->getResultArray();
    }

    

    public function getByUserAll($id) {
        
        $query   = $this->query(" SELECT a.*, b.os_platform, b.token_fcm, b.token_forgot 
            FROM tb_user a, tb_install b 
            WHERE a.id_install=b.id_install
            AND a.id_user='".$id."' ");

        return $query->getResultArray();
    }


    public function loginByUsername($username, $password) {
        return $this->where('status', '1')
                    ->where('username', $username)
                    ->where('password_user', $password)
                    ->findAll();
    }

    public function loginByEmail($email, $password) {
        return $this->where('status', '1')
                    ->where('email', $email)
                    ->where('password_user', $password)
                    ->findAll();
    }

    public function loginByPhone($phone, $password) {
        return $this->where('status', '1')
                    ->where('phone', $phone)
                    ->where('password_user', $password)
                    ->findAll();
    }

    public function allByLimit($limit=100, $offset=0) {
        return $this->where('status','1')
                    ->orderBy('total_rent','desc')
                    ->orderBy('total_comment','desc')
                    ->orderBy('fullname','asc')
                    ->findAll($limit, $offset);
    }

    public function allByLimitPanel($limit=100, $offset=0) {
        $getlimit = "$offset,$limit";
        
        $query   = $this->query(" SELECT a.*, b.os_platform FROM tb_user a, tb_install b 
            WHERE a.id_install=b.id_install
            ORDER BY a.date_created DESC 
            LIMIT ".$getlimit." ");

        return $query->getResultArray();
    }


    public function getLastId() {
        return $this->orderBy('id_user','desc')
                    ->first();
    }

    public function updateUser($array) {
        if ($array['id']!='') {
            $data = [
                'id_user'       => $array['id'],
                'uid_fcm'       => $array['uf'],
                'id_install'    => $array['is'],
                'latitude'      => $array['lat'],
                'location'      => $array['loc'],
                'country'       => $array['cc'],
            ];

            $this->save($data);
        }

        return $this->getById($array['id']);
    }

    public function register($array) {

        if ($array['em'] == '' || $array['fn'] == '' || $array['is'] == '') {
            return null;
        }

        $username = $array['us'];    
        if ($array['id'] == '' && $username == '') {
            $splitname = explode(" ", strtolower($array['fn']));
            $lastRow = $this->getLastId();

            
            $plusOne = 0;
            if ($lastRow['id_user'] != '') {
                $plusOne = (int) $lastRow['id_user'];
            }

            $plusOne = $plusOne + 1;
            $username = $this->generate_unique_username($splitname[0], $splitname[1],  "$plusOne");
            //print_r($username);
            //die();
        }

        //$datenow = date('YmdHis');
        $data = [
            'id_user'       => $array['id'],
            'id_install'    => $array['is'],
            'email'         => $array['em'],
            'phone'         => $array['ph'],
            'fullname'      => $array['fn'],
            'image'         => 'https://homerental.hobbbies.id/upload/avatar_red.jpg',
            'username'       => $username,
            'uid_fcm'        => $array['uf'],
            'password_user'  => $array['ps'],
            'password_real'  => $array['rp'],
            'latitude'       => $array['lat'],
            'location'       => $array['loc'],
            'country'        => $array['cc'],
        ];

        //print_r($data);
        //die();

        $check = $this->getByEmail($array['em']);
        if ($check['id_user'] != '' && $check['id_user'] != '0') { 
            $data['id_user'] = $check['id_user'];
        }
        
        //print_r($data);
        //die();

        $this->save($data);

        return $this->getByEmail($array['em']);
    }

    public function registerByPhone($array) {

        if ($array['ph'] == '' || $array['fn'] == '') {
            return null;
        }

        $username = $array['us'];    
        if ($array['id'] == '' && $username == '') {
            $splitname = explode(" ", strtolower($array['fn']));
            $lastRow = $this->getLastId();
            
            $plusOne = 0;
            if ($lastRow['id_user'] != '') {
                $plusOne = (int) $lastRow['id_user'];
            }

            $plusOne = $plusOne + 1;
            $username = $this->generate_unique_username($splitname[0], $splitname[1], "$plusOne");
        }

        $data = [
            'id_user'   => $array['id'],
            'id_install'   => $array['is'],
            'email'    => $array['em'],
            'phone'         => $array['ph'],
            'fullname'    => $array['fn'],
            'image'         => 'https://hobbies.in-news.id/upload/avatar.png',
            'username'         => $username,
            'uid_fcm'         => $array['uf'],
            'password_user'  => $array['ps'],
            'latitude'  => $array['lat'],
            'location'  => $array['loc'],
            'country'       => $array['cc'],
        ];

        //print_r($data);
        //die();

        $check = $this->getByPhone($array['ph']);
        if ($check['id_user'] != '' && $check['id_user'] != '0') { 
            $data['id_user'] = $check['id_user'];
        }
        
        $this->save($data);

        return $this->getByPhone($array['ph']);
    }

    public function getByEmail($email) {
        return $this->where('email', $email)
                    ->first();
    }

    public function getByPhone($phone) {
        return $this->where('phone', $phone)
                    ->first();
    }

    public function getById($id) {
        return $this->where('id_user', $id)
                    ->first();
    }

    public function getTokenById($id) {
        $query1   = $this->query(" SELECT b.*, c.token_fcm FROM tb_user b, tb_install c 
            WHERE b.id_install=c.id_install 
            AND b.id_user='".$id."' ");
        $result1 = $query1->getResultArray();
        return $result1[0];
    }

    public function isAvailable($userName){
       $check = $this->where('username', $userName)->first();
    
        if ( $check['id_user'] != '' || strlen(trim($userName)) < 8 ) {
             //echo 'User with this username already exists!';
             return false;
        } else {
            return true;
        }
    }
    
    public function generate_unique_username($firstname, $lastname, $userId){  
        $userNamesList = array();
        $firstChar = str_split($firstname, 1)[0];
        $firstTwoChar = str_split($firstname, 2)[0];
        /**
         * an array of numbers that may be used as suffix for the user names index 0 would be the year
         * and index 1, 2 and 3 would be month, day and hour respectively.
         */
        $numSufix = explode('-', date('Y-m-d-H')); 
    
        // create an array of nice possible user names from the first name and last name
        array_push($userNamesList, 
            $firstname,                 //james
            $lastname,                 // oduro
            $firstname.$lastname,       //jamesoduro
            $firstname.'.'.$lastname,   //james.oduro
            $firstname.'-'.$lastname,   //james-oduro
            $firstChar.$lastname,       //joduro
            $firstTwoChar.$lastname,    //jaoduro,
            $firstname.$numSufix[0],    //james2019
            $firstname.$numSufix[1],    //james12 i.e the month of reg
            $firstname.$numSufix[2],    //james28 i.e the day of reg
            $firstname.$numSufix[3]     //james13 i.e the hour of day of reg
        );
    
    
        $isAvailable = false; //initialize available with false
        $index = 0;
        $maxIndex = count($userNamesList) - 1;

        // loop through all the userNameList and find the one that is available
        do {
            $availableUserName = $userNamesList[$index];
            $isAvailable = $this->isAvailable($availableUserName);
            $limit =  $index >= $maxIndex;
            $index += 1;
            if($limit){
                break;
            }

        } while (!$isAvailable );
    
        // if all of them is not available concatenate the first name with the user unique id from the database
        // Since no two rows can have the same id. this will sure give a unique username
        if(!$isAvailable){
            return $firstname.$userId;
        }
        return $availableUserName;
    }

    //send FCM notif
    public function sendFCMMessage($token, $data_array){ 
        //$keyServerFCM = 'AAAA9eRty_8:APA91bGToAt_01JkfbmiDsRYElEEw9puiibwShvtZCcryoFDs8SnousKcLZjpo-ufP9U3iF5BuU8KCdtnFiYkoYYQ3jnSMAikEv36ZrukyJMGNmG2t2CMS8mgpgW_6UK8Ze12vCkq0gx';
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = array(
            'notification' => array(
                "title" => $data_array['title'], 
                "body"  => $data_array['body'], 
                'image'  => $data_array['image'],
                'imageUrl' => $data_array['image'],
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                'priority' =>  'high', 
                'sound' => 'default'
            ),
            'data' => $data_array['payload'],
            // Set Android priority to "high"
            'android' => array(
                'priority'=> "high",
                'image'  => $data_array['image'],
            ),
            // Add APNS (Apple) config
            'apns' => array(
                'payload' => array(
                    'aps' => array(
                        'contentAvailable' => true,
                    ),
                ),
                'headers' => array(
                    "apns-push-type" => "background",
                    "apns-priority" => "5", // Must be `5` when `contentAvailable` is set to true.
                    "apns-topic" => "io.flutter.plugins.firebase.messaging", // bundle identifier
                ),
            ),
            'priority' => 'high',
            "to" => $token
        );

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode($data),
                'header'=>  "Content-Type: application/json\r\n" .
                            "Accept: application/json\r\n" .
                            "Authorization: key=" . $this->keyServerFCM
            )
        );
        
        $context  = stream_context_create( $options );
        $result =  file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

}

/* id_user, fullname, username, phone, email, about, 
image, location, latitude, id_install, uid_fcm, total_rent, 
total_like, total_comment, total_follower, total_following, 
password_user, timestamp, flag, status, 
date_created, date_updated
*/