<?php namespace Way\Tests;

use Mockery;

trait ModelHelpers {

    public function tearDown()
    {
        Mockery::close();
    }

    public function assertValid($model)
    {
        $this->assertRespondsTo('validate', $model, "The 'validate' method does not exist on this model.");
        $this->assertTrue($model->validate(), 'Model did not pass validation.');
    }

    public function assertNotValid($model)
    {
        $this->assertRespondsTo('validate', $model, "The 'validate' method does not exist on this model.");
        $this->assertFalse($model->validate(), 'Did not expect model to pass validation.');
    }

    public function assertBelongsToMany($relation, $class, $relatedClass = null, $key = null)
    {
        $this->assertRelationship($relation, $class, 'belongsToMany', $relatedClass, $key);
    }

    public function assertBelongsTo($relation, $class, $relatedClass = null, $key = null)
    {
        $this->assertRelationship($relation, $class, 'belongsTo', $relatedClass, $key);
    }

    public function assertHasMany($relation, $class, $relatedClass = null, $key = null)
    {
        $this->assertRelationship($relation, $class, 'hasMany', $relatedClass, $key);
    }

    public function assertHasOne($relation, $class, $relatedClass = null, $key = null)
    {
        $this->assertRelationship($relation, $class, 'hasOne', $relatedClass, $key);
    }

    public function assertRespondsTo($method, $class, $message = null)
    {
        $message = $message ?: "Expected the '$class' class to have method, '$method'.";

        $this->assertTrue(
            method_exists($class, $method),
            $message
        );
    }

    public function assertRelationship($relationship, $class, $type, $relatedClass = null, $key = null)
    {
        $relatedClass = $relatedClass
            ? str_replace('\\', '\\\\', $relatedClass)
            : str_singular($relationship);

        $this->assertRespondsTo($relationship, $class);

        $class = Mockery::mock($class."[$type]");

        if ($key) {
            $class->shouldReceive($type)
              ->with('/' . $relatedClass . '/i', $key)
              ->once();
        } else {
            $class->shouldReceive($type)
              ->with('/' . $relatedClass . '/i')
              ->once();
        }

        $class->$relationship();
    }
}
