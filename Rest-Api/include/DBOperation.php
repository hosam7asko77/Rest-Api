<?php
/**
 *
 */
class DbOperation
{
  private $con;
  function __construct()
  {
    require_once dirname(__FILE__).'/SendEmail.php';
     require_once dirname(__FILE__).'/DBConnect.php';
     $db=new DBConnect;
     $this->con=$db->connect();
  }

public function createUser($name,$course,$email,$password,$age){
  if(!$this->isEmailExist($email)){
    $verifyEmail=$this->generateString(30);
    $sent=new SendMail;
    $sentResult=$sent->sendToken($email,$verifyEmail);
    if ($sentResult) {
    $status=false;
    $sql=$this->con->prepare("INSERT INTO student(s_name, s_course, s_email, s_password, s_age,verify_email, verify_status)
    VALUES(?,?,?,?,?,?,?)");
    $sql->bind_param("sssssss",$name,$course,$email,$password,$age,$verifyEmail,$status);
    if($sql->execute()){
      return 200;
    }else {
      return 404;
    }
  }else {
    return 404;
  }
  }else {
    return 401;
  }
}
  public function isEmailExist($email){
    $sql=$this->con->prepare("SELECT * FROM student WHERE s_email=?");
    $sql->bind_param("s",$email);
    $sql->execute();
    $sql->store_result();
    return $sql->num_rows  > 0;
  }
  public function userLogin($email,$password){
    if($this->isEmailExist($email)){
      $hashed_password= $this->getUserPasswordByEmail($email);
      if(password_verify($password,$hashed_password)){
        return 201;

      }else {

        return 202;
      }
    }else {
      return 203;
    }

  }
  public function getUserPasswordByEmail($email)
{
$sql=$this->con->prepare("SELECT s_password FROM student WHERE s_email=?");
$sql->bind_param("s",$email);
$sql->execute();
$sql->bind_result($password);
$sql->fetch();
return $password;

}
public function getUserByEmail($email)
{
$sql=$this->con->prepare("SELECT s_id,s_name, s_course, s_email, s_password, s_age FROM student WHERE s_email=? ");
$sql->bind_param("s",$email);
$sql->execute();
$sql->bind_result($id,$name,$course,$email,$password,$age);
$sql->fetch();
$user=array();
$user['id']=$id;
$user['name']=$name;
$user['course']=$course;
$user['email']=$email;
$user['password']=$password;
$user['age']=$age;
return $user;
}
function getAll(){
$sql=$this->con->prepare("SELECT s_id,s_name, s_course, s_email, s_password, s_age FROM student");
$sql->execute();
$sql->bind_result($id,$name,$course,$email,$password,$age);
  $users=array();
while ($sql->fetch()) {
  $user=array();
  $user['id']=$id;
  $user['name']=$name;
  $user['course']=$course;
  $user['email']=$email;
  $user['password']=$password;
  $user['age']=$age;
array_push($users,$user);
}
  return $users;
}
public function addImage($name1,$name2,$user)
{
    $sql=$this->con->prepare("INSERT INTO image(profile, background,u_id)VALUES(?,?,?)");
    $sql->bind_param("sss",$name1,$name2,$user);
    if($sql->execute()){
      return 200;
    }else {
      return 404;
    }

}
public function getVerifyEmail($verifyEmailtoken)
{
  $sql=$this->con->prepare("SELECT * FROM student WHERE verify_email=?");
  $sql->bind_param("s",$verifyEmailtoken);
  $sql->execute();
  $sql->store_result();
  return $sql->num_rows  > 0;
}
public function updateVerifyStatus($verifyEmailToken)
{
  if ($this->getVerifyEmail($verifyEmailtoken)) {
$sql=$this->con->prepare("UPDATE student SET verify_email=?,verify_status=? WHERE verify_email = ?");
$status=true;
$updateValue=null;
$sql->bind_param("sss",$updateValue,$status,$verifyEmailtoken);
if($sql->execute()){
  return 200;
}else {
  return 404;
}
  }else {
    return 500;
  }
}
public function generateString($number)
{
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$gen=substr(str_shuffle($permitted_chars), 0, $number);
return $gen;
}
public function addAddress($fullAddress,$country,$state,$city,$lng,$lat)
{

  $sql=$this->con->prepare("INSERT INTO address(full_address,
      country,
       state,
        city,
        lng,
         lat)VALUES(?,?,?,?,?,?)");
  $sql->bind_param("ssssss",$fullAddress,$country,$state,$city,$lng,$lat);
  if($sql->execute()){
    return 200;
  }else {
    return 401;
  }
}

}

 ?>
