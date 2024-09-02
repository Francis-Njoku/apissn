<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'mediaType' => $this->mediaType,
            'news_date' => $this->news_date,
            'image_url' => $this->featuredImage ? URL::to('storage/'.$this->featuredImage) : null,
            'status' => $this->status,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];

        if ($this->mediaType == "video") {
            $data['media'] = $this->media ? URL::to('storage/'.$this->media) : null;
        }elseif ($this->mediaType == "audio") {
            $data['media'] = $this->media ? URL::to('storage/'.$this->media) : null;
        }

        return $data;
    }
}
