<?php

namespace Expose;

interface Queue
{
    public function getPending($limit);
    public function add($data);
    public function markProcessed($id);
}