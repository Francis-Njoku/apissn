<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Newsletter extends Model
{
    use HasFactory, HasSlug;
    // Table name
    protected $table = 'newsletter';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    // Fillable
    protected $fillable = ['news_type_id', 'name', 'title', 'slug', 'body','news_date','featuredImage', 'media','mediaType','status'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

}
