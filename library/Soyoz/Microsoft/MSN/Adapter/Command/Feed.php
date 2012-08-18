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
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Adapter_Command_Feed_Validator');

class Soyoz_Microsoft_MSN_Adapter_Command_Feed
{
    /**
     * @var array
     */
    public static $information = array("description" => "(i) Roboscop.Feed servisinin kullanımı ve komutları ile ilgili detaylı bilgi için; http://labs.soyoz.com/roboscop/services/feed");

    /**
     * @var array
     */
    private static $_allowedCommandParameters = array('pasif' => false, 'aktif' => false, 'liste' => false, 'yeni' => false);

    /**
     * @var array
     */
    public static $feedSubscriptionStatus = array('aktif' => 10, 'pasif' => 20, 'kırık link' => 30);

    /**
     * @var array
     */
    public static $feedStatus = array('aktif' => 10, 'pasif' => 20);

    /**
     * @var array
     */
    public static $feedSubscriptionStatusValue = array(10 => 'aktif', 20 => 'pasif', 'kırık link' => 30);

    /**
     * @param $passport string
     * @param $screenName string
     * @param $message string
     * @param $messageId string
     * @return string | boolean
     */
    public static function run($robotId, $passport, $screenName, $message)
    {
        if (isset ($robotId) && isset ($passport) && isset ($screenName) && isset ($message)) {
            $parameter = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getParameter($message);

            /**
             * Parametreleri kontrol et
             */
            if ($parameter) {
                $parameter = preg_split("/\\s+/", trim($parameter));

                $command = trim(Soyoz_Microsoft_MSN_Adapter_Helper_Function::strtolower($parameter [0]));
                /**
                 * Status parametresi kurallara uygun mu?
                 */
                if (array_key_exists($command, self::$_allowedCommandParameters)) {
                    $MSN_Buddy = new MSN_Buddy ();
                    $checkBuddyIsExists = $MSN_Buddy->fetchRow(array('ROBOT_ID = ' . $robotId, 'PASSPORT = "' . $passport . '"'));

                    if (sizeof($checkBuddyIsExists) >= 1) {
                        $buddyId = $checkBuddyIsExists->BUDDY_ID;

                        /**
                         * Status headerlarini tanimla
                         */
                        switch ($command) {
                            case 'aktif' :
                                $statusFooter = 'Feed aboneliği (RSS, Atom) üzerinde yeni bir bilgi tespit ettiğim anda seni bilgilendireceğim.';
                                break;

                            case 'pasif' :
                                $statusFooter = 'Bu feed aboneliği için bilgilendirme yapmayacağım.';
                                break;
                        }

                        /**
                         * Status'e gore isleme devam et
                         */
                        switch ($command) {
                            /**
                             * 1 parametre gelmek zorundadir. Gelen parametre feedId'yi kapsar.
                             *
                             * @example !feed aktif feed-numarasi
                             */
                            case 'aktif' :
                                $feedSubscriptionId = $parameter [1];
                                $status = $command;
                                $header = self::setFeedStatus($feedSubscriptionId, $buddyId, $status, $statusFooter);
                                break;

                            /**
                             * 1 parametre gelmek zorundadir. Gelen parametre feedId'yi kapsar.
                             *
                             * @example !feed pasif feed-numarasi
                             */
                            case 'pasif' :
                                $feedSubscriptionId = $parameter [1];
                                $status = $command;
                                $header = self::setFeedStatus($feedSubscriptionId, $buddyId, $status, $statusFooter);
                                break;

                            /**
                             * Parametre gerekmez. Feed listesini verir.
                             *
                             * @example !feed liste
                             */
                            case 'liste' :
                                $header = self::_getFeedSubscriptionListHeader($buddyId);
                                break;

                            /**
                             * 1 parametre gelmek zorundadir. Gelen parametre feed URL'ini kapsar.
                             *
                             * @example !feed yeni feed-url
                             */
                            case 'yeni' :
                                $feedURL = $parameter [1];
                                $header = self::addFeed($feedURL, $buddyId);
                                break;
                        }
                    }
                    unset ($MSN_Buddy);
                } else {
                    $header = ":| Uyarı: \"" . $command . "\" böyle bir parametre bulunamadı. Daha fazla yardım almak için !feed komutunu gönderebilirsiniz.";
                }
            } else {
                $example = @Soyoz_Microsoft_MSN_Adapter_Command_Feed::$information ['example'];
                $description = Soyoz_Microsoft_MSN_Adapter_Command_Feed::$information ['description'];

                $header = $description . "\r\n\r\n" . $example;
            }
            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($header);

            return $header;
        } else {
            return false;
        }
    }

