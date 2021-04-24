<?php

namespace App;

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

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function attachments()
    {
        return $this->hasMany('App\Attachment');
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
        // return $this->whereHas('tags', function (Builder $query) {
        //     $query->where('is_public', '=', 1);
        // });
        return $this->tags->get()->whereHas('tags', function (Builder $query) {
            $query->where('is_public', '=', 1);
        });
    }
}
