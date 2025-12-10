<?php
class User {
    private $file = "users.json";
    private $users = [];

    public function __construct() {
        if (file_exists($this->file)) {
            $data = json_decode(file_get_contents($this->file), true);
            $this->users = $data ? $data : [];
        } else {
            $this->users = [];
        }
    }

    private function validatePassword($password) {
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return "Password must contain at least one special character";
        }
        return true;
    }

  
    public function register($fullname, $phone, $address, $dob, $pob, $gender, $email, $username, $password) {
        

        $passwordValidation = $this->validatePassword($password);
        if ($passwordValidation !== true) {
            return $passwordValidation;
        }

   
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                return "Username is already taken";
            }
            if ($user['email'] === $email) {
                return "Email is already registered";
            }
        }


        $this->users[] = [
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address,
            'dob' => $dob,
            'pob' => $pob,
            'gender' => $gender,
            'email' => $email,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        file_put_contents($this->file, json_encode($this->users, JSON_PRETTY_PRINT));

        return "Registration successful!";
    }

    public function login($username, $password) {
        foreach ($this->users as $user) {
            if ($user['username'] === $username) {
                if (password_verify($password, $user['password'])) {
                    return "Login successful";
                } else {
                    return "Incorrect password";
                }
            }
        }
        return "Username not found";
    }
}
?>