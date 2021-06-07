<?php
/**
 * Class User
 *
 * @package models
 *
 *
 *
 * @OA\Schema(
 *     title="User model",
 *     description="User model",
 * )
 */
class User
{

  // database connection and table name
  private $conn;
  private $table_name = "users";

  // object properties
  /**
   * @OA\Property(
   *     format="int64",
   *     description="ID",
   *     title="ID",
   * )
   *
   * @var integer
   */
  public $id;
  /**
   * @OA\Property(
   *     description="Name",
   *     title="Name",
   * )
   *
   * @var string
   */
  public $name;
  /**
   * @OA\Property(
   *     description="Username",
   *     title="Username",
   * )
   *
   * @var string
   */
  public $username;
  /**
   * @OA\Property(
   *     description="Mobile",
   *     title="Mobile",
   * )
   *
   * @var string
   */
  public $mobile;
  /**
   * @OA\Property(
   *     format="email",
   *     description="Email",
   *     title="Email",
   * )
   *
   * @var string
   */
  public $email;
  /**
     * @OA\Property(
     *     format="int64",
     *     description="Password",
     *     title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
  public $password;

  // constructor
  public function __construct($db)
  {
    $this->conn = $db;
  }

  function findByEmail($email){
    $query = "SELECT * FROM " . $this->table_name . " WHERE email=:email;";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $num = $stmt->rowCount();
    if($num>0){
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $this->id = $row['id'];
      $this->name = $row['name'];
      $this->username = $row['username'];
      $this->mobile = $row['mobile'];
      $this->email = $row['email'];
      $this->password = $row['password'];     
      } else {
        return "account does not exist";
      }
  }
  

  function login(): array {
    $errors = array();
    if(!$this->username) {
      array_push($errors, "username is empty.");
    }
    if(!$this->password) {
      array_push($errors, "password is empty.");
    }
    if($this->username && $this->password) {
      $query = "SELECT * FROM " . $this->table_name . " WHERE username=:username;";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(":username", $this->username);
      $stmt->execute();
      $num = $stmt->rowCount();
      if($num>0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!password_verify($this->password, $row['password'])) {
          array_push($errors, "password is not valid.");
        } else {
          $this->id = $row['id'];
          $this->name = $row['name'];
          $this->username = $row['username'];
          $this->mobile = $row['mobile'];
          $this->email = $row['email'];
          $this->password = $row['password'];
        }
      } else {
        array_push($errors, "username does not exist.");
      }
    }
    return $errors;
  }

  // create new user record
  function create() {
    $errors = array();

    if(!$this->name) {
      array_push($errors, "name is empty.");
    }

    if(!$this->username) {
      array_push($errors, "username is empty.");
    }

    if(!$this->mobile) {
      array_push($errors, "mobile is empty.");
    }

    if(!$this->email) {
      array_push($errors, "email is empty.");
    }

    if(!$this->password) {
      array_push($errors, "password is empty.");
    }

    if(!empty($errors)) {
      return $errors;
    }

    // insert query
    $query = "INSERT INTO " . $this->table_name . "
          SET
              name = :name,
              username = :username,
              mobile = :mobile,
              email = :email,
              password = :password";

    // prepare the query
    $stmt = $this->conn->prepare($query);

    // validate
    $errors = $this->validate($errors);
    

    // bind the values
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':username', $this->username);
    $stmt->bindParam(':mobile', $this->mobile);
    $stmt->bindParam(':email', $this->email);

    // hash the password before saving to database
    $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $password_hash);

    
    if(!empty($errors)) {
      return $errors;
    }
    
    if ($stmt->execute()) {
      $stmt = $this->executeUserStmt($this->username, 'username');
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $this->id = $row['id'];
    } else {
      array_push($errors, "Unknown error");
    }

    return $errors;
  }
    

  // }
  private function validate($errors) : array {

    // username
    if($this->hasSpecialCharacters($this->username)) {
      array_push($errors, "username has special characters");
    }
    if(!$this->checkIsUnique($this->username, "username")) {
      array_push($errors, "username is not unique");
    }

    // email
    if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
      array_push($errors, "email not valid.");
    }
    if(!$this->checkIsUnique($this->email, "email")) {
      array_push($errors, "email is not unique");
    }

    // password
    if(strlen($this->password) < 6 && !preg_match('/[A-Z]/', $this->password) && $this->hasSpecialCharacters($this->password) &&
    !preg_match('/\d/', $this->password)) {
      array_push($errors, "password not valid.");
    }
    return $errors;
  }

  function hasSpecialCharacters($string) {
    return preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $string);
  }

  function checkIsUnique($value, $name) {
    $stmt = $this->executeUserStmt($value, $name);
    return $stmt->rowCount() == 0;
  }

  function executeUserStmt($value, $name) {
    $query = "SELECT * FROM users WHERE " . $name ."=:" . $name;
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":$name", $value);
    $stmt->execute();
    return $stmt;
  }
}