    /**
     * @param $feedId integer
     * @param $buddyId integer
     * @param $status string
     * @param $header string
     * @return string
     */
    private static function setFeedStatus($feedSubscriptionId, $buddyId, $status, $footer)
    {
        if (isset ($feedSubscriptionId) && isset ($buddyId) && isset ($status) && isset ($footer)) {
            $feedSubscriptionId = intval($feedSubscriptionId);
            $feedSubscriptionStatus = self::$feedSubscriptionStatus [$status];

            $Service_Feed_Subscription = new Service_Feed_Subscription ();
            $getFeedSubscriptionList = $Service_Feed_Subscription->fetchAll(array('STATUS = ' . self::$feedSubscriptionStatus ['aktif'], 'BUDDY_ID = ' . $buddyId));

            /**
             * Eger aktif 3 feed'den fazla aboneligi var ise, uyarı ver ve aktif aboneliklerini gostererek
             * herhangi bir tanesini isterse pasif yapmasini sagla.
             */
            if ($feedSubscriptionStatus == 10 && sizeof($getFeedSubscriptionList) >= 3) {
                $getFeedSubscriptionListHeader = self::_getFeedSubscriptionListHeader($buddyId);

                if ($getFeedSubscriptionListHeader) {
                    $feedSubscriptionList = $getFeedSubscriptionListHeader;
                } else {
                    $feedSubscriptionList = null;
                }
                $header = ":| Uyarı: Aktif olarak en fazla 3 feed aboneliği edinebilirsin.\r\n(i) Eklediğin diğer aktif durumdaki feed aboneliklerinden herhangi birisinin durumunu pasif hale getirerek, yeni bir feed aboneliği ekleyebilirsin.\r\n\r\n(i) Aktif Feed Abonelikleri Listesi:\r\n" . $feedSubscriptionList;
            } else {
                /**
                 * Girilen feed abone numarasi kayitli mi kontrol et
                 */
                $checkFeedSubscriptionIsExists = $Service_Feed_Subscription->fetchRow(array('FEED_SUBSCRIPTION_ID = ' . $feedSubscriptionId, 'BUDDY_ID = ' . $buddyId));

                if ($checkFeedSubscriptionIsExists) {
                    /**
                     * Guncellenmek istenen feed aboneliginin status'u, yeni status ile ayni ise.
                     */
                    if ($checkFeedSubscriptionIsExists->STATUS == $feedSubscriptionStatus) {
                        $header = ":| Uyarı: Bu feed aboneliğinin durumu şu anda \"" . $status . "\" olarak kayıtlı zaten. *-)";
                    } else {
                        $Service_Feed_Subscription->update(array('STATUS' => $feedSubscriptionStatus, 'LAST_UPDATE_DATE' => time()), array('FEED_SUBSCRIPTION_ID = ' . $feedSubscriptionId, 'BUDDY_ID = ' . $buddyId));
                        $header = "(i) Bilgi: \"" . $feedSubscriptionId . "\" numaralı feed aboneliğinin durumunu \"" . $status . "\" olarak değiştirdin.\r\n\r\n(*) " . $footer;
                    }
                } else {
                    $header = ":| Uyarı: \"" . ucwords($status) . "\" duruma getirmek istediğin \"" . $feedSubscriptionId . "\" numaralı feed aboneliği kayıtlı değil.\r\n\r\n(i) !feed liste yazarak kayıtlı feed aboneliklerinin numaralarını öğrenebilirsin.";
                }
                unset ($Service_Feed_Subscription);
            }
        } else {
            $header = ":| Uyarı: \"" . $status . "\" hale getirmek istediğin feed aboneliğinin numarasını girmedin.\r\n\r\n(i) !feed liste yazarak kayıtlı feed aboneliklerinin numaralarını öğrenebilirsin.";
        }
        return $header;
    }

