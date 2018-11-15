<?php

namespace Salesforce\Interfaces;

interface ApiInterface
{
  public function query(string $query);

  public function insert(string $sobject, array $record);
  
  public function update(string $sobject, array $record);

  public function delete(string $sobject, string $id);
}