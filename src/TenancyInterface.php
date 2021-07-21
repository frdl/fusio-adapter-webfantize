<?php
namespace Fusio\Adapter\Webfantize;

interface TenancyInterface
{
       public function config():array;
       public function getByHost( $host):TenantInterface;
       public function getByDomain( $domain):TenantInterface;
       public function get($identifier):TenantInterface;
}
