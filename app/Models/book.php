<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'subject',
        'availability',
        'status',
        'isbn',
        'description',
        'publish',
        'image',
        'number_of_copies',
        'count_copies',
        'condition',
        'is_borrowed',
    ];

    public function requestedByUsers()
    {
        return $this->belongsToMany(User::class, 'book_requests', 'book_id', 'user_id')
            ->withTimestamps();
    }


    public function acceptedRequests()
    {
        return $this->hasMany(AcceptedRequest::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


}
