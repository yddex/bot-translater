<?php
namespace Yddex\TranslateBot\Models;
use Yddex\TranslateBot\Exceptions\UpdateException;

class Update
{
    protected bool $is_callback = false;
    protected bool $is_message = false;
    protected ?int $callback_id = null;
    protected int $update_id;
    protected string $data;
    protected int $chat_id;
    protected array $chat_info;
    protected array $original;


    public function __construct(array | \Telegram\Bot\Objects\Update $update)
    {
        $this->update_id = $update['update_id'];
        //Callback query
        if(isset($update['callback_query'])){
            $callback = $update['callback_query'];

            $this->is_callback = true;
            $this->callback_id = $callback['id'];
            $this->chat_id = $callback['message']['chat']['id'];
            $this->chat_info = $callback['message']['chat'];
            $this->data = $callback['data'];
        }
        else if(isset($update['message']) && isset($update['message']['text'])){
            $this->is_message = true;
            $this->chat_id = $update['message']['chat']['id'];
            $this->chat_info = $update['message']['chat'];
            $this->data = $update['message']['text'];
        }else{
            throw new UpdateException('Not supported format.');
        }
    }
}