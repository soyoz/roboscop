<?php
/**
 * @author Erol Soyöz <erol@soyoz.com>
 * @copyright 2009 The Authors
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @link https://github.com/soyoz/roboscop
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Soyoz_Microsoft_MSN_Adapter_Command_Konus
{
    /**
     * @var array
     */
    public static $information = array(
        "example" => "!konus osman@hotmail.com buraya mesajını yazabilirsin.", "description" => "Roboscop Konus, Roboscop'u kullanan başka bir hesaba mesaj gönderebilmenizi sağlar. Eğer arkadaşlarınız Roboscop'u kullanıyorsa, arkadaşlarınıza mesajlarınızı Roboscop üzerinden gönderebilirsiniz."
    );

    /**
     * @param $parameter string
     * @param $messageId integer
     * @return boolean
     */
    public static function run($message)
    {
        if (isset ($message)) {
            $parameter = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getParameter($message);

            $parameter = explode(' ', $parameter);

            if ($parameter && is_array($parameter) && sizeof($parameter) >= 2) {
                $passport = $parameter [0];
                unset ($parameter [0]);

                $passportMessage = implode(' ', $parameter);

                $MSN_Buddy = new MSN_Buddy ();
                $checkPassportIsExists = $MSN_Buddy->fetchRow(array(
                    'Passport = "' . $passport . '"'
                ));
                unset ($MSN_Buddy);

                if (sizeof($checkPassportIsExists) >= 1) {
                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($passportMessage);

                    Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['passport'] = $passport;
                    Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['header'] = $header;

                    $header = "(i) Mesajını " . $passport . " MSN hesabına başarıyla gönderdim. Beni tebrik eder misin? :P";
                } else {
                    $header = ":( Mesaj göndermek istediğin " . $passport . " MSN hesabı maalesef kayıtlı değil.";
                }
            } else {
                $header = "*-) Girdiğin parametrelerin doğru olduğundan emin misin? \r\n\r\nKontrol edip tekrar deneyebilir misin ya da !yardim konus yazıp gönderirsen, sana bu servis ile ilgili detaylı bilgi verebilirim.";
            }
            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($header);
            return $header;
        }
        return false;
    }
}

?>