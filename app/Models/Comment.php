<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments'; // Specify the table name if different

    protected $fillable = [
        'book_id',
        'user_id',
        'comment',
    ];
    public function replies()
    {
        return $this->hasMany(Reply::class)->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
}
