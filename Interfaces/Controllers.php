<?php

namespace StindCo\stinder\Interfaces;

use StindCo\stinder\Utils\EntityManager;

interface Controllers
{
    public function error(EntityManager $entityManager);
    public function default_method(EntityManager $entityManager);
}

