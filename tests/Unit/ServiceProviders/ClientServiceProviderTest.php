<?php
namespace agoalofalife\Tests\ServiceProviders;


use agoalofalife\bpm\Contracts\ServiceProvider;
use agoalofalife\bpm\ServiceProviders\ClientServiceProvider;
use agoalofalife\Tests\TestCase;
use GuzzleHttp\ClientInterface;

class ClientServiceProviderTest extends TestCase
{
    protected $providers;

    public function setUp()
    {
        parent::setUp();
        $this->providers = new ClientServiceProvider();
    }

    public function test_instanceOf()
    {
        $this->assertInstanceOf(ServiceProvider::class, $this->providers);
    }

    public function test_register()
    {
        $this->providers->register();
        $this->assertTrue(app()->bound(ClientInterface::class));
    }
}