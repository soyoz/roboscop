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
require_once 'Zend/Db/Table/Abstract.php';

class Service_Feed extends Zend_Db_Table_Abstract
{
    protected $_name = 'SERVICE_FEED';
    protected $_primary = 'FEED_ID';

    /**
     * Feed servisi abonelerinin, takip ettigi RSS'lerdeki son bilgileri import eder.
     *
     * @return void
     */
    public function import()
    {
        $getFeedList = $this->fetchAll();

        if (sizeof($getFeedList) >= 1) {
            $Service_Feed_Import = new Service_Feed_Import ();
            foreach ($getFeedList as $feedValue) {
                $checkFeedIsValid = Soyoz_Microsoft_MSN_Adapter_Command_Feed_Validator::check($feedValue->URL);

                if ($checkFeedIsValid) {
                    /**
                     * Eger feed'in statusu aktif ise,
                     *
                     * Diger statusler, feed'e erisim olmayabilir, feed'in RSS XML yapisi bozuk oldugu icin
                     * yoneticiler tarafindan pasif duruma getirilmis olabilir v.b
                     */
                    if ($feedValue->STATUS == Soyoz_Microsoft_MSN_Adapter_Command_Feed::$feedStatus ['aktif']) {
                        $this->update(array('STATUS' => Soyoz_Microsoft_MSN_Adapter_Command_Feed::$feedStatus ['aktif'], 'LAST_UPDATE_DATE' => time()), array('FEED_ID = ' . $feedValue->FEED_ID));
                        $parseFeed = Soyoz_Microsoft_MSN_Adapter_Command_Feed_Parser::parse($feedValue->URL);

                        if (isset ($parseFeed ['items'])) {
                            foreach ($parseFeed ['items'] as $value) {
                                $checkImportLinkIsExists = $Service_Feed_Import->fetchRow(array('FEED_ID = ' . $feedValue->FEED_ID, 'LINK = "' . addslashes($value ['link']) . '"'));

                                if (sizeof($checkImportLinkIsExists) <= 0) {
                                    $Service_Feed_Import->insert(array('FEED_ID' => $feedValue->FEED_ID, 'TITLE' => addslashes($value ['title']), 'LINK' => $value ['link'], 'DESCRIPTION' => $value ['description'], 'INSERT_DATE' => time()));
                                }
                            }
                        }
                    }
                } else {
                    $this->update(array('STATUS' => Soyoz_Microsoft_MSN_Adapter_Command_Feed::$feedStatus ['kırık link'], 'LAST_UPDATE_DATE' => time()), array('FEED_ID = ' . $feedValue->FEED_ID));
                }
            }
            unset ($Service_Feed_Import);
        }
    }

    /**
     * Import edilen RSS'lerde, RSS aboneleri icin bir yenilik var mi kontrol eder.
     * Eger var ise, notification tablosuna kayit girer.
     *
     * @return void
     */
    public function check()
    {
        /**
         * Durumu aktif olan abonelikleri getir
         */
        $Service_Feed_Subscription = new Service_Feed_Subscription ();
        $getFeedSubscriptionList = $Service_Feed_Subscription->getListByStatus(Soyoz_Microsoft_MSN_Adapter_Command_Feed::$feedSubscriptionStatus ['aktif']);
        unset ($Service_Feed_Subscription);

        if ($getFeedSubscriptionList) {
            $Service_Feed_Notification = new Service_Feed_Notification ();
            $Service_Feed_Import = new Service_Feed_Import ();
            $System_Notification = new System_Notification ();

            foreach ($getFeedSubscriptionList as $value) {
                /**
                 * Abonelerin takip ettigi feed importlarinin listesini al
                 */
                $getFeedImportList = $Service_Feed_Import->fetchAll(array('FEED_ID = ' . $value ['FEED_ID']));

                if (sizeof($getFeedImportList) >= 1) {
                    foreach ($getFeedImportList as $feedImportListValue) {
                        /**
                         * Feed import tarihi, kullanicinin abonelik bilgilerini guncellemesinden sonra ise,
                         * notification'a yaz. Eger degilse, yazma.
                         *
                         * Bu sekilde, kullanicinin pasif durumdayken import edilen feed'leri, kullaniciya
                         * gonderilmez.
                         */
                        if ($feedImportListValue->INSERT_DATE >= $value ['LAST_UPDATE_DATE']) {
                            $checkFeedImportListIsExists = $Service_Feed_Notification->fetchRow(array('FEED_IMPORT_ID = "' . $feedImportListValue->FEED_IMPORT_ID . '"', 'BUDDY_ID = ' . $value ['BUDDY_ID']));

                            if (sizeof($checkFeedImportListIsExists) <= 0) {
                                $message = Soyoz_Microsoft_MSN_Adapter_Command_Feed::setMessage($feedImportListValue->INSERT_DATE, $value ['SF_URL'], $feedImportListValue->TITLE, $feedImportListValue->LINK);

                                $Service_Feed_Notification->insert(array('FEED_IMPORT_ID' => $feedImportListValue->FEED_IMPORT_ID, 'BUDDY_ID' => $value ['BUDDY_ID'], 'INSERT_DATE' => time()));

                                $System_Notification->insert(array('SERVICE_ID' => 1, 'BUDDY_ID' => $value ['BUDDY_ID'], 'MESSAGE' => $message, 'INSERT_DATE' => time(), 'IS_READ' => 0));
                            }
                        }
                    }
                }
            }
            unset ($System_Notification);
            unset ($Service_Feed_Import);
            unset ($Service_Feed_Notification);
        }
    }

    /**
     * Feed servisi ile http://soyoz.com/blog RSS'ine abone olmayan kullanicilar
     * icin otomatik olarak abonelik baslatir.
     *
     * @return void
     */
    public function autoSubscription()
    {
        $MSN_Buddy = new MSN_Buddy ();
        $getBuddyList = $MSN_Buddy->fetchAll();
        unset ($MSN_Buddy);

        if (sizeof($getBuddyList) >= 1) {
            $Service_Feed_Subscription = new Service_Feed_Subscription ();
            foreach ($getBuddyList as $value) {
                $checkSubscriptionIsExists = $Service_Feed_Subscription->fetchRow(array('FEED_ID = 1', 'BUDDY_ID = ' . $value->BUDDY_ID));

                if (sizeof($checkSubscriptionIsExists) <= 0) {
                    $Service_Feed_Subscription->insert(array('FEED_ID' => 1, 'BUDDY_ID' => $value->BUDDY_ID, 'INSERT_DATE' => time(), 'LAST_UPDATE_DATE' => time(), 'STATUS' => Soyoz_Microsoft_MSN_Adapter_Command_Feed::$feedSubscriptionStatus ['aktif']));
                }
            }
        }
    }
}

?>