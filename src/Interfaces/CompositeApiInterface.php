<?php

namespace Salesforce\Interfaces;

interface ApiInterface
{
  public function query(string $query, string $count): ?\stdClass;

  public function insert(string $sobject, string $count): ?\stdClass;
  
  public function update(string $sobject, string $count): ?\stdClass;

  public function delete(string $sobject, string $id, string $count): ?string;
}