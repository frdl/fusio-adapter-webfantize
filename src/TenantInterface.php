<?php
namespace Fusio\Adapter\Webfantize;

use Fusio\Engine\ConnectionInterface;

interface TenantInterface
{
     //  public function create() :TenantInterface;
       public function connection( $alias) :ConnectionInterface;
       public function vhost( $stage=null):VHostInterface;
       public function config(array $config = null):array;
}
