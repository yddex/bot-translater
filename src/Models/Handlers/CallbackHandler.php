<?php
namespace Yddex\TranslateBot\Models\Handlers;

use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Objects\BaseObject;
use Telegram\Bot\Objects\Chat;
use Yddex\TranslateBot\Models\LanguageKeyboard;

class CallbackHandler extends Handler
{

    private const ALREADY_SELECT_ANSWER = 'Язык уже выбран!';

    private const SUCCESS_ANSWER = 'Окей, я сохранил!';


    public function handle(BaseObject $update)
    {
        $chat = $this->getChat($update['callback_query']['message']['chat']);
        $callback_id = $update['callback_query']['id'];
        $message_id = $update['callback_query']['message']['message_id'];

        [$prefix, $lang] = explode(':', $update['callback_query']['data']);

        //Проверяем префикс
        if ($prefix == LanguageKeyboard::FROM_PREFIX) {
            //Если выбран текущий язык язык
            if ($chat->getSourceLang() == $lang) {
                return $this->api->answerCallbackQuery([
                    'callback_query_id' => $callback_id,
                    'text' => self::ALREADY_SELECT_ANSWER,
                ]);
            }
            //Обновление в бд
            $chat->setSourceLang($lang);
            $this->chatRepository->updateLang($chat);

            //Отправляем сообщение, что коллбек обработан успешно

            $this->api->answerCallbackQuery([
                'callback_query_id' => $callback_id,
                'text' => self::SUCCESS_ANSWER
            ]);




            //Изменяем текущую клавиатуру
            return $this->editMessageReplyMarkup(
                $chat->getChatId(),
                $message_id,
                LanguageKeyboard::inlineFromKeyboard($lang)
            );

        } else if ($prefix == LanguageKeyboard::TO_PREFIX) {
            if ($chat->getTargetLang() == $lang) {
                return $this->api->answerCallbackQuery([
                    'callback_query_id' => $callback_id,
                    'text' => self::ALREADY_SELECT_ANSWER,
                ]);
            }

            $chat->setTargetLang($lang);
            $this->chatRepository->updateLang($chat);


            $this->api->answerCallbackQuery([
                'callback_query_id' => $callback_id,
                'text' => self::SUCCESS_ANSWER
            ]);


            return $this->editMessageReplyMarkup(
                $chat->getChatId(),
                $message_id,
                LanguageKeyboard::inlineToKeyboard($lang)
            );

        }

    }

    private function editMessageReplyMarkup(int $chat_id, int $message_id, array $keyboard)
    {
        $params = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $this->api->replyKeyboardMarkup([
                'inline_keyboard' => $keyboard
            ])
        ];
        return $this->api->anySendRequest('editMessageReplyMarkup', $params);
    }


}