<?php

namespace Salesforce\Interfaces;

interface CompositeInterface
{
  public function query(string $query, string $count);

  public function insert(string $sobject, array $record, string $count);
  
  public function update(string $sobject, array $record, string $count);

  public function delete(string $sobject, string $id, string $count);
}