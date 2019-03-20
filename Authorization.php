<?php

class Authorization {
    private $link;
    public $user = null;
    public $user_session = null;

    function __construct(PDO $link) {
        $this->link = $link;
    }

    function getUser(){
        return $this->user;
    }
    function setUser($user){
        $this->user = $user;
    }

    function getUserSession(){
        return $this->user_session;
    }
    function setUserSession($session){
        $this->user_session = $session;
    }

    function login ($username, $password) {
        $queryUserExist = $this->userExists($username, $password);
        if ($queryUserExist->rowCount() === 0) {
            return [ 'error' => 'User not found', ];
        }
        $token = $this->generateToken();

        $this->setUser($queryUserExist->fetch());
        $this->setUserSession(null);
        $this->registerToken($this->getUser(), $token);
        
        return true;
    }

    function registerToken($user, $token){
        $prepare = $this->link->prepare('
            INSERT INTO user_session
                (user, token, active)
            VALUES
                (:user, :token, 1)
        ');

        $result = $prepare->execute([
            'user' => $user['id'],
            'token' => $token,
        ]);

        $this->user_session = [
            'id' => $this->link->lastInsertId(),
            'token' => $token,
            'active' => true,
        ];
    }

    function userExists($username, $password){
        $prepare = $this->link->prepare('
            SELECT id
            FROM user
            WHERE
                username = :username
                AND password = :password
        ');

        $prepare->execute([
            'username' => $username,
            'password' => $password,
        ]);
        return $prepare;
    }

    function generateToken(){
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrtuvwxyz';
        $token = '';
        for ($i = 0; $i < 20; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $token;
    }

    function verifyToken ($token) {
        $prepare = $this->link->prepare('
            SELECT id, user, active
            FROM user_session
            WHERE token = :token
        ');

        $result = $prepare->execute([
            'token' => $token,
        ]);

        if ($prepare->rowCount() === 0) {
            return [ 'error' => 'Token not found', ];
        }

        $user_session = $prepare->fetch();

        if ((bool) $user_session['active'] === false) {
            return [ 'error' => 'Token is not active' ];
        }

        $this->user = [
            'id' => $user_session['user'],
        ];

        $this->user_session = [
            'id' => $user_session['id'],
            'token' => $token,
            'active' => (bool) $user_session['active'],
        ];

        return true;
    }

    function disableToken($token){
        $prepare = $this->link->prepare('
            UPDATE user_session
            SET active = 0
            WHERE token = :token'
        );

        $result = $prepare->execute([
            'token' => $token,
        ]);

        $this->user = null;
        $this->user_session = null;

        return true;
    }
}