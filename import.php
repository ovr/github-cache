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

while (true) {
    for ($i = 0; $i < count($usersConfig); $i++) {
        $userConfig = $usersConfig[$i];

        try {
            $client->authenticate($userConfig->token, null, \Github\Client::AUTH_HTTP_TOKEN);
            $rate = json_decode($client->getHttpClient()->get('rate_limit')->getBody(true))->rate;
            var_dump($rate);

            if ($rate->remaining == 0) {
                continue;
            }
            
            $service = $client->users();
            $service->setPerPage(100);

            while ($result = $service->all($latestId)) {
                foreach ($result as $entity) {
                    $info = false;

                    try {
                        $info = $client->users()->show($entity['login']);
                    } catch (\Exception $e) {
                        if ($e->getPrevious()) {
                            if ($e->getPrevious() instanceof \Github\Exception\ApiLimitExceedException) {
                                continue;
                            }
                        }
                        var_dump($e);
                    }

                    $user = new User();
                    $user->setSiteAdmin($entity['site_admin']);
                    $user->setLogin($entity['login']);
                    $user->setId($entity['id']);

                    if ($info) {
                        if (!empty($info['email'])) {
                            $user->setEmail($info['email']);
                        }

                        if (!empty($info['name'])) {
                            $user->setName($info['name']);
                        }

                        if (!empty($info['bio'])) {
                            $user->setBio($info['bio']);
                        }

                        if (!empty($info['blog'])) {
                            $user->setBlog($info['blog']);
                        }

                        if (!empty($info['company'])) {
                            $user->setCompany($info['company']);
                        }

                        if (!empty($info['location'])) {
                            $user->setLocation($info['location']);
                        }

                        if (isset($info['hireable'])) {
                            $user->setHireable($info['hireable']);
                        }

                        $user->setPublicRepos($info['public_repos']);

                        $user->setFollowers($info['followers']);
                        $user->setFollowing($info['following']);

                        $user->setCreatedAt($info['created_at']);
                        $user->setUpdatedAt($info['updated_at']);
                    }

                    $dm->persist($user);

                    $latestId = $entity['id'];
                    var_dump($latestId);
                }

                $dm->flush();
                $dm->clear();
            }

        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                if ($e->getPrevious() instanceof \Github\Exception\ApiLimitExceedException) {
                    continue;
                }
            }
            var_dump($i . ' == ' . $e->getMessage());
        } finally {
            $dm->flush();
            $dm->clear();
        }
    }

    sleep(60*10);
}