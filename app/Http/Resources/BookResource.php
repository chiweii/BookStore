<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->type);
        return [
            'id' => $this->id,
            'ISBN' => $this->ISBN,
            'name' => $this->name,
            'description' => $this->description,
            'publisher_id' => $this->publisher_id,
            'publish_date' => $this->publish_date,
            'publish_age' => $this->publish_age,
            'author_id'=> $this->author_id,
            'type_id' => $this->type_id ?? null,
            'type_name' => $this->type->name ?? null,
            'book_classification' => $this->book_classification,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
        // return parent::toArray($request);
    }
}
