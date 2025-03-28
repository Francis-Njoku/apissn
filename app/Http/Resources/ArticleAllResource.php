<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ArticleAllResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'news_type_id' => $this->news_type_id,
            'name' => $this->name,
            'title' => $this->title,
            'body' => $this->body,
            'slug' => $this->slug,
            'mediaType' => $this->mediaType,
            'mediaSrc' => $this->mediaSrc,
            'news_date' => $this->news_date,
            'image_url' => $this->featuredImage ? URL::to('storage/'.$this->featuredImage) : null,
            'status' => $this->status,
            'featured_image' => $this->featuredImage ? $this->featuredImage : null,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
        
        if ($this->mediaType == "video") {
            $data['media'] =  $this->media 
            ? ( $this->mediaSrc === 'local' ?  URL::to('storage/'.$this->media ) : $this->media ) 
            : null  ;
        } elseif ($this->mediaType == "audio") {
            $data['media'] =  $this->media 
            ? ( $this->mediaSrc === 'local' ?  URL::to('storage/'.$this->media ) : $this->media ) 
            : null  ;
        }

        return $data;
    }
}