    /**
     * @param $url string
     * @param $buddyId integer
     * @param $status integer
     * @return string
     */
    private static function addFeed($url, $buddyId)
    {
        if (isset ($url) && isset ($buddyId)) {
            /**
             * Durumu aktif olan aboneliklerin listesini getir
             *
             * Eger aktif 3 feed'den fazla aboneligi var ise, bilgi ver.
             */
            $Service_Feed_Subscription = new Service_Feed_Subscription ();
            $getFeedSubscriptionList = $Service_Feed_Subscription->fetchAll(array('STATUS = ' . self::$feedSubscriptionStatus ['aktif'], 'BUDDY_ID = ' . $buddyId));
            unset ($Service_Feed_Subscription);

            /**
             * Eger aktif 3 feed'den fazla aboneligi var ise, uyarı ver ve aktif aboneliklerini gostererek
             * herhangi bir tanesini isterse pasif yapmasini sagla.
             */
            if (sizeof($getFeedSubscriptionList) >= 3) {
                $getFeedSubscriptionListHeader = self::_getFeedSubscriptionListHeader($buddyId);

                if ($getFeedSubscriptionListHeader) {
                    $feedSubscriptionList = $getFeedSubscriptionListHeader;
                } else {
                    $feedSubscriptionList = null;
                }
                $header = ":| Uyarı: Aktif olarak en fazla 3 feed aboneliği edinebilirsin.\r\n(i) Eklediğin diğer aktif durumdaki feed aboneliklerinden herhangi birisinin durumunu pasif hale getirerek, yeni bir feed aboneliği ekleyebilirsin.\r\n\r\n(i) Aktif Feed Abonelikleri Listesi:\r\n" . $feedSubscriptionList;
            } else {
                $checkFeedIsValid = Soyoz_Microsoft_MSN_Adapter_Command_Feed_Validator::check($url);
                Soyoz_Microsoft_MSN_Adapter_Log::write('URL Validating ...', array('type' => 'RECV'));

                if ($checkFeedIsValid) {
                    $url = $checkFeedIsValid;
                    /**
                     * Feed var mi kontrol et
                     */
                    $Service_Feed = new Service_Feed ();
                    $checkFeedIsExists = $Service_Feed->fetchRow(array('URL = "' . $url . '"'));
                    unset ($Service_Feed);

                    if (sizeof($checkFeedIsExists) >= 1) {
                        $feedId = $checkFeedIsExists->FEED_ID;
                    } else {
                        $Service_Feed = new Service_Feed ();
                        $feedId = $Service_Feed->insert(array('URL' => $url, 'INSERT_DATE' => time(), 'STATUS' => self::$feedStatus ['aktif']));
                        unset ($Service_Feed);
                    }
                    /**
                     * Feed uzerinde bir aboneligi var mi kontrol et
                     */
                    $Service_Feed_Subscription = new Service_Feed_Subscription ();
                    $checkFeedSubscriptionIsExists = $Service_Feed_Subscription->fetchRow(array('FEED_ID = ' . $feedId, 'BUDDY_ID = ' . $buddyId));
                    unset ($Service_Feed_Subscription);

                    if (sizeof($checkFeedSubscriptionIsExists) >= 1) {
                        $getFeedSubscriptionListHeader = self::_getFeedSubscriptionListHeader($buddyId);

                        if ($getFeedSubscriptionListHeader) {
                            $feedSubscriptionList = $getFeedSubscriptionListHeader;
                        } else {
                            $feedSubscriptionList = null;
                        }
                        $header = ":| Uyarı: Bu feed URL'i için daha önce bir aboneliğin olduğu tespit edildi, bunu sende aşağıda görebilirsin:\r\n\r\n" . $feedSubscriptionList;
                    } else {
                        $Service_Feed_Subscription = new Service_Feed_Subscription ();
                        $feedSubscriptionId = $Service_Feed_Subscription->insert(array('FEED_ID' => $feedId, 'BUDDY_ID' => $buddyId, 'INSERT_DATE' => time(), 'LAST_UPDATE_DATE' => time(), 'STATUS' => self::$feedStatus ['aktif']));
                        unset ($Service_Feed);

                        /**
                         * Eger kayit basariliysa
                         */
                        if ($feedSubscriptionId) {
                            $parseFeedList = Soyoz_Microsoft_MSN_Adapter_Command_Feed_Parser::parse($url);

                            if (isset ($parseFeedList ['items'])) {
                                $Service_Feed_Import = new Service_Feed_Import ();
                                foreach ($parseFeedList ['items'] as $value) {
                                    $checkIsExists = $Service_Feed_Import->fetchRow(array('FEED_ID = ' . $feedId, 'LINK = "' . addslashes($value ['link']) . '"'));

                                    if (sizeof($checkIsExists) <= 0) {
                                        $Service_Feed_Import->insert(array('FEED_ID' => $feedId, 'LINK' => $value ['link'], 'TITLE' => $value ['title'], 'DESCRIPTION' => $value ['description'], 'INSERT_DATE' => strtotime('-1 hours', time())));
                                    }
                                }
                                unset ($Service_Feed_Import);
                            }
                            $header = "(i) Bilgi: Takip etmek istediğin feed'in URL'i ( " . $url . " ) başarıyla kaydedildi.\r\n\r\n(*) Feed üzerinde yeni bir bilgi tespit ettiğim anda seni bilgilendireceğim.";
                        } else {
                            $header = ":| Uyarı: Üzgünüm, takip etmek istediğin feed bilgilerinin kayıt aşamasında bir sorun ile karşılaşıldı.\r\n\r\n(i) Lütfen daha sonra tekrar deneyebilir misin?";
                        }
                    }
                } else {
                    $header = ":| Uyarı: Maalesef takip etmek istediğin feed ( " . $url . " ), doğrulama kontrolünden geçemedi. \r\n\r\n(i) Yanlış ya da geçersiz bir feed URL girmiş olabilirsin, lütfen kontrol edip tekrar deneyebilir misin?";
                }
            }
        } else {
            $header = ":| Uyarı: Eklemek istediğin feed'in URL'ini girmedin.";
        }
        return $header;
    }

