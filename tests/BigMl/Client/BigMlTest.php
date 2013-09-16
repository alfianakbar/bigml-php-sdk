<?php
namespace BigMl\Client;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

class BigMlTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730',
            'version' => 'andromeda',
            'mode' => 'dev',
        ));
        $this->assertEquals('alfred', $client->getOption('username'));
        $this->assertEquals('79138a622755a2383660347f895444b1eb927730', $client->getOption('api_key'));
        $this->assertEquals('andromeda', $client->getOption('version'));
        $this->assertEquals('dev', $client->getOption('mode'));
    }

    public function testConstructDefault()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $this->assertEquals('alfred', $client->getOption('username'));
        $this->assertEquals('79138a622755a2383660347f895444b1eb927730', $client->getOption('api_key'));
        $this->assertEquals('andromeda', $client->getOption('version'));
        $this->assertEquals('dev', $client->getOption('mode'));
    }

    public function testConstructGetClient()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $this->assertInstanceof('ZendRest\Client\RestClient', $client->getClient());
        $this->assertEquals('https://bigml.io/dev/andromeda/', $client->getClient()->getUri()->toString());
    }

    public function testConstructSetClient()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $mock = $this->getMock('ZendRest\Client\RestClient');
        $client->setClient($mock);
        $this->assertEquals($mock, $client->getClient());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithoutParameters()
    {
        $client = new BigMl(array());
    }

    public function testGetOption()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $this->assertEquals('alfred', $client->getOption('username'));
    }

    public function testSetOption()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $client->setOption('username', 'testing');
        $this->assertEquals('testing', $client->getOption('username'));
    }

    public function testRestGet()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $client->setClient($this->getClientMock('restGet'));
        $this->assertEquals(array('status' => 'ok'), $client->restGet('source'));
    }

    public function testRestPut()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $client->setClient($this->getClientMock('restPut'));
        $this->assertEquals(array('status' => 'ok'), $client->restPut('source'));
    }

    public function testRestPost()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $client->setClient($this->getClientMock('restPost'));
        $this->assertEquals(array('status' => 'ok'), $client->restPost('source'));
    }

    public function testRestDelete()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $client->setClient($this->getClientMock('restDelete'));
        $this->assertEquals(array('status' => 'ok'), $client->restDelete('source'));
    }

    public function testPreparePath()
    {
        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));

        $method = new ReflectionMethod(
          get_class($client), 'preparePath'
        );
        $method->setAccessible(TRUE);
        $this->assertEquals(
            'abc?username=alfred;api_key=79138a622755a2383660347f895444b1eb927730', $method->invoke($client, 'abc')
        );
    }

    public function testProcessResponse()
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
             ->method('getBody')
             ->will($this->returnValue(json_encode(array('abc' => 123))));
        $response->expects($this->once())
             ->method('isOk')
             ->will($this->returnValue(true));

        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $method = new ReflectionMethod(
          get_class($client), 'processResponse'
        );
        $method->setAccessible(TRUE);
        $this->assertEquals(array('abc' => 123), $method->invoke($client, $response));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testProcessResponseError()
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
             ->method('isOk')
             ->will($this->returnValue(false));

        $client = new BigMl(array(
            'username' => 'alfred',
            'api_key' => '79138a622755a2383660347f895444b1eb927730'
        ));
        $method = new ReflectionMethod(
          get_class($client), 'processResponse'
        );
        $method->setAccessible(TRUE);
        $method->invoke($client, $response);
    }

    private function getClientMock($method)
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
             ->method('getBody')
             ->will($this->returnValue(json_encode(array('status' => 'ok'))));
        $response->expects($this->once())
             ->method('isOk')
             ->will($this->returnValue(true));

        $client = $this->getMock('ZendRest\Client\RestClient');
        $client->expects($this->once())
             ->method($method)
             ->will($this->returnValue($response));
        return $client;
    }


    public function testFactory()
    {
        $source = BigMl::factory('source', array(
            'username' => 'alfred',
            'api_key'  => '79138a622755a2383660347f895444b1eb927730'            
        ));
        $this->assertInstanceof('BigMl\Resource\AbstractResource', $source);
        $this->assertInstanceof('BigMl\Resource\Source', $source);
        $this->assertInstanceof('BigMl\Client\BigMl', $source->getClient());
    }
}