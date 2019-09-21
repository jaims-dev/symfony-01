<?php


namespace App\Security;


class TokenGenerator
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUWXYZabcdefghijklmnopqrstuwxyz0123456789';

    public function getRandomSectureToken(int $length) {
        $maxNumber = strlen(self::ALPHABET);
        $token = '';

        for($i = 0; $i < $length; $i++ ) {
            $token .= self::ALPHABET[random_int(0, $maxNumber-1)];
        }

        return $token;
    }


}