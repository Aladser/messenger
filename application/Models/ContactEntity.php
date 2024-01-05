<?php

namespace App\Models;

use App\Core\Model;

/** Контакт пользователя */
class ContactEntity extends Model
{
    public function get($userId, $contactId)
    {
        $sql = "
                select chat_id, user_id, user_photo,
                getPublicUserName(user_email, user_nickname, user_hide_email) as username, 
                (
                    select chat_participant_isnotice
                    from chat_participant
                    where chat_participant_chatid = chat_id 
                    and chat_participant_userid = :userId
                ) as isnotice
                from chat 
                join chat_participant on chat_participant_chatid = chat_id
                join users on chat_participant_userid = user_id
                where chat_type = 'dialog'
                and chat_id in (
                    select chat_participant_chatid 
                    from chat_participant 
                    where chat_participant_userid = :userId
                )
                and user_id = :contactId
            ";
        $args = ['userId' => $userId, 'contactId' => $contactId];
        $contact = $this->dbQuery->queryPrepared($sql, $args, false);

        return $contact;
    }

    public function getUserContacts(int $userId, bool $onlyId = false): array
    {
        $sql = "
            select chat_id as chat, user_id as user, photo as photo,
            getPublicUserName(email, nickname, hide_email) as username, 
            (
                select notice 
                from chat_participants 
                where chat_id = chats.id 
                and user_id = :userId
            ) as notice
            from chats 
            join chat_participants on chat_id = chats.id
            join users on user_id = users.id
            where type = 'dialog'
            and chat_id in (
                select chat_id
                from chat_participants
                where user_id = :userId)
            and user_id != :userId";
        $args = ['userId' => $userId];
        $userList = $this->dbQuery->queryPrepared($sql, $args, false);

        if ($onlyId) {
            // только ID
            $userIdList = [];
            foreach ($userList as $user) {
                $userIdList[] = $user['user_id'];
            }

            return $userIdList;
        } else {
            // полные данные
            $cleanedUserList = [];
            // удаление дублей
            foreach ($userList as $user) {
                $cleanedUserList[] = [
                    'chat' => $user['chat'],
                    'user' => $user['user'],
                    'photo' => $user['photo'],
                    'username' => $user['username'],
                    'notice' => $user['notice'],
                ];
            }

            return $cleanedUserList;
        }
    }

    public function add($chatId, $userId): bool
    {
        $participantData = ['chat_id' => $chatId, 'user_id' => $userId];
        $isAdded = $this->dbQuery->insert('chat_participants', $participantData) > 0;

        return $isAdded;
    }

    public function remove($contactId, $userId): bool
    {
        $whereCondition = '(cnt_user_id=:userId and cnt_contact_id=:contactId) 
        or (cnt_contact_id=:userId and cnt_user_id=:contactId)';
        $args = ['userId' => $userId, 'contactId' => $contactId];
        $isRemoved = $this->dbQuery->delete('contacts', $whereCondition, $args);

        return $isRemoved;
    }

    public function exists($contactId, $userId)
    {
        $sql = 'select count(*) as count from contacts 
        where cnt_user_id = :userId and cnt_contact_id = :contactId 
        or cnt_user_id = :contactId and cnt_contact_id = :userId';
        $args = ['userId' => $userId, 'contactId' => $contactId];
        $isExisted = $this->dbQuery->queryPrepared($sql, $args)['count'] > 0;

        return $isExisted;
    }
}
