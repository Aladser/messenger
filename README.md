# messenger.local
**Месенджер**

Особенности:
* Часть индексов - первичные ключи. В основном запросы используют первичные ключи в условиях where
* создание чатов контактов и групповых чатов, добавление сообщений в БД совершается через процедуры
* Изменения исходного макета:
  + сообщения контакта окрашены в серый цвет
  + кнопка поиска пользователей
  + полоса прокрутки сообщений, контактов и групповых чатов
  + над кнопкой отправки сообщения пишутся сообщения о состоянии подключений пользователей к серверу
  + линия, отделяющая контакты от групповых чатов, имеет отступы по горизонтали
  + в заголовке чата выделено жирным название пользователя, с кем открыт диалог, или группового чата
* В качестве асинхронного общения между клиентом и сервером используются вебсокеты и библиотека PHP Ratchet
* Соединения клиентов добавляются в БД таблицу соединений connections
* Вебсокет работает как служба-отдельное приложение. Это не требует постоянного перезапуска вебсокета при открытии сайта
* пользователь в БД ищется по почте или никнейму, полученных от клиента
* если вебсокет недоступен, то перед отправкой сообщения вылезет предупреждение

Используемые модули:
* Open Server Panel
* PHP 8.1
* MySQL
* phpmailer
* ratchet

![Окно чатов](/application/images/demo2.png)
