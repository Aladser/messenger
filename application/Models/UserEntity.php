<?php

namespace App\Models;

use App\Core\Model;

/** класс БД таблицы пользователей */
class UserEntity extends Model
{
    // Проверка существования значения
    public function exists($field, $value)
    {
        $sql = "select count(*) as count from users where $field = :value";
        $args = ['value' => $value];

        return $this->dbQuery->queryPrepared($sql, $args)['count'] > 0;
    }

    // Поле строки таблицы
    public function get($email, $field): mixed
    {
        $sql = "select $field from users where user_email = :email";
        $args = ['email' => $email];

        return $this->dbQuery->queryPrepared($sql, $args)[$field];
    }

    // Получить ID пользователя
    public function getIdByName(string $publicUsername): int
    {
        $sql = 'select user_id from users 
                where user_email = :publicUsername or user_nickname=:publicUsername';
        $args = ['publicUsername' => $publicUsername];
        $id = $this->dbQuery->queryPrepared($sql, $args)['user_id'];

        return $id;
    }

    // Получить публичное имя пользователя
    public function getPublicUsername(int $userId)
    {
        $sql = '
            select getPublicUserName(user_email, user_nickname, user_hide_email) as username 
            from users where user_id = :userId';
        $args = ['userId' => $userId];
        $username = $this->dbQuery->queryPrepared($sql, $args)['username'];

        return $username;
    }

    // Проверка авторизации
    public function verify($email, $password): bool
    {
        $sql = 'select user_password from users where user_email=:email';
        $args = ['email' => $email];
        $passHash = $this->dbQuery->queryPrepared($sql, $args)['user_password'];

        return password_verify($password, $passHash) == 1;
    }

    // добавить нового пользователя
    public function add($email, $password): bool
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $fields = ['user_email' => $email, 'user_password' => $password];
        $userId = $this->dbQuery->insert('users', $fields);

        return $userId;
    }

    // добавить хэш пользователю
    public function addUserHash($email, $hash)
    {
        $sql = "update users set user_hash='$hash' where user_email='$email'";

        return $this->dbQuery->exec($sql);
    }

    // проверить хэш пользователя
    public function checkUserHash($email, $hash): bool
    {
        $sql = 'select count(*) as count from users where user_email = :email and user_hash = :hash';

        return $this->dbQuery->queryPrepared($sql, ['email' => $email, 'hash' => $hash])['count'] === 1;
    }

    /** подтвердить почту */
    public function confirmEmail($email)
    {
        $sql = "update users set user_email_confirmed = 1, user_hash = null where user_email='$email'";

        return $this->dbQuery->exec($sql);
    }

    // список пользователей по шаблону почты или никнейма
    public function getUsers($phrase, $email)
    {
        $phrase = "%$phrase%";
        // список пользователей, подходящие по шаблону
        $sql = '
            select user_id as user, user_nickname as name, user_photo as photo 
            from users 
            where user_nickname  != \'\' and user_nickname is not null 
            and user_email != :email and user_nickname  like :phrase
            and user_email not in (select * from unhidden_emails where user_email  like :phrase)
            union 
            select user_id as user, user_email as name, user_photo as photo 
            from users 
            where user_hide_email  = 0 and user_email != :email and user_email like :phrase;
        ';
        $args = ['email' => $email, 'phrase' => $phrase];

        return $this->dbQuery->queryPrepared($sql, $args, false);
    }

    // изменить пользовательские данные в Бд
    public function setUserData($data): bool
    {
        $rslt = false;
        $email = $data['user_email'];

        // запись никнейма
        $nickname = $data['user_nickname'];
        $rslt |= $this->isEqualData($nickname, 'user_nickname', $email) ?
            true :
            $this->dbQuery->exec("update users set user_nickname = '$nickname' where user_email='$email'");

        // запись скрытия почты
        $hideEmail = $data['user_hide_email'];
        $rslt |= $this->isEqualData($hideEmail, 'user_hide_email', $email) ?
            true :
            $this->dbQuery->exec("update users set user_hide_email = '$hideEmail' where user_email='$email'");

        // запись фото
        $photo = $data['user_photo'];
        $rslt |= $this->isEqualData($photo, 'user_photo', $email) ?
            true :
            $this->dbQuery->exec("update users set user_photo = '$photo' where user_email='$email'");

        return $rslt;
    }

    // сравнение новых данных и в БД
    private function isEqualData($data, $field, $email): bool
    {
        $sql = "select $field from users WHERE user_email=:email";
        $args = ['email' => $email];
        $dbData = $this->dbQuery->queryPrepared($sql, $args)[$field];

        return $data === $dbData;
    }
}
