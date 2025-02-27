<?php

namespace WebDevEtc\BlogEtc\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use WebDevEtc\BlogEtc\Interfaces\CaptchaInterface;

/**
 * Class AddNewCommentRequest
 * @package WebDevEtc\BlogEtc\Requests
 */
class AddNewCommentRequest extends FormRequest
{

    /**
     * Can user add new comments?
     *
     * @return bool
     */
    public function authorize():bool
    {
        // TODO - use constants
        return config('blogetc.comments.type_of_comments_to_show') === 'built_in';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        // basic rules
        $return = [
            'comment' => ['required', 'string', 'min:3', 'max:1000'],
            'author_name' => ['string', 'min:1', 'max:50'],
            'author_email' => ['string', 'nullable', 'min:1', 'max:254', 'email'],
            'author_website' => ['string', 'nullable', 'min:' . strlen('http://a.b'), 'max:175', 'active_url',],
        ];

        // do we need author name?
        if (Auth::check() && config('blogetc.comments.save_user_id_if_logged_in', true)) {
            // is logged in, so we don't need an author name (it won't get used)
            $return['author_name'][] = 'nullable';
        } else {
            // is a guest - so we require this
            $return['author_name'][] = 'required';
        }

        // is captcha enabled? If so, get the rules from its class.
        if (config('blogetc.captcha.captcha_enabled')) {
            /** @var string $captcha_class */
            $captcha_class = config('blogetc.captcha.captcha_type');

            /** @var CaptchaInterface $captcha */
            $captcha = new $captcha_class();

            $return[$captcha->captchaFieldName()] = $captcha->rules();
        }

        // in case you need to implement something custom, you can use this...
        if (config('blogetc.comments.rules') && is_callable(config('blogetc.comments.rules'))) {
            /** @var callable $function */
            $function = config('blogetc.comments.rules');
            $return = $function($return);
        }

        if (config('blogetc.comments.require_author_email')) {
            $return['author_email'][] = 'required';
        }

        return $return;
    }
}
