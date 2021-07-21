<?php
namespace Fusio\Adapter\Webfantize;

use Fusio\Engine\ConnectionInterface;

interface VHostInterface
{
     //  public function create() :TenantInterface;
       public function owner($Tenant=null) :TenantInterface;
	   public function domain( $alias=null);
	   public function directory( $alias=null);
	   public function apps():array;
	   public function DocumentRoot( $domainAlias=null);
	   
}
