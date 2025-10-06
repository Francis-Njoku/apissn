<?php

namespace App\Models;

use Spatie\Sluggable\SlugOptions;
use Spatie\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasComments;

class Newsletter extends Model
{
    use HasFactory;
    use HasSlug;
    use HasComments;
    // Table name
    protected $table = 'newsletter';
    // Primary Key
    public $primaryKey = 'id';
    // Timestamps
    // Fillable
    protected $fillable = ['news_type_id', 'name', 'title', 'slug', 'body','news_date','featuredImage', 'media','mediaType', 'mediaSrc','status', 'tags', 'author_id', 'featured'];

    // public function author()
    // {
    //     return $this->belongsTo(User::class, 'author_id');
    // }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

}
