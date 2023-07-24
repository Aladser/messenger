<?php

namespace Aladser\models;

use Aladser\core\Model;

/** список сообщений чата с пользователем*/
class GetMessagesModel extends Model
{
    private $usersTable;
    private $contactsTable;
    private $messageTable;

    public function __construct($CONFIG)
    {
        $this->usersTable = $CONFIG->getUsers();
        $this->contactsTable = $CONFIG->getContacts();
        $this->messageTable = $CONFIG->getMessageDBTable();
    }

    // получить список сообщений чата
    public function run()
    {
        session_start();
        $chatId = null;
        $type = null;
        // диалоги
        if (isset($_POST['contact'])) {
            $userHostName = isset($_COOKIE['auth']) ?  $_COOKIE['email'] : $_SESSION['email'];  // имя клиента-хоста
            $userId = $this->usersTable->getUserId($userHostName);                              // id клиента-хоста
            $contactId = $this->usersTable->getUserId($_POST['contact']);                        // id клиента-контакта
            $chatId = $this->messageTable->getDialogId($userId, $contactId);
            $type = 'dialog';
        } elseif (isset($_POST['discussionid'])) {
            // групповые чаты
            $chatId = $_POST['discussionid'];
            $type = 'discussion';
        }

        $messages = ['chatId' => $chatId, 'type'=>$type, 'messages' => $this->messageTable->getMessages($chatId)];
        echo json_encode($messages);
    }
}
