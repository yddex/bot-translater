### Telegram Bot Translater
<hr>

#### Стек
- PHP 8.1
- MySql
- [Telegram Bot Sdk](https://github.com/irazasyed/telegram-bot-sdk) - Библиотека для удобного взаимодействия с Telegram Bot Api
- [GoogleFreeTranslater](https://github.com/dejurin/php-google-translate-for-free) - Легкая библиотека для перевода с помощью Google посредством POST запроса.

#### Описание

Чат-бот в мессенджере Telegram для быстрого перевода предложений или слов. 

Для перевода требуется:
1. Настроить язык с которого переводить. По умолчанию - Русский
2. Настроить язык на который переводить. По умолчанию - Английский
3. Отправить слово или предложение.
4. В ответ получить перевод.

 <a href="https://imgbb.com/"><img src="https://i.ibb.co/5c1XKBh/2022-12-24-01-20-50.png" alt="2022-12-24-01-20-50" border="0" /></a>

Выбор языка осуществляется вводом команд:
`/from` - для выбора переводимого языка.

<a href="https://imgbb.com/"><img src="https://i.ibb.co/Ss2LPht/2022-12-24-01-30-38.png" alt="2022-12-24-01-30-38" border="0"></a>

`/to` - выбрать язык перевода.

<a href="https://imgbb.com/"><img src="https://i.ibb.co/dWyNwpN/2022-12-24-01-32-08.png" alt="2022-12-24-01-32-08" border="0"></a>

**Выбранные настройки запоминаются ботом, и будут использоваться при каждом переводе.**

### Как запустить?
`git clone https://github.com/yddex/bot-translater.git` - загрузить репозиторий
`composer install` - установить зависимости
Создать файл `.env`, где определить переменные: 
- `BOT_TOKEN='token'` - Токен полученный при регистрации бота в BotFather
- `DB_HOST='localhost'` - Хост для подключения к БД
- `DB_USERNAME='username'` - Логин пользователя для подключения к БД
- `DB_PASSWORD='password'` - Пароль пользователя
- `DB_NAME='database name'` - Имя базы данных

Запрос для создания таблицы.
``` sql
CREATE TABLE `chatsS` (
  `chat_id` bigint NOT NULL PRIMARY KEY UNIQUE,
  `first_name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `source_lang` varchar(2) NOT NULL DEFAULT 'ru',
  `target_lang` varchar(2) NOT NULL DEFAULT 'en',
  `register_at` timestamp NOT NULL
);
```

Запуск бота -  `php index.php`

Запустить бесконечный цикл, который будет опрашивать телеграм о новых сообщениях. 

Для использования на хостинге лучше использовать Webhooks.


