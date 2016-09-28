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
$ git clone
$ cd xxxxxx
$ php composer.phar install
$ sqlite3 db/development.db < db/schema.sql
```

環境変数の設定
```
$ export 
```
