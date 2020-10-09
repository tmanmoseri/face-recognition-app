<?php

include('classes/DB.php');
include('classes/Login.php');

$tokenIsValid = False;
$errors = array();

if(Login::isLoggedIn()){
    $tokenIsValid = True;
    header("Location: index.php");
}


if(isset($_POST['register'])){
    
     $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
			'secret' => "6LexmdkUAAAAAFdVXetRc1WiS5D5RIODM8cVkrXS",
			'response' => $_POST['token'],
			'remoteip' => $_SERVER['REMOTE_ADDR']
		];

		$options = array(
		    'http' => array(
		      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		      'method'  => 'POST',
		      'content' => http_build_query($data)
		    )
		  );

		$context  = stream_context_create($options);
  		$response = file_get_contents($url, false, $context);

		$res = json_decode($response, true);
		if($res['success'] == true) {
    
            
    $empID = $_POST['empID'];
    $password = $_POST['password'];
    $passwordrepeat = $_POST['password_repeat'];
    $email = $_POST['email'];
    $profileImage = 'https://i.imgur.com/hb19E1c.png';
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phoneNum = $_POST['phoneNum'];
    $secretAns = $_POST['secretAns'];
    $deptID = $_POST['deptID'];    
    $isAdmin = 0;
            
        if($deptID == 0000){
                $isAdmin = 1;
        }
  
    if(!DB::query('SELECT empID FROM users WHERE empID=:empID', array(':empID'=>$empID))){
        if(!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))){
        
        if(strlen($empID) >= 3 && strlen($empID) <= 10 ){
            
            if(preg_match('/[a-zA-Z0-9_]+/', $empID)){
                
                if(strlen($password) >= 6 && strlen($password) <= 60){
                
                if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                    
                    if($password == $passwordrepeat){
                        
                        DB::query('INSERT INTO users VALUES(:empID, :firstName, :lastName, :email, :phoneNum, :password, :securityAns, :profilePic, :deptID, :isAdmin)', array(':empID'=>$empID, ':firstName'=> $firstName, 'lastName'=>$lastName, ':email'=>$email, 'phoneNum'=>$phoneNum, ':password'=>password_hash($password,PASSWORD_BCRYPT), ':securityAns'=>$secretAns, ':profilePic'=>$profileImage, ':deptID'=>$deptID, ':isAdmin'=>$isAdmin));
        
                
        echo "Success";
                        
        header('Location: login.php');
        }
                    
                    else{
                        $err = "Password don't match";
                        array_push($errors, $err);
                    }
                }
                    
                    else{
                    $err = "Invalid email address";
                    array_push($errors, $err);
                }
                }
                
                 else{
                    $err = "Invalid password. Doesn't match minimum character length of 6 or exceeds maximum character length of 60";
                     array_push($errors, $err);
                }
                    
                }
            
            else {
                $err = "Invalid employee ID. Doesn't match allowed format";
                array_push($errors, $err);
            }
                
            }
            
            else {
           $err = "Invalid employee ID. Doesn't meet minimum character length of 3 or exceeds maximum length of 10.";
                array_push($errors, $err);
        }
            
            
        }
        
        else {
            $err = "This email address is already in use";
            array_push($errors, $err);
        }
        
    }
    
    else{
        $err =  "This user already exists in our records";
        array_push($errors, $err);
    }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Register - Zensar compliance tracker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.min.css">
    <script src="https://www.google.com/recaptcha/api.js?render=6LexmdkUAAAAAMRZ8X2k7cFIt7MiUA1zqEPYiVmg"></script>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="card shadow-lg o-hidden border-0 my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-flex">
                        <div class="flex-grow-1 bg-register-image" style="background-image: url(&quot;assets/img/zensar.jpg&quot;);background-position: center;background-size: contain;background-repeat: no-repeat;"></div>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h4 class="text-dark mb-4">Create an Account!</h4>
                            </div>
                            
                            <?php if(count($errors) > 0){
                                            foreach($errors as $error){
                                            echo "<div><p class='text-danger'>".$error."</p></div>";
                                        }
                                        }?>
                            
                            <form class="user" method="post" action="register.php">
                                 <input type="hidden" id="token" name="token">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0"><input class="form-control form-control-user" type="text" id="firstName" placeholder="First Name" name="firstName"></div>
                                    <div class="col-sm-6"><input class="form-control form-control-user" type="text" id="lastName" placeholder="Last Name" name="lastName" required></div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0"><input class="form-control form-control-user" type="text" id="empID" placeholder="Employee ID" name="empID" required></div>
                                    <div class="col-sm-6"><input class="form-control form-control-user" type="text" id="secretAns" placeholder="Secret Word / Phrase" name="secretAns" required></div>
                                </div>
                                <div class="form-group"><input class="form-control form-control-user" type="email" id="email" aria-describedby="emailHelp" placeholder="Email Address" name="email" required></div>
                                <div class="form-group"><input class="form-control form-control-user" type="text" id="phoneNum" aria-describedby="emailHelp" placeholder="Phone Number" name="phoneNum" required></div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0"><input class="form-control form-control-user" type="password" id="password" placeholder="Password" name="password" required></div>
                                    <div class="col-sm-6"><input class="form-control form-control-user" type="password" id="password_repeat" placeholder="Repeat Password" name="password_repeat" required></div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0"><input class="form-control form-control-user" type="text" id="deptID" placeholder="Department ID" name="deptID" required></div>
                                </div>
                                <button class="btn btn-primary btn-block text-white btn-user" type="submit" id="register" name="register">Register Account</button>
                                <hr>
                            </form>
                            <div class="text-center"><a class="small" href="login.php">Already have an account? Login!</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="assets/js/script.min.js"></script>
    <script>
          grecaptcha.ready(function() {
              grecaptcha.execute('6LexmdkUAAAAAMRZ8X2k7cFIt7MiUA1zqEPYiVmg', {action: 'homepage'}).then(function(token) {
                 // console.log(token);
                 document.getElementById("token").value = token;
              });
          });
    </script>
</body>

</html>