<?php

class Authorization {
    private $link;
    public $user = null;
    public $user_session = null;

    function __construct(PDO $link) {
        $this->link = $link;
    }

    function login ($username, $password) {
        $prepare = $this->link->prepare('
            SELECT id
            FROM user
            WHERE
                username = :username
                AND password = :password
        ');

        $result = $prepare->execute([
            'username' => $username,
            'password' => $password,
        ]);

        if ($prepare->rowCount() === 0) {
            return [ 'error' => 'User not found', ];
        }

        $user = $prepare->fetch();
        $token = '';

        $this->user = $user;
        $this->user_session = null;

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrtuvwxyz';

        for ($i = 0; $i < 20; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }

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

        return true;
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

    function logout () {
        if ($this->user_session === null) {
            return [ 'error' => 'You should call verifyToken or login first' ];
        }

        $prepare = $this->link->prepare('
            UPDATE user_session
            SET active = 0
            WHERE id = :user_session
        ');

        $result = $prepare->execute([
            'user_session' => $this->user_session['id'],
        ]);

        $this->user = null;
        $this->user_session = null;

        return true;
    }
}