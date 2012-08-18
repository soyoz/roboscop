Roboscop
========
PHP ile MSNP9 (MSN Protocol 9) kullanılarak deneysel olarak geliştirilen, eski-nesil bir MSN robotudur. Proje, 2009'un başlarında geliştirilmeye başlanmış ve 2009'un sonlarında geliştirilmesi durdurulmuştur.

Uygulama deneysel olarak geliştirildiği için, kod dökümante edilmemiştir. Konu ile ilgilenen geliştiricilere referans olması düşünülerek, **GNU General Public License** altında Açık Kaynak olarak dağıtılmaktadır.

Nedir
========
Roboscop, tanımlanan bir veya bir den fazla hesap ile bağlanır ve bağlandığı hesap listesinde bulunan kişilere
çeşitli hizmetler verir.

Nasıl
========
Roboscop'u test etmek için aşağıdaki adımları izleyebilirsiniz;

1. Uygulamanın çalışacağı environment (production, testing, staging, development) değerini /public/.htaccess içerisinde güncelleyin.

2. Uygulamanın çalışacağı environment'a göre /application/configs/application.ini içerisinde MySQL bağlantı bilgilerini güncelleyin.

3. MySQL için /application/database/roboscop.sql dosyasını veritabanınıza import edin.

4. Çalışma zamanı kayıtlarını takip edebilmek için /var/log klasörünün erişim izinlerini 0777 olarak değiştirin.

5. Roboscop GitHub deposunun bulunduğu klasöre girin ve çalıştırın.

	$ php public/index.php --passport=roboscop.testing@hotmail.com

6. MSN hesabınıza bir IM istemcisi (Adium, Pidgin, WLM gibi) ile oturum açın ve roboscop.testing@hotmail.com adresini ekleyin.
