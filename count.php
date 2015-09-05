<?php

use App\Model\User;

include_once __DIR__ . '/common.php';

/** @var  $res */
$res = $dm->getRepository(User::class)->createQueryBuilder()
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
$res = $dm->getRepository(User::class)->createQueryBuilder()
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
