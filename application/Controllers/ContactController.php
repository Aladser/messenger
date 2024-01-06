<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ChatEntity;
use App\Models\ContactEntity;
use App\Models\GroupContactEntity;
use App\Models\MessageEntity;
use App\Models\UserEntity;

/** контроллер контактов */
class ContactController extends Controller
{
    private ContactEntity $contacts;
    private GroupContactEntity $groupContacts;
    private MessageEntity $messages;
    private UserEntity $users;
    private ChatEntity $chats;

    private string $authUserEmail;
    private int $authUserId;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserEntity();
        $this->contacts = new ContactEntity();
        $this->groupContacts = new GroupContactEntity();
        $this->messages = new MessageEntity();
        $this->authUserEmail = UserController::getAuthUserEmail();
        $this->authUserId = $this->users->getIdByName($this->authUserEmail);
        $this->chats = new ChatEntity();
    }

    public function getContact()
    {
        $contact = htmlspecialchars($_POST['contact']);
        $contactId = $this->users->getIdByName($contact);

        // добавляется контакт, если не существует
        $isContact = $this->contacts->exists($contactId, $this->authUserId);
        if (!$isContact) {
            $this->contacts->add($contactId, $this->authUserId);
            $chatId = $this->messages->getDialogId($this->authUserId, $contactId);
            $contactName = $this->users->getPublicUsername($contactId);
            $userData = ['username' => $contactName, 'chat_id' => $chatId, 'isnotice' => 1];
        } else {
            $contact = $this->contacts->get($this->authUserId, $contactId);
            $userData = [
                'username' => $contact[0]['username'],
                'chat_id' => $contact[0]['chat_id'],
                'isnotice' => $contact[0]['isnotice'],
            ];
        }
        echo json_encode($userData);
    }

    public function getContacts()
    {
        $userContacts = $this->contacts->getUserContacts($this->authUserId);
        for ($i = 0; $i < count($userContacts); ++$i) {
            $userContacts[$i]['photo'] = $this->getAvatarImagePath($userContacts[$i]['photo']);
        }
        echo json_encode($userContacts);
    }

    public function createGroupContact()
    {
        $discussionId = htmlspecialchars($_POST['discussionid']);
        $username = htmlspecialchars($_POST['username']);
        $userId = $this->users->getIdByName($username);

        $gcExisted = $this->groupContacts->exists($discussionId, $userId);
        if (!$gcExisted) {
            $isAdded = (int) $this->groupContacts->add($discussionId, $userId);
        } else {
            $isAdded = 1;
        }

        echo json_encode([
            'result' => $isAdded,
            'group' => 'group-'.$discussionId,
            'user' => $username,
        ]);
    }

    public function getGroupContacts()
    {
        $discussionId = htmlspecialchars($_POST['discussionid']);
        $creatorId = $this->messages->getDiscussionCreatorId($discussionId);
        echo json_encode([
            'participants' => $this->contacts->getGroupContacts($discussionId),
            'creatorName' => $this->users->getPublicUsername($creatorId),
        ]);
    }

    public function exists()
    {
        $contact_name = $_POST['contact_name'];
        $contact_id = $this->users->getIdByName($contact_name);
        $chat_id = $this->chats->getDialogId($this->authUserId, $contact_id);
        var_dump($chat_id);
    }
}
