<?php

interface JobInterface
{
  public function getJob ();
  
  public function createJob ();
  
  public function closeJob ();
}

