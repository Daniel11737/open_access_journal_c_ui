<?php 

require 'dbcon.php';
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);

session_start();



if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $data = $_POST;

    $errors = login($data);

    if (empty($errors)) {
        // Login successful
        echo json_encode(array("success" => "Login successful"));
    } else {
        // Login failed, send error messages
        echo json_encode(array("error" => implode("<br>", $errors)));
    }
}

function login($data)
{
    $errors = array();

    if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email";
    }

    if (!isset($data['password']) || strlen(trim($data['password'])) < 4) {
        $errors[] = "Password must be at least 4 characters long";
    }

    if (empty($errors)) {
        $arr['email'] = $data['email'];
        $password = hash('sha256', $data['password']);
        $urli = $data['urli'];

        $query_author = "SELECT * FROM author WHERE email = :email AND status = 1 LIMIT 1";
        $row_author = database_run($query_author, $arr);

        if (is_array($row_author)) {
            $row_author = $row_author[0];

            if ($password === $row_author->password) {
                $_SESSION['USER'] = $row_author;
                $_SESSION['id'] = $row_author->author_id;
                $_SESSION['journal_id'] = $row_author->journal_id;
                $_SESSION['role'] = $row_author->role;
                $_SESSION['first_name'] = $row_author->first_name;
                $_SESSION['middle_name'] = $row_author->middle_name;
                $_SESSION['last_name'] = $row_author->last_name;
                $_SESSION['email'] = $row_author->email;
                $_SESSION['orc_id'] = $row_author->orc_id;
                $_SESSION['public_name'] = $row_author->public_name;
                $_SESSION['role'] = $row_author->role;
                $_SESSION['position'] = $row_author->position;
                $_SESSION['country'] = $row_author->country;
                $_SESSION['gender'] = $row_author->gender;
                $_SESSION['birthday'] = $row_author->birth_date;
                $_SESSION['bio'] = $row_author->bio;
                $_SESSION['status'] = $row_author->status;
                $_SESSION['afiliations'] = $row_author->afiliations;
                $_SESSION['expertise'] = $row_author->field_of_expertise;
                $_SESSION['date_added'] = $row_author->date_added;
                $_SESSION['affix'] = $row_author->affix;
                $_SESSION['expertise'] = $row_author->field_of_expertise;
                $_SESSION['USER']->urli = $urli;
                $_SESSION['LOGGED_IN'] = true;
            } else {
                $errors[] = "Wrong email or password";
            }
        } else {
            $query_admin = "SELECT * FROM admin WHERE email = :email AND status = 1 LIMIT 1";
            $row_admin = database_run($query_admin, $arr);

            if (is_array($row_admin)) {
                $row_admin = $row_admin[0];

                if ($password === $row_admin->password) {
                    $_SESSION['USER'] = $row_admin;
                    $_SESSION['id'] = $row_admin->admin_id;
                    $_SESSION['role'] = $row_admin->role;
                    $_SESSION['email'] = $row_admin->email;
                    $_SESSION['first_name'] = $row_admin->first_name;
                    $_SESSION['last_name'] = $row_admin->last_name;
                    $_SESSION['middle_name'] = $row_admin->middle_name;
                    $_SESSION['LOGGED_IN'] = true;
                } else {
                    $errors[] = "Wrong email or password";
                }
            } else {
                $errors[] = "Wrong email or password";
            }
        }
    }

    return $errors;
}

function check_login($redirect = true){

	if(isset($_SESSION['USER']) && isset($_SESSION['LOGGED_IN'])){

		return true;
	}

	if($redirect){
		header("Location: login.php");
		die;
	}else{
		return false;
	}
	
}



