<?php

namespace WebDevEtc\BlogEtc\Requests;

/**
 * Class BaseRequest
 * @package WebDevEtc\BlogEtc\Requests
 */
class UploadImageRequest extends BaseRequest
{
    /**
     *  rules for uploads
     *
     * @return array
     */
    public function rules():array
    {
        $rules = [
            'sizes_to_upload' => [
                'required',
                'array',
            ],
            'sizes_to_upload.*' => [
                'string',
                'max:100',
            ],
            'upload' => [
                'required',
                'image',
            ],
            'image_title' => [
                'required',
                'string',
                'min:1',
                'max:150',
            ],
        ];

        return $rules;
    }
}
