# OSSを利用したサンプル
OSSをサービスのストレージの一部として利用する場合のサンプルアプリケーション。

## セットアップ
必要パッケージのインストール
```
$ sudo apt-get install sqlite3
$ sudo apt-get install php php-curl php-mbstring php-xml php-pdo-sqlite
```

アプリのセットアップ
```
$ git clone git@github.com:sbcloud/oss-usage-php-sample.git
$ cd oss-usage-php-sample
$ php composer.phar install
$ sqlite3 db/development.db < db/schema.sql
```

環境変数の設定
```
$ export 
```

アプリの起動
```
$ cd oss-usage-php-sample
$ php -S localhost:8080  // ローカル内で起動する場合
$ php -S 0.0.0.0:8080    // 公開する場合
```
