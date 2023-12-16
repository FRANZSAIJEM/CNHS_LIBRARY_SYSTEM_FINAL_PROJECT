<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\CarbonInterval;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'grade_level',
        'id_number',
        'contact',
        'email',
        'password',
        'gender',
        'image',
        'is_borrowed',
        'borrowed_count',
        'last_checked_requests',
        'last_checked_notifications',
        'last_checked_chats',
        'visited_students',
        'is_suspended',
    ];




    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [

        'password' => 'hashed',
        'is_admin' => 'boolean',
        'visited_students' => 'array',
        'is_suspended' => 'boolean',
        'suspend_start_date' => 'datetime',
        'suspend_end_date' => 'datetime',
    ];

    // User.php
    public function requestedBooks()
    {
        return $this->belongsToMany(Book::class, 'book_requests', 'user_id', 'book_id')
            ->withTimestamps();
    }


    public function hasAcceptedBook()
    {
        return $this->acceptedRequests()->exists();
    }



    // public function hasRequestedBookAny()
    // {
    //     return $this->requestedBooks()->exists();
    // }





    public function hasRequestedBook($bookId)
    {
        return $this->requestedBooks()->where('book_id', $bookId)->exists();
    }





    public function acceptedRequests()
    {
        return $this->hasMany(AcceptedRequest::class);
    }


    public function hasAcceptedRequestForBook($bookId)
    {
        return AcceptedRequest::where('book_id', $bookId)->exists();
    }

    // User.php
    public function hasAcceptedReturnedBookForBook($bookId)
    {
        return $this->acceptedRequests()->where('book_id', $bookId)->where('book_returned', true)->exists();
    }




    public function notifications()
    {
        return $this->belongsToMany(Notification::class)->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->is_admin === 1;
    }

    public function hasLikedComment(Comment $comment)
    {
        // Check if the user has liked the given comment.
        return $this->commentLikes()->where('comment_id', $comment->id)->exists();
    }

    public function commentLikes()
    {
        // Define the relationship between User and CommentLike models.
        return $this->hasMany(CommentLike::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function messages()
    {
        return $this->hasMany(Chat::class, 'receiver_id', 'id');
    }


        // Inside your User model (User.php)
    public function hasChatData()
    {
        return $this->messages->count() > 0;
    }



    public function chats()
    {
        return $this->hasMany(Chat::class, 'receiver_id', 'id');
    }


    public function latestChatTime()
    {
        // Assuming you have a relationship defined between users and chats
        $latestChatTime = $this->chats()->max('created_at');

        return $latestChatTime;
    }

    public function getSuspensionDuration()
    {
        if ($this->is_suspended) {
            $startDate = Carbon::parse($this->suspend_start_date);
            $endDate = Carbon::parse($this->suspend_end_date);

            // Swap dates if necessary
            if ($startDate->greaterThan($endDate)) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            $diffInSeconds = $endDate->diffInSeconds($startDate);

            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;

            $formattedDiff = sprintf('%dh %02dm %02ds', $hours, $minutes, $seconds);

            return $formattedDiff;
        }

        return "";
    }

    public function userBookRequest()
    {
        return $this->hasOne(UserBookRequest::class);
    }

    public function borrowCount()
    {
        return $this->hasOne(BorrowCount::class);
    }

}
