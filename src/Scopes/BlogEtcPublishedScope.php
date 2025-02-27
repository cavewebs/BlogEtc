<?php

namespace WebDevEtc\BlogEtc\Scopes;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class BlogEtcPublishedScope
 * @package WebDevEtc\BlogEtc\Scopes
 */
class BlogEtcPublishedScope implements Scope
{
    /**
     * If user is logged in and canManageBlogEtcPosts() == true, then don't add any scope
     * But for everyone else then it should only show PUBLISHED posts with a POSTED_AT < NOW()
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!Auth::check() || !Auth::user()->canManageBlogEtcPosts()) {
            // user is a guest, or if logged in they can't manage blog posts
            $builder->where('is_published', true);
            $builder->where('posted_at', '<=', Carbon::now());
        }
    }
}
