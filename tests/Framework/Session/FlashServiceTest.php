<?php

namespace Tests\Framework\Session;

use Framework\Session\ArraySession;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    /**
     * @var ArraySession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flashService;

    public function setUp()
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flashService->addFlash('success', 'test');
        $this->assertEquals('test', $this->flashService->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('test', $this->flashService->get('success'));
        $this->assertEquals('test', $this->flashService->get('success'));
    }
}