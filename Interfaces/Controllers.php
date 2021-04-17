<?php

namespace Framework\Interfaces;

use Framework\Utils\EntityManager;

interface Controllers
{
    public function error(EntityManager $entityManager);
    public function default_method(EntityManager $entityManager);
}

