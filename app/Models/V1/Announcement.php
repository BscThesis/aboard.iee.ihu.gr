<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Illuminate\Notifications\Notifiable;
use \Carbon\Carbon;

class Announcement extends Model implements Feedable
{

    use SoftDeletes, Notifiable;

    const SORT_VALUES = ['IF(pinned_until >= NOW(), pinned_until, 1) DESC, updated_at DESC','updated_at DESC','updated_at ASC'] ;

    public function tags()
    {
        return $this->belongsToMany('App\Models\V1\Tag');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\V1\Attachment');
    }

    public function toFeedItem()
    {
        return FeedItem::create([
            'id' => route('announcement', ['id' => $this->id]),
            'title' => $this->title,
            'summary' => $this->body,
            'updated' => $this->updated_at,
            'link' => route('announcement', ['id' => $this->id]),
            'author' => $this->user->name,
        ]);
    }
    
    public function scopeWithFilters($query, $users, $tags, $title, $body, $updated_after, $updated_before)
    {
        // Filter announcements based on users, tags, title, body. 
       
        if (in_array($updated_after,["last_hour","last_day","last_week","last_month","last_6months"])) {
            switch ($updated_after) {
                case 'last_hour':
                    $updated_after = Carbon::now()->subHour(1);
                    break;
                case 'last_day':
                    $updated_after = Carbon::today()->subDays(1);
                    break;    
                case 'last_week':
                    $updated_after = Carbon::today()->subDays(7);
                    break;  
                case 'last_month':
                    $updated_after = Carbon::today()->subMonths(1);
                    break;     
                default:
                    $updated_after = Carbon::today()->subMonths(6);
                    break;
            }
        }
        
        return $query->when($title !== '' && $title !== null, function ($query) use ($title) {
            $query->where(function($query) use ($title) {
                $query->where('title', 'LIKE', '%' . $title . '%')
                ->orWhere('eng_title', 'LIKE', '%' . $title . '%');
            });            
        })->when($body !== '' && $body !== null, function ($query) use ($body){
            $query->where(function($query) use ($body) {
                $query->where('body', 'LIKE', '%' . $body . '%')
                ->orWhere('eng_body', 'LIKE', '%' . $body . '%');
            });
        })->when(count($users), function ($query) use ($users) {
            $query->whereIn('user_id', $users);
        })->when(count($tags), function ($query) use ($tags){
           $query->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('id', $tags);
            });
        })->when($updated_before !== '' && $updated_before !== null, function ($query) use ($updated_before) {
            $query->where('updated_at','<=',$updated_before);
        })->when($updated_after !== '' && $updated_after !== null, function ($query) use ($updated_after) {
            $query->where('updated_at','>=',$updated_after);
        });
    }

    public static function getFeedItems()
    {
        return Announcement::whereHas('tags', function (Builder $query) {
            $query->where('is_public', '=', 1);
        })->where(function (Builder $query) {
            $query->orWhere('created_at', '>=', Carbon::today()->subDays(env("FEED_DAYS", "7")))->orWhere('is_pinned', 1);
        })->orderBy('updated_at', 'desc')->get();
    }

    public function getLinkAttribute()
    {
        return route('announcements.rss', $this);
    }

    /**
     * Get the user that owns the announcement.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Check if announcement and attachment relate.
     */
    public function hasAttachment($attachment_id)
    {
        return $this->attachments()->where('id', $attachment_id)->exists();
    }

    /**
     * Check if announcement has public tags.
     */
    public function hasPublicTags()
    {
        return in_array(true, $this->tags()->pluck('is_public')->toArray());
    }
}