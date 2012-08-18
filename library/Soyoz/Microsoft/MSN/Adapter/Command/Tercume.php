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
class Soyoz_Microsoft_MSN_Adapter_Command_Tercume
{
    /**
     * @var array
     */
    public static $information = array(
        "description" => "(i) Roboscop.Tercüme servisinin kullanımı ile ilgili detaylı bilgi için; http://labs.soyoz.com/roboscop/services/tercume"
    );

    private static $_languageList = array(
        'almanca' => 'de', 'arapca' => 'ar', 'arnavutca' => 'sq', 'bulgarca' => 'bg', 'cekoslavakca' => 'cs', 'cince' => 'zh', 'danca' => 'da', 'endonezyaca' => 'id', 'estonyaca' => 'et', 'filipince' => 'fl', 'fince' => 'fi', 'fransizca' => 'fr', 'galicyaca' => 'gl', 'hirvatca' => 'hr', 'hintce' => 'hi', 'hollandaca' => 'nl', 'ibranice' => 'iw', 'ingilizce' => 'en', 'ispanyolca' => 'es', 'isvecce' => 'sv', 'italyanca' => 'it', 'japonca' => 'ja', 'katalanca' => 'ca', 'korece' => 'ko', 'lehce' => 'pl', 'letonyaca' => 'lv', 'litvanyaca' => 'lt', 'macarca' => 'hu', 'maltaca' => 'mt', 'norvecce' => 'no', 'romence' => 'ro', 'rusca' => 'ru', 'sirpca' => 'sr', 'slovakca' => 'sk', 'slovence' => 'sl', 'tayvanca' => 'th', 'turkce' => 'tr', 'ukraynaca' => 'uk', 'vietnamca' => 'vi', 'yunanca' => 'el'
    );

    public static function run($message)
    {
        if (isset ($message)) {
            $parameter = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getParameter($message);
            /**
             * Parametreleri kontrol et
             */
            if ($parameter) {
                $parameter = preg_split("/\\s+/", trim($parameter));
                $command = trim(Soyoz_Microsoft_MSN_Adapter_Helper_Function::strtolower($parameter [0]));

                switch ($command) {
                    case 'liste' :
                        $header = self::_getLanguageListHeader();
                        break;

                    default :
                        $source = trim(Soyoz_Microsoft_MSN_Adapter_Helper_Function::strtolower($parameter [0]));
                        unset ($parameter [0]);

                        if (isset ($source) && $source) {
                            if (array_key_exists($source, self::$_languageList) || $sourceCode = Soyoz_Microsoft_MSN_Adapter_Helper_Function::arrayValueExists($source, self::$_languageList)) {
                                if (!$sourceCode) {
                                    $sourceCode = self::$_languageList [$source];
                                }
                                $destination = trim(Soyoz_Microsoft_MSN_Adapter_Helper_Function::strtolower($parameter [1]));
                                unset ($parameter [1]);

                                if (isset ($destination) && $destination) {
                                    if (array_key_exists($destination, self::$_languageList) || $destinationCode = Soyoz_Microsoft_MSN_Adapter_Helper_Function::arrayValueExists($destination, self::$_languageList)) {
                                        if (!$destinationCode) {
                                            $destinationCode = self::$_languageList [$destination];
                                        }

                                        $text = trim(implode(' ', $parameter));

                                        if (isset ($text) && $text) {
                                            if (strlen($text) <= 390) {
                                                $translate = Soyoz_Microsoft_MSN_Adapter_Command_Tercume_API::translate($sourceCode, $destinationCode, $text);

                                                $header = '(i) ' . $translate;
                                            } else {
                                                $header = ":| Uyarı: Çevirmek istediğin kelime ya da cümlenin uzunluğu en fazla 390 karakter içerebilir.\r\n\r\nSenin girdiğin kelime ya da cümle " . strlen($text) . " karakter içeriyor.";
                                            }
                                        } else {
                                            $header = ":| Uyarı: Çevirmek istediğin kelime ya da cümleyi yazmadın.";
                                        }
                                    } else {
                                        $header = ":| Uyarı: Çeviri yapmak istediğin " . $destination . " dili bulunamadı.\r\n\r\n!tercume liste komutunu göndererek, hangi diller arasında çeviri yapabileceğini öğrenebilirsin.";
                                    }
                                } else {
                                    $header = ":| Uyarı: Çevirmek istediğin dili yazmadın.";
                                }
                            } else {
                                $header = ":| Uyarı: Çevirmek istediğin " . $source . " dili bulunamadı.\r\n\r\n!tercume liste komutunu göndererek, hangi diller arasında çeviri yapabileceğini öğrenebilirsin.";
                            }
                        } else {
                            $header = ":| Uyarı: Çeviri yapmak istediğin dili yazmadın.";
                        }
                        break;
                }
            } else {
                $example = @Soyoz_Microsoft_MSN_Adapter_Command_Tercume::$information ['example'];
                $description = @Soyoz_Microsoft_MSN_Adapter_Command_Tercume::$information ['description'];

                $header = $description . "\r\n\r\n" . $example;
            }
            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($header);

            return $header;
        } else {
            return false;
        }
    }

    /**
     * @return string | boolean
     */
    private static function _getLanguageListHeader()
    {
        if (isset (self::$_languageList) && is_array(self::$_languageList)) {
            $header = null;
            foreach (self::$_languageList as $key => $value) {
                $value;

                $header = $header . "(*) " . $key . "\r\n";
            }
            return $header;
        } else {
            return false;
        }
    }
}

?>