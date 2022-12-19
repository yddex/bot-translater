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
    protected array $chat;
    protected array $original;


    public function __construct(array $update)
    {
        $this->update_id = $update['update_id'];
        //Callback query
        if(isset($update['callback_query'])){
            $callback = $update['callback_query'];

            $this->is_callback = true;
            $this->callback_id = $callback['id'];
            $this->chat = $callback['message']['chat'];
            $this->data = $callback['data'];
        }
        else if(isset($update['message']) && isset($update['message']['text'])){
            $this->is_message = true;
            $this->chat = $update['message']['chat'];
            $this->data = $update['message']['text'];
        }else{
            throw new UpdateException('Not supported format');
        }
    }
}