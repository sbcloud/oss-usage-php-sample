<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config = [
  'settings' => [
    'displayErrorDetails' => true,
    'aliyun' => [
      'accessKeyId' => getenv('ALIYUN_ACCESS_KEY'),
      'accessKeySecret' => getenv('ALIYUN_ACCESS_SECRET'),
      'accessRegion' => getenv('ALIYUN_ACCESS_REGION'),
      'bucketName' => getenv('ALIYUN_OSS_BUCKET_NAME')
    ],
    'user' => 'testuser'
  ]
];
$app = new \Slim\App($config);

$container = $app->getContainer();
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('templates', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    return $view;
};

function oss() {
    global $container;
    $aliyun = $container->get('settings')['aliyun'];
    try {
        $ossClient = new \OSS\OssClient(
            $aliyun['accessKeyId'],
            $aliyun['accessKeySecret'],
            $aliyun['accessRegion']
        );
    } catch (OssException $e) {
        print $e->getMessage();
    }
    return $ossClient;
}

function db() {
    return new PDO('sqlite:db/development.db');
}

function db_execute($query, $args = array()) {
    $stmt = db()->prepare($query);
    $stmt->execute($args);
    return $stmt;
}

$app->get('/', function (Request $request, Response $response) {
    $contents = db_execute('select * from contents')->fetchAll();
    return $this->view->render($response, 'index.php', [
      'bucket' => $this->get('settings')['aliyun'],
      'contents' => $contents
    ]);
});

$app->post('/upload', function (Request $request, Response $response) {
    $user_id = $this->get('settings')['user'];
    $file = $request->getUploadedFiles()['newfile'];
    if (empty($file->file)) {
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    
    $file_name = $file->getClientFilename();
    $save_name = "{$user_id}/{$file_name}";
    db_execute(
      'insert into contents (name, save_name, user_id) values (?, ?, ?)',
      array($file_name, $save_name, $user_id)
    );
    oss()->uploadFile(
        $this->get('settings')['aliyun']['bucketName'],
        $save_name,
        $file->file
    );
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->get('/download', function (Request $request, Response $response) {
    $user_id = $this->get('settings')['user'];
    $file_name = $request->getParam('file_name');
    $file = "/tmp/${file_name}";
    
    $doesExist = oss()->doesObjectExist($this->get('settings')['aliyun']['bucketName'], "{$user_id}/${file_name}");
    if(!$doesExist) {
        return $response->withStatus(404);
    }

    $options = array(
        \OSS\OssClient::OSS_FILE_DOWNLOAD => "/tmp/${file_name}",
    );
    oss()->getObject(
        $this->get('settings')['aliyun']['bucketName'],
        "{$user_id}/${file_name}",
				$options
    );

    $fh = fopen($file, 'rb');
    $stream = new \Slim\Http\Stream($fh);
    return $response->withHeader('Content-Type', 'application/force-download')
        ->withHeader('Content-Type', 'application/download')
        ->withHeader('Content-Disposition', 'attachment; filename="'.basename($file).'"')
        ->withHeader('Content-Length', filesize($file))
        ->withBody($stream);
});

$app->get('/delete', function (Request $request, Response $response) {
    $user_id = $this->get('settings')['user'];
    $file_name = $request->getParam('file_name');
    if(!$file_name) {
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    $save_name = "{$user_id}/{$file_name}";
    db_execute(
      'delete from contents where name = ? and user_id = ?',
      array($file_name, $user_id)
    );
    oss()->deleteObject(
        $this->get('settings')['aliyun']['bucketName'],
        $save_name
    );
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->run();
