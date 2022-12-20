<?php
namespace Yddex\TranslateBot\Models;



class LanguageKeyboard
{

    private const LANGS = [
        'ru'=>'Русский',
        'en'=>'Английский',
        'ar'=>'Арабский',
        'be'=>'Белорусский',
        'bg'=>'Болгарский',
        'el'=>'Греческий',
        'es'=>'Испанский',
        'it'=>'Итальянский',
        'ko'=>'Корейский',
        'de'=>'Немецкий',
        'pl'=>'Польский',
        'pt'=>'Португальский',
        'tr'=>'Турецкий',
        'cs'=>'Чешский',
        'uk'=>'Украинский',
        'fr'=>'Французский',
        'zh'=>'Китайский',
        'ja'=>'Японский'
    ];
    


    public const FROM_PREFIX = 'from';
    public const TO_PREFIX = 'to';

    private static function createKeyboard(string $currentLang, string $prefix) :array
    {
        $keyboard = [];
        $row = [];
        foreach(self::LANGS as $code => $lang){
            $text = $currentLang == $code ? '•' . $lang : $lang; 
            $button = ['text' => $text, 'callback_data' => $prefix . ':' . $code];
            //В одной строке 2 клавиши
            if(count($row) < 2)
            {
                $row[] = $button;
            }else{
                $keyboard[] = $row;
                $row = [];
            }
        }

        return $keyboard;
    }

    /**
     * Get inline keyboard of translating language
     * @param string $currentLang current selected language code
     * @return array
     */
    static function inlineFromKeyboard(string $currentLang) :array
    {
        return self::createKeyboard($currentLang, self::FROM_PREFIX);
    }

    /**
     * Get inline keyboard of language for translate
     * @param string $currentLang current selected language code
     * @return array
     */
    static function inlineToKeyboard(string $currentLang) :array
    {
        return self::createKeyboard($currentLang, self::TO_PREFIX);
    }

    static function getLanguageByCode(string $code)
    {
        return self::LANGS[$code] ?? '';
    }
}
