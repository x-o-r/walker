<?php

namespace Walker;

use PHPUnit\Framework\TestCase;
use stdClass;

class WalkerTest extends TestCase
{
    /**
     * @test
     */
    public function data_stream_is_empty()
    {
        $target  = 'Foo->Bar';
        $dataStream = [];

        $walker = new Walker();
        $this->assertEmpty($walker->from($dataStream)->with($target)->asString());
    }

    /**
     * @test
     */
    public function data_entry_point_is_not_specified()
    {
        $walker = new Walker();
        $this->assertEmpty($walker->with('Foo->Bar')->asString());
    }

    /**
     * @test
     */
    public function target_is_not_specified()
    {
        $walker = new Walker();
        $this->assertEmpty($walker->from([])->asString());
    }

    /**
     * @test
     */
    public function data_stream_contains_one_object()
    {
        $target  = 'Foo->Bar';
        $dataStream = [(object) ['Foo' => (object)['Bar' => 'MustBeReturned']]];

        $walker = new Walker();
        $this->assertEquals(['MustBeReturned'], $walker->from($dataStream)->with($target)->asArray());
    }
    /**
     * @test
     */
    public function multiple_target_are_specified()
    {
        $dataStream = [
            (object) ['Foo' => (object)['Bar' => 'Must']],
            (object) ['Tor' => (object)['Tue' => 'Be']],
            (object) ['Walker' => (object)['Texas' => (object)['Ranger' => 'Returned']]]
        ];

        $walker = new Walker();
        $this->assertEquals(
            'MustBeReturned',
            $walker
                ->from($dataStream)
                ->with('Foo->Bar')
                ->with('Tor->Tue')
                ->with('Walker->Texas->Ranger')
                ->asString(function($founds) {
                    return join('', $founds);
                })
        );
    }

        /**
     * @test
     */
    public function many_values_for_one_target_are_returned_formated()
    {
        $target  = 'Foo->Bar';
        $dataStream = [
            (object) ['Foo' => (object)['Bar' => 'Must']],
            (object) ['Foo' => (object)['Bar' => 'Be']],
            (object) ['Foo' => (object)['Bar' => 'Returned']],
        ];

        $walker = new Walker();
        $this->assertEquals(
            '[Must, Be, Returned]',
            $walker
                ->from($dataStream)
                ->with($target)
                ->asString(function($founds) {
                        return '[' . join(', ', $founds) . ']';
                    }
                )
        );
    }

    /**
     * @test
     */
    public function data_stream_contains_mixed_data_for_same_target()
    {
        $target  = 'Foo->Bar->Bu';
        $dataStream = [
            (object) ['Foo' => [(object)['Bar' => 'MustNotBeReturned'], (object)['Bu' => 'MustNotBeReturned']]],
            (object) ['Foo' => (object)['Bar' => 'MustNotBeReturned']],
            (object) ['Foo' => (object)['Bar' => (object)['Bu' => 'Must']]],
            (object) ['Foo' => [(object)['Bar' => (object)['Bu' => 'Be']], (object)['Bar' => (object)['Bu' => 'Returned']]]]
        ];

        $walker = new Walker();
        $this->assertEquals(['Must', 'Be', 'Returned'], $walker->from($dataStream)->with($target)->asArray());
    }

    /**
     * @test
     */
    public function can_handle_json()
    {
        $target  = 'Foo->Bar';

        $json = '{
            "Foo": {
                "Bar": "MustBeReturned"
            }
        }';

        $walker = new Walker();
        $this->assertEquals(['MustBeReturned'], $walker->fromJson($json)->with($target)->asArray());
    }

    /**
     * @test
     */
    public function multiple_values_are_returned_as_string_formated_by_default()
    {
        $this->assertEquals(
            "Some, values",
            (new Walker)
                ->from([
                    (object)["Foo" =>(object)["Bar" => "Some"]],
                    (object)["Walker" =>(object)["Texas" => (object)["Ranger" => "values"]]]
                ])
                ->with('Foo->Bar')
                ->with('Walker->Texas->Ranger')
                ->asString()
        );
    }

    /**
     * @test
     */
    public function data_entry_point_is_an_object()
    {
        $target = 'Foo->Bar';

        $dataAsObject  = new StdClass();
        $dataAsObject->Foo = new stdClass();
        $dataAsObject->Foo->Bar = 'value';

        $walker = new Walker();
        $this->assertEquals('value', $walker->from($dataAsObject)->with($target)->asString());
    }
}
