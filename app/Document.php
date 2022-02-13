<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public const DRAFT = "draft";
    public const PUBLISHED = "published";

    protected $fillable = [
        "status",
        "payload"
    ];
}
