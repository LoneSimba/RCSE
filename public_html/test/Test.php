<?php
use PHPUnit\Framework\TestCase;
use RCSE\Core\Database\SelectQuery;

class Test extends TestCase
{
    public function testQuery()
    {
        $selQuery = new SelectQuery('users', ['`*`']);

        $this->assertEqualsIgnoringCase("SELECT `*` FROM `users`", $selQuery->getStatement());
    }
}