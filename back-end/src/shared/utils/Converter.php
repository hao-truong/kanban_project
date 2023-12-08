<?php
declare(strict_types=1);

namespace shared\utils;

class Converter
{
    /**
     * @param array $card
     * @return array
     */
    public static function toCardResponse(array $card): array
    {
        if ($card['assigned_user']) {
            $assigned_user = [
                'id'       => $card['assigned_user_id'],
                'username' => $card['assigned_user_username'],
                'alias'    => $card['assigned_user_alias'],
                'email'    => $card['assigned_user_email'],
            ];
            $card["assigned_user"] = $assigned_user;
        }
        unset($card['assigned_user_id']);
        unset($card['assigned_user_username']);
        unset($card['assigned_user_alias']);
        unset($card['assigned_user_email']);
        return $card;
    }
}
