<?php
namespace Yarmat\Comment\Contracts;

interface CommentContract
{
    public function comments();

    public function saveComment(array $attributes);

    public function deleteComments();
}