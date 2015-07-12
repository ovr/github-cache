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
        $client->authenticate('1951772aae988679dfb40baec619d9fee7977f09', null, \Github\Client::AUTH_HTTP_TOKEN);
        var_dump(json_decode($client->getHttpClient()->get('rate_limit')->getBody(true)));
//        var_dump($client->getHttpClient()->get('users')->getHeaders());
        die();

        while ($result = $client->users()->all($latestId)) {
            foreach ($result as $entity) {
                $info = false;
                try {
                    $info = $client->users()->show($entity['login']);
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                }

                $user = new User();
                $user->setSiteAdmin($entity['site_admin']);
                $user->setLogin($entity['login']);
                $user->setId($entity['id']);

                if ($info) {
                    $user->setName($info['name']);
                    $user->setBio($info['bio']);
                    $user->setEmail($info['email']);
                    $user->setBlog($info['blog']);
                    $user->setHireable($info['hireable']);
                    $user->setPublicRepos($info['public_repos']);
                    $user->setCompany($info['company']);
                    $user->setLocation($info['location']);

                    $user->setFollowers($info['followers']);
                    $user->setFollowing($info['following']);

                    $user->setCreatedAt($info['created_at']);
                    $user->setUpdatedAt($info['updated_at']);
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
    die();
}
