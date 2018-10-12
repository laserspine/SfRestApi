<?php

namespace Salesforce\Interfaces;

interface ApiInterface
{
  public function query(string $query): ?\stdClass;

  public function insert(string $sobject, array $record): ?\stdClass;
  
  public function update(string $sobject, array $record): ?\stdClass;

  public function delete(string $sobject, string $id): ?string;
}