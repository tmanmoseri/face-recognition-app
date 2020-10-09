<?php

class Login {
    public static function isLoggedIn(){
    
    if(isset($_COOKIE['complianceSession'])){
        if(DB::query('SELECT empID FROM login_tokens WHERE token = :token', array(':token'=>sha1($_COOKIE['complianceSession'])))){
            
            $userID = DB::query('SELECT empID FROM login_tokens WHERE token = :token', array(':token'=>sha1($_COOKIE['complianceSession'])))[0]['empID'];
            
            if(isset($_COOKIE['complianceSession_'])){
                return $userID;
            }
            
            else {
                $cstrong = true;
                $id = 0;
                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                DB::query('INSERT INTO login_tokens VALUES (:id, :token, :empID)', array(':id'=> $id, ':token' => sha1($token), ':empID' => $userID));
                DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['complianceSession'])));
                
                setcookie("complianceSession", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);
                setcookie("complianceSession_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, true);
                
                return $userID;
            }
            
        }
    }
    
    return false;
}
}

?>