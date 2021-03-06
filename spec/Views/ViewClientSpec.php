<?php

namespace spec\TheMarketingLab\Hg\Views;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\ClientInterface as ClientInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\RequestInterface;
use TheMarketingLab\Hg\ConfigurationInterface;
use TheMarketingLab\Hg\Views\ViewInterface;
use TheMarketingLab\Hg\Views\ViewFactoryInterface;
use TheMarketingLab\Hg\Tests\TestInterface;

class ViewClientSpec extends ObjectBehavior
{
    function let(ConfigurationInterface $config, ClientInterface $guzzle, ViewFactoryInterface $viewFactory)
    {
        $config->getAccessToken()->willReturn('1234');
        $config->getClient()->willReturn($guzzle);
        $config->isValid()->willReturn(true);
        $this->beConstructedWith($config, $viewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('TheMarketingLab\Hg\Views\ViewClient');
    }

    function it_is_a_view_client()
    {
        $this->shouldHaveType('TheMarketingLab\Hg\Views\ViewClientInterface');
    }

    function it_updates_a_view(
        ClientInterface $guzzle,
        ViewInterface $view,
        RequestInterface $request,
        Response $response,
        ViewFactoryInterface $viewFactory,
        ViewInterface $updatedView
    ) {
        $view->getSegment()->willReturn('default');
        $view->getTest()->willReturn(null);

        $guzzle->post('/views', array(), json_encode(array(
            'segment' => 'default'
        )))->shouldBeCalled()->willReturn($request);

        $request->send()->shouldBeCalled()->willReturn($response);
        $viewFactory->create($response)->shouldBeCalled()->willReturn($updatedView);

        $this->update($view)->shouldReturn($updatedView);
    }

    function it_updates_a_view_with_a_test(
        ClientInterface $guzzle,
        ViewInterface $view,
        TestInterface $test,
        RequestInterface $request,
        Response $response,
        ViewFactoryInterface $viewFactory,
        ViewInterface $updatedView
    ) {
        $view->getSegment()->willReturn('default');
        $view->getTest()->willReturn($test);
        $test->getId()->willReturn('123');
        $test->getVariant()->willReturn('A');

        $guzzle->post('/views', array(), json_encode(array(
            'segment' => 'default',
            'test' => array(
                'id' => '123',
                'variant' => 'A'
            )
        )))->shouldBeCalled()->willReturn($request);

        $request->send()->shouldBeCalled()->willReturn($response);
        $viewFactory->create($response)->shouldBeCalled()->willReturn($updatedView);

        $this->update($view)->shouldReturn($updatedView);
    }
}
