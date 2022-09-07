<?php

namespace Bonnier\Willow\MuPlugins;

class OffloadS3
{
    public function __construct()
    {
        add_filter('as3cf_object_meta', [$this, 'setDownloadableFiles']);

        add_action('option_tantan_wordpress_s3', [$this, 'setOptions']);

        add_filter('intermediate_image_sizes_advanced', [$this, 'disableResizing']);

        add_filter('sanitize_file_name', [$this, 'sanitizeFileName'], 10);
    }

    public function setDownloadableFiles($args)
    {
        if (isset($args['Key']) && isset($args['ContentType'])) {
            $contentTypes = [
                'application/octet-stream'
            ];
            $fileExtensions = [
                'lrtemplate',
                'exe'
            ];
            if (preg_match('/([^.]+$)/', $args['Key'], $extensionMatches) &&
                in_array($args['ContentType'], $contentTypes)
            ) {
                $extension = $extensionMatches[0];
                if (in_array($extension, $fileExtensions)) {
                    $args['ContentDisposition'] = 'attachment';
                }
            }
        }
        return $args;
    }

    public function setOptions($currentOptions)
    {
        $overrideOptions = [
            'bucket' => getenv('AWS_S3_BUCKET', 'wp-uploads.interactives.dk'),
            'object-prefix' => getenv('AWS_S3_UPLOADS_PATH'),
            'remove-local-file' => 1,
            'licence' => '',
            'copy-to-s3' => 1,
            'serve-from-s3' => 1,
            'region' => getenv('AWS_S3_REGION', 'eu-west-1')
        ];

        // By setting the AWS_S3_DOMAIN env option you may control the domain files are served from
        if ($domain = getenv('AWS_S3_DOMAIN')) {
            $overrideOptions['cloudfront'] = $domain;
            $overrideOptions['domain'] = 'cloudfront';
        }

        return array_merge($currentOptions, $overrideOptions);
    }

    public function disableResizing(array $sizes)
    {
        // To avoid having WordPress resize images, when we are using Imgix,
        // we need to return an empty array, to disable all sizes.
        return [];
    }

    public function sanitizeFileName(string $filename)
    {
        if (str_contains($filename, '.')) {
            list($name, $extension) = preg_split('/\./', $filename, 2, PREG_SPLIT_NO_EMPTY);
            return sprintf('%s.%s', $name, $extension);
        }
        return sanitize_title($filename);
    }
}
