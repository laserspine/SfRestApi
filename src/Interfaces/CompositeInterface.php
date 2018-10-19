<?php

namespace Salesforce\Interfaces;

interface CompositeInterface
{
  public function query(string $query, string $count): ?\stdClass;

  public function insert(string $sobject, array $record, string $count): ?\stdClass;
  
  public function update(string $sobject, array $record, string $count): ?\stdClass;

  public function delete(string $sobject, string $id, string $count): ?string;
}