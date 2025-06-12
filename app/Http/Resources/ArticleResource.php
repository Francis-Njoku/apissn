<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class ArticleResource extends JsonResource
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
            'author_id' => $this->author_id,
            'name' => $this->name,
            'title' => $this->title,
            'body' => $this->body,
            'mediaType' => $this->mediaType,
            'news_date' => $this->news_date,
            'image_url' => $this->featuredImage ? URL::to('storage/'.$this->featuredImage) : null,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];

        switch ($this->mediaType) {
            case 'video':
                $data['media'] = $this->media ? URL::to('storage/'.$this->media) : null;
                break;
            case 'audio':
                $data['media'] = $this->media ? URL::to('storage/'.$this->media) : null;
                break;
            default:
                $data['media'] = null;
                break;
        }
        if ($this->relationLoaded('comments')) {
            $data['comments'] = $this->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'status' => $comment->status,
                    'created_at' => $comment->created_at,
                    'user' => $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name
                    ] : [
                        'name' => $comment->author_name
                    ]
                ];
            });
            $data['comments_count'] = $this->comments_count;
        }

        return $data;
    }
}
