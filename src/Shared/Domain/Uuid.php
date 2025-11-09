<?php
namespace Udemy\Shared\Domain;

interface Uuid
{
public function next(): string;
}