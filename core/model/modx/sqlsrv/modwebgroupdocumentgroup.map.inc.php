<?php
/**
 * @package modx
 * @subpackage sqlsrv
 */
$xpdo_meta_map['modWebGroupDocumentGroup']= array (
  'package' => 'modx',
  'version' => '1.1',
  'table' => 'webgroup_access',
  'fields' => 
  array (
    'webgroup' => 0,
    'documentgroup' => 0,
  ),
  'fieldMeta' => 
  array (
    'webgroup' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'documentgroup' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'modWebGroup' => 
    array (
      'class' => 'modWebGroup',
      'key' => 'id',
      'local' => 'webgroup',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'modDocumentGroup' => 
    array (
      'class' => 'modDocumentGroup',
      'key' => 'id',
      'local' => 'documentgroup',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
