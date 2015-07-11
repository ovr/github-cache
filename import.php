<?php

include_once __DIR__ . '/vendor/autoload.php';


$usersConfig = json_decode(file_get_contents(__DIR__ . '/users.json'));

use App\Model\User;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

AnnotationDriver::registerAnnotationClasses();

$config = new Configuration();
$config->setProxyDir(__DIR__ . '/proxies');
$config->setProxyNamespace('Proxies');
$config->setHydratorDir(__DIR__ . '/hydrators');
$config->setHydratorNamespace('Hydrators');
$config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . '/app/models'));

$dm = DocumentManager::create(new Connection(), $config);


/** @var  $res */
$res = $dm->getRepository('App\Model\User')->createQueryBuilder()
    ->select('_id')
    ->sort('_id', -1)
    ->limit(1)
    ->getQuery()
    ->getSingleResult();

$latestId = null;

if ($res) {
    $latestId = $res->getId();
}

$client = new \Github\Client();

for ($i = 0; $i < count($usersConfig); $i++) {
    $userConfig = $usersConfig[$i];

    try {
        $client->authenticate($userConfig->token, null, \Github\Client::AUTH_HTTP_TOKEN);

        while ($result = $client->users()->all($latestId)) {
            foreach ($result as $entity) {
                $info = $client->users()->show($entity['login']);

                $user = new User();
                $user->setSiteAdmin($entity['site_admin']);
                $user->setLogin($entity['login']);
                $user->setId($entity['id']);

                if ($info) {
                    $user->setBio($info['bio']);
                    $user->setEmail($info['email']);
                    $user->setBlog($info['blog']);
                    $user->setHireable($info['hireable']);
                    $user->setPublicRepos($info['public_repos']);
                    $user->setBlog($info['location']);
                    $user->setCompany($info['company']);
                }

                $dm->persist($user);
                $dm->flush();
                $dm->clear();

                $latestId = $entity['id'];
                var_dump($latestId);
            }
        }

    } catch (\Github\Exception\ApiLimitExceedException $e) {
        var_dump($i . ' == ' . $e->getMessage());
    } catch (\Exception $e) {
        var_dump($i . ' == ' . $e->getMessage());
    }
}
