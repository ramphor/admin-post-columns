<?php
namespace Ramphor\PostColumns\Abstracts;

use Ramphor\PostColumns\Interfaces\ColumnInterface;

abstract class ColumnAbstract implements ColumnInterface
{
    protected $position = 10;
    protected $postType = 'post';

    protected $id;
    protected $title;
    protected $postId;

    protected $renderer;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $setValueMethod = sprintf('set%s', preg_replace_callback('/(^\w|\_\w)/', function ($matches) {
                    return ltrim(strtoupper($matches[0]), '_');
                }, $attribute));
                if (!method_exists($this, $setValueMethod)) {
                    $this->$attribute = $value;
                } else {
                    call_user_func([$this, $setValueMethod], $value);
                }
            }
        }
    }

    public function setPosition($position)
    {
        $this->position = intval($position);
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function registerTo(&$columns)
    {

        $keys = array_keys($columns);
        $sliceKeys = array_slice($keys, 0, $this->getPosition(), true);
        $values = array_values($columns);
        $sliceValues = array_slice($values, 0, $this->getPosition());

        if ($this->getPosition() > 0) {
            array_push($sliceKeys, $this->getId());
            array_push($sliceValues, $this->getTitle());
        } else {
            array_unshift($sliceKeys, $this->getId());
            array_unshift($sliceValues, $this->getTitle());
        }

        $columns = array_combine(
            array_merge($sliceKeys, $keys),
            array_merge($sliceValues, $values)
        );

        return $columns;
    }

    /**
     *
     * @param  callable $callable
     * @return  mixed
     */
    public function registerRenderCallback($callable)
    {
        if (is_callable($callable)) {
            $this->renderer = $callable;
        }
    }

    public function render()
    {
        if (is_null($this->renderer)) {
            do_action("ramphor_post_colum_{$this->getId()}_content", $this->getPostId());

            return;
        }
        return call_user_func($this->renderer, $this->getPostId());
    }

    public function setAppliedPostType($postType)
    {
        $this->postType = $postType;
    }
}
