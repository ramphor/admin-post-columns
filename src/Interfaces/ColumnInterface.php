<?php

namespace Ramphor\PostColumns\Interfaces;

interface ColumnInterface
{
    public function setPosition($position);

    /**
     * @return  int
     */
    public function getPosition();

    public function setPostId($postId);

    /**
     * @return int
     */
    public function getPostId();

    public function setId($id);

    public function getId();

    public function setTitle($title);

    public function getTitle();

    public function registerTo(&$columns);

    public function render();

    public function setAppliedPostType($postType);
}