    /**
     * @param $date integer
     * @param $url string
     * @param $title string
     * @param $link string
     * @return string | boolean
     */
    public static function setMessage($date, $url, $title, $link)
    {
        if (isset ($date) && isset ($url) && isset ($title) && isset ($link)) {
            $message = "(*) Roboscop.Feed Bilgilendirme Servisi\r\n\r\nTakip ettiğiniz " . $url . " üzerinde yeni bilgi tespit edildi.\r\n\r\nBaşlık: " . $title . "\r\nDevamı için lütfen tıklayın; " . $link;

            return $message;
        } else {
            return false;
        }
    }

    /**
     * @param integer $buddyId
     * @return array | boolean
     */
    private static function _getFeedSubscriptionList($buddyId)
    {
        if (isset ($buddyId)) {
            $Service_Feed_Subscription = new Service_Feed_Subscription ();
            $getFeedSubscriptionList = $Service_Feed_Subscription->getList($buddyId);
            unset ($Service_Feed_Subscription);

            if ($getFeedSubscriptionList) {
                return $getFeedSubscriptionList;
            }
        }
        return false;
    }

    /**
     * @param $buddyId integer
     * @param $status integer
     * @return string | boolean
     */
    private static function _getFeedSubscriptionListHeader($buddyId)
    {
        $getFeedSubscriptionList = self::_getFeedSubscriptionList($buddyId);

        if ($getFeedSubscriptionList) {
            $header = null;
            foreach ($getFeedSubscriptionList as $value) {
                $header = $header . "(*) Abone Numarası: " . $value ['FEED_SUBSCRIPTION_ID'] . " | Durum: " . self::$feedSubscriptionStatusValue [$value ['STATUS']] . " | Feed: " . $value ['SF_URL'] . " \r\n";
            }
            return $header;
        } else {
            return false;
        }
    }
}

?>