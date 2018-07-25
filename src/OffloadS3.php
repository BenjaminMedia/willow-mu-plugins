<?php

namespace Bonnier\Willow\MuPlugins;


class OffloadS3
{
    public function __construct()
    {
        add_action('option_tantan_wordpress_s3', [$this, 'setOptions']);
    }
    public function setOptions($currentOptions) {
        $overrideOptions = [
            'bucket' => env('AWS_S3_BUCKET', 'wp-uploads.interactives.dk'),
            'object-prefix' => env('AWS_S3_UPLOADS_PATH'),
            'remove-local-file' => 1,
            'licence' => '',
            'copy-to-s3' => 1,
            'serve-from-s3' => 1,
            'region' => env('AWS_S3_REGION', 'eu-west-1')
        ];

        // By setting the AWS_S3_DOMAIN env option you may control the domain files are served from
        if ($domain = env('AWS_S3_DOMAIN')) {
            $overrideOptions['cloudfront'] = $domain;
            $overrideOptions['domain'] = 'cloudfront';
        }

        return array_merge($currentOptions, $overrideOptions);
    }
}
