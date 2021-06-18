<?php
/*
 *
 * Copyright Elijah M. Abgao
 * Free to use
 *
 */
class dbhelper
{
    private $db_username;
    private $db_name;
    private $db_password;
    private $db_host;
    private $conn;
    private $image_b64;
    private $image_bin;
    private $fields = array();

    /**
     * dbhelper constructor.
     * @param string $db_username
     * @param string $db_name
     * @param string $db_password
     * @param string $db_host
     */
    public function __construct($db_username, $db_name, $db_password, $db_host)
    {
        $this->db_username = $db_username;
        $this->db_name = $db_name;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->conn = new mysqli($this->db_host,$this->db_username,$this->db_password,$this->db_name);
    }

    /**
     * @param mixed $image_b64
     */
    public function setImageB64($image_b64)
    {
        $this->image_b64 = $image_b64;
    }

    /**
     * @param mixed $image_bin
     */
    public function setImageBin($image_bin)
    {
        $this->image_bin = $image_bin;
    }

    /**
     * @return mixed
     */
    public function getImageBin()
    {
        return $this->image_bin;
    }

    function setFields(...$fields){
        $tmp = array();
        for($i = 0; $i != count($fields); ++$i){
            $tmp[] =  $fields[$i];

        }
        $this->fields = $tmp;
    }

    function pushDB($tablename,...$data){
        $binds = "";
        $value_params = "";
        $bind_array = array();
        for($i = 0; $i != count($data); ++$i){
            switch (gettype($data[$i])){
                case 'integer':
                    $binds .='i';
                    break;
                case 'string':
                    $binds .='s';
                    break;
                case 'double':
                    $binds .='d';
                    break;

            }
            if ($i < count($data) - 1){
                $value_params .= '?,';
            }else{
                $value_params .= '?';
            }
            $bind_array[] = $data[$i];
        }
        $field_array = implode(", ",$this->fields);
        $stmt = $this->conn->prepare("INSERT INTO ".$tablename."(".$field_array.")".
            "VALUES "."(".$value_params.")");
        $stmt->bind_param($binds,...$bind_array);
        if($stmt->execute() === TRUE){
            return true;
        }else{
            return false;
        }
    }
    function getData($tablename,...$column){
        $column_array = array();
        $row = array();
        for($i = 0; $i != count($column); ++$i){
            $column_array[] = $column[$i];
        }
        $result = $this->conn->query("SELECT ". implode(', ',$column_array) ." FROM ".$tablename);
        if($result->num_rows > 0){
            for($i = 0; $i != $result->num_rows; ++$i){
                $row[] = mysqli_fetch_assoc($result);
            }

        }
        return $row;
    }
    function getCurrentData($tablename,$id,$selector,...$column){
        $column_array = array();
        $row = array();
        for($i = 0; $i != count($column); ++$i){
            $column_array[] = $column[$i];
        }
        $result = $this->conn->query("SELECT ". implode(', ',$column_array) ." FROM ".$tablename. " WHERE ".$selector. "="."\"".$id."\"");
        if($result->num_rows > 0){
            for($i = 0; $i != $result->num_rows; ++$i){
                $row[] = mysqli_fetch_assoc($result);
            }
        }
        return $row;
    }
    function getAllCurrentData($tablename,$id,$selector,...$column){
        $column_array = array();
        $row = array();
        for($i = 0; $i != count($column); ++$i){
            $column_array[] = $column[$i];
        }
        $result = $this->conn->query("SELECT ". implode(', ',$column_array) ." FROM ".$tablename. " WHERE ".$selector. "="."\"".$id."\"");
        if($result->num_rows > 0){
            $row[] = mysqli_fetch_assoc($result);
        }
        return $row;
    }

    function editData($tablename,$id,$selector,...$column){
        $builder = array();
        for($i = 0; $i != count($column);++$i){
            $builder[] = $this->fields[$i].'= '."'".$column[$i]."'";
        }
        $sql = "UPDATE ".$tablename." SET ".implode(', ',$builder)." WHERE ".$selector."=".$id;
        if( $this->conn->query($sql) === TRUE){
            return true;
        }else{
            return false;
        }
    }

    function deleteData($tablename,$id){
        $sql = "DELETE FROM ".$tablename." WHERE ".$tablename.".id = ".$id."";
        if($this->conn->query($sql) === TRUE){
            echo "Entry Deleted";
        }

    }
    function DBConn(){
        return $this->conn;
    }
    function b64_to_image($b64,$userid){
        $bin = base64_decode($b64);
        $image = imagecreatefromstring($bin);

        if(!$image){
            die('Base64 value is not an Image!');
        }
        $image_file = '/img/'.$userid;
        imagepng($image,$image_file,4);
    }

    function getImage($image,$filename){
        if($image === null){
            $response = $this->getCurrentData("admin",$_SESSION['user_id'],"userid","*");
            return $response[0]['image'];
        }
        $imagefiletype = pathinfo($image,PATHINFO_EXTENSION);
        $basename = $filename.".".$imagefiletype;
        $location = "./img/".$basename;

        // sanitize file
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false){
            $uploadable = true;
        }else{
            echo "File is not an Image: ";
            $uploadable = false;
        }

        if($uploadable === true && $filename != ""){
            if(move_uploaded_file($_FILES['image']['tmp_name'],$location)){
                echo "";
            }else{
                echo "OOPS! \n";
            }
        }else{
            echo "OOPS! \n";
        }
        return $basename;
    }

    function getFile($file,$filename){
        $imagefiletype = pathinfo($file,PATHINFO_EXTENSION);
        $basename = $filename.".".$imagefiletype;
        $location = "/home/deyji/www/moj/admin/files/".$basename;

        if($filename != ""){
            if(move_uploaded_file($_FILES['FILE']['tmp_name'],$location)){
                echo "";
            }else{
                echo "OOPS! \n";
            }
        }else{
            echo "OOPS! \n";
        }
        return $basename;
    }

    function getMultipleFiles($files){
        $length = count($files);
        $location = "/home/deyji/www/moj/admin/files/";
        $index = 0;
        for($i = 0; $i < $length; ++$i){
            $filename = $files[$i];
            $path = $location.$filename;
            $file_ext = pathinfo($path, PATHINFO_EXTENSION);
            $file_ext_list = strtolower($file_ext);

            $sanitize_ext = array("pdf", "doc", "jpg", "jpeg", "png");

            if(in_array($file_ext_list, $sanitize_ext)){
                move_uploaded_file($_FILES["FILE"]["tmp_name"][$i], $path);
            }

        }
    }
}