<?php

namespace spec\Dto\Outbound;

use Dto\Outbound\EntityDto;
use Dto\Outbound\Jsonable;
use JsonSerializable;
use PhpSpec\ObjectBehavior;

class SuccessSpec extends ObjectBehavior
{
    function it_should_be_a_Dto()
    {
        $this->shouldBeAnInstanceOf(EntityDto::class);
    }
    
    function it_should_be_Jsonable()
    {
        $this->shouldBeAnInstanceOf(Jsonable::class);
    }

    function it_should_be_JsonSerializable()
    {
        $this->shouldBeAnInstanceOf(JsonSerializable::class);
    }
    
    function it_should_have_a_200_status_code()
    {
        $this->getStatusCode()->shouldBe(200);
    }

    function it_should_prepare_itself_for_serialisation_with_multiple_keys(EntityDto $dto)
    {
        $this->add("variableName1", "variableValue");
        $this->add("variableName2", ["a"=>"variableSubValue1", "b"=>"variableSubValue2"]);
        $this->add("variableName3", $dto);
        
        $response = $this->jsonSerialize();
        
        $response->shouldBe(["variableName1"=>"variableValue", "variableName2"=>["a"=>"variableSubValue1", "b"=>"variableSubValue2"], "variableName3"=>$dto]);
    }
    
    function it_should_prepare_itself_for_serialisation_with_a_single_keys()
    {
        $this->add("a", "variableSubValue1");
        $this->add("b", "variableSubValue2");
        
        $response = $this->jsonSerialize();
        
        $response->shouldBe(["a"=>"variableSubValue1", "b"=>"variableSubValue2"]);
    }
    
    function it_should_prepare_itself_for_serialisation_without_keys()
    {
        $this->add("a", ["variable_array"]);
        
        $this->hideKeys();
        $response = $this->jsonSerialize();
        
        $response->shouldBe(["variable_array"]);
    }
}