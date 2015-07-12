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
var_dump($latestId);

/**
 * @var $res \Doctrine\MongoDB\ArrayIterator
 */
$res = $dm->getRepository('App\Model\User')->createQueryBuilder()
    ->group(
        array(
            'company' => 1
        ),
        array(
            'total' => 0
        )
    )
    ->reduce('function ( curr, result ) { result.total += 1;}' )
//    ->field( 'company' )->equals ( 'Smartfish Software Ltd.' )
    ->getQuery()
    ->execute();

foreach ($res as $r) {
    if ($r['total'] > 10) {
        var_dump($r);
    }
}

var_dump($res->count());
