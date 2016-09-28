# OSSを利用したサンプル
OSSをサービスのストレージの一部として利用する場合のサンプルアプリケーション。  
OSSにてユーザのコンテンツを管理する場合、データベースと併用して情報を管理する必要がでてくる。  
例えば、AとBというユーザがそれぞれ`test.png`というファイルをアップロードするとする。  
その２つの`test.png`は別々のものとして管理する必要がある。  
また、OSSではオブジェクトの一覧を取得することが難しい。OSSとは別にDBにオブジェクト一覧を持つ必要性などがでてくる

■確認ポイント
- ファイルをアップロードしよう
- OSS上に`user_name/file_name`の形で保存されていることを確認しよう
- データベース(sqlite)にも情報が書き込まれていることを確認しよう
- ダウンロード時は、ユーザ→サーバ→OSSの順で取得する必要がある点を確認

## 構成
- 言語: PHP7(おそらくphp5系でも動く)
- アプリ: Slim3(Templateはtwig)
- DB: sqlite3
- OS: Ubuntu16.04（検証した環境）

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
$ export ALIYUN_ACCESS_KEY=xxxxxxxxxx
$ export ALIYUN_ACCESS_SECRET=xxxxxxxxxx
$ export ALIYUN_ACCESS_REGION=xxxxxxxxxx
$ export ALIYUN_OSS_BUCKET_NAME=xxxxxxxxxx
```

アプリの起動
```
$ cd oss-usage-php-sample
$ php -S localhost:8080  // ローカル内で起動する場合
$ php -S 0.0.0.0:8080    // 公開する場合
```
