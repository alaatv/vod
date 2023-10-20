<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => true,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage/public',
            'visibility' => 'public',
            'throw' => true,
        ],

        'profileImage' => [
            'driver' => 'local',
            'root' => storage_path('app/public/profile/images'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'assignmentQuestionFile' => [
            'driver' => 'local',
            'root' => storage_path('app/public/assignment/questionFiles'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'assignmentSolutionFile' => [
            'driver' => 'local',
            'root' => storage_path('app/public/assignment/solutionFiles'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'productImage' => [
            'driver' => 'local',
            'root' => storage_path('app/public/product/images'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'productCatalog_PDF' => [
            'driver' => 'local',
            'root' => storage_path('app/public/product/catalog/pdf'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'consultingAudioQuestions' => [
            'driver' => 'local',
            'root' => storage_path('app/public/userUploads/consultingAudioQuestions'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'consultationThumbnail' => [
            'driver' => 'local',
            'root' => storage_path('app/public/consultation/thumbnails'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'articleImage' => [
            'driver' => 'local',
            'root' => storage_path('app/public/article/images'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'homeSlideShowPic' => [
            'driver' => 'local',
            'root' => storage_path('app/public/slideShow/home'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'orderFile' => [
            'driver' => 'local',
            'root' => storage_path('app/public/orderFiles'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'general' => [
            'driver' => 'local',
            'root' => storage_path('app/public/general'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'productFile' => [
            'driver' => 'local',
            'root' => storage_path('app/public/product/files'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'eventReport' => [
            'driver' => 'local',
            'root' => storage_path('app/public/event/userReports'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'exam' => [
            'driver' => 'local',
            'root' => storage_path('app/public/educationalContent/exam'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'pamphlet' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/pamphlet'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'voice' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/voice'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'book' => [
            'driver' => 'local',
            'root' => storage_path('app/public/content/book'),
            'visibility' => 'public',
            'throw' => true,
        ],

        'profileImageSFTP' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST2', ''),
            'port' => env('SFTP_PORT', '22'),
            'username' => env('SFTP_USERNAME', ''),
            'password' => env('SFTP_PASSWORD', ''),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root' => env('SFTP_ROOT', ''),
            'prefix' => '/'.env('PROFILE_IMAGE_PATH'),
            'timeout' => env('SFTP_TIMEOUT', '10'),
            'dHost' => env('CDN_SERVER_NAME', ''),
            'dProtocol' => env('DOWNLOAD_SERVER_PROTOCOL', 'http://'),
            'throw' => true,
        ],

        'productFileSFTP' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PAID_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'freeVideoContentMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'contentThumbnailMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => 'thumbnails/',
            'throw' => true,
        ],

        'productsIntroVideoMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET', 'upload'),
            'path' => 'introVideos/',
            'throw' => true,
        ],

        'productsIntroVideoThumbnailsMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET', 'upload'),
            'path' => 'introVideos/thumbnails/',
            'throw' => true,
        ],

        'channelThumbnailsMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET', 'upload'),
            'path' => 'channel/',
            'throw' => true,
        ],

        'freeVideoContentMinioHalfPrice' => [
            'driver' => 's3',
            'endpoint' => env('HALF_PRICE_MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('HALF_PRICE_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => '',
            'throw' => true,
        ],


        'productFileSFTPHalfPrice' => [
            'driver' => 's3',
            'endpoint' => env('HALF_PRICE_MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('HALF_PRICE_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PAID_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'alaaCdnSFTP' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST2', ''),
            'port' => env('SFTP_PORT', '22'),
            'username' => env('SFTP_USERNAME', ''),
            'password' => env('SFTP_PASSWORD', ''),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root' => env('SFTP_ROOT', ''),
            'prefix' => null,
            'timeout' => env('SFTP_TIMEOUT', '10'),
            'dHost' => env('CDN_SERVER_NAME', ''),
            'dProtocol' => env('DOWNLOAD_SERVER_PROTOCOL', 'http://'),
            'throw' => true,
        ],

        'pamphletSftp' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => 'c/pamphlet/',
            'throw' => true,
        ],

        'voiceSftp' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => 'c/voice/',
            'throw' => true,
        ],

        'examSftp' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => 'c/exam/',
            'throw' => true,
        ],

        'bookSftp' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_MEDIA_BUCKET'),
            'path' => 'c/book/',
            'throw' => true,
        ],

        'vastVideoHqSFTP' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST2', ''),
            'port' => env('SFTP_PORT', '22'),
            'username' => env('SFTP_USERNAME', ''),
            'password' => env('SFTP_PASSWORD', ''),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root' => env('SFTP_ROOT', ''),
            'prefix' => '/upload/vastVideos/hq/',
            'timeout' => env('SFTP_TIMEOUT', '10'),
            'dHost' => env('CDN_SERVER_NAME', ''),
            'dProtocol' => env('DOWNLOAD_SERVER_PROTOCOL', 'http://'),
            'throw' => true,
        ],

        'vastVideoHd720pSFTP' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST2', ''),
            'port' => env('SFTP_PORT', '22'),
            'username' => env('SFTP_USERNAME', ''),
            'password' => env('SFTP_PASSWORD', ''),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root' => env('SFTP_ROOT', ''),
            'prefix' => '/upload/vastVideos/HD_720p/',
            'timeout' => env('SFTP_TIMEOUT', '10'),
            'dHost' => env('CDN_SERVER_NAME', ''),
            'dProtocol' => env('DOWNLOAD_SERVER_PROTOCOL', 'http://'),
            'throw' => true,
        ],

        'vastVideo240pSFTP' => [
            'driver' => 'sftp',
            'host' => env('SFTP_HOST2', ''),
            'port' => env('SFTP_PORT', '22'),
            'username' => env('SFTP_USERNAME', ''),
            'password' => env('SFTP_PASSWORD', ''),
            'privateKey' => env('SFTP_PRIVATE_KEY_PATH', ''),
            'root' => env('SFTP_ROOT', ''),
            'prefix' => '/upload/vastVideos/240p/',
            'timeout' => env('SFTP_TIMEOUT', '10'),
            'dHost' => env('CDN_SERVER_NAME', ''),
            'dProtocol' => env('DOWNLOAD_SERVER_PROTOCOL', 'http://'),
            'throw' => true,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'url' => env('AWS_URL'),
            'throw' => true,
        ],

        'buckets' => [
            'upload' => [
                'bucket' => 'upload',
                'remove' => 'upload',
            ]
        ],

        'minio_upload' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'minio_upload_excel' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'excel/',
            'throw' => true,
        ],

        'profileImageMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/profile/',
            'throw' => true,
        ],

        'kartemeliImageMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/kartemeli/',
            'throw' => true,
        ],

        'productImageMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/product/',
            'throw' => true,
        ],

        'homeSlideShowPicMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/slideShow/',
            'throw' => true,
        ],

        'setImageMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'contentset/departmentlesson/',
            'throw' => true,
        ],

        'periodDescriptionPhotoMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/periodDescription/',
            'throw' => true,
        ],

        'sourcePhotoMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/source/',
            'throw' => true,
        ],

        'faqPhotoMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/faq/',
            'throw' => true,
        ],

        'ticketPhotoMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/ticket/',
            'throw' => true,
        ],

        'ticketVoiceMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'ticketVoices/',
            'throw' => true,
        ],

        'mapDetailIconMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/mapDetail',
            'throw' => true,
        ],

        'vastVideoHqMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'vast/videos/Hq/',
            'throw' => true,
        ],

        'vastVideoHd720pMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'vast/videos/HD_720p/',
            'throw' => true,
        ],

        'vastVideo240pMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'vast/videos/240p/',
            'throw' => true,
        ],

        'vastXmlMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'vast/xml/',
            'throw' => true,
        ],

        'orderFileMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'tempPrivate/orderReceipt/',
            'throw' => true,
        ],

        'productCatalogMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'productCatalogs/',
            'throw' => true,
        ],

        'eventResultMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PRIVATE_BUCKET'),
            'path' => 'userReports/',
            'throw' => true,
        ],

        'eventResultMinioTemp' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'tempPrivate/userReports/',
            'throw' => true,
        ],

        'liveDescriptionMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'images/livedescriptions/',
            'throw' => true,
        ],

        'groupRegistrationReportMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PRIVATE_BUCKET'),
            'path' => 'reports/',
            'throw' => true,
        ],

        'excelReportMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'reports/bonyadEhsan/',
            'throw' => true,
        ],

        'alaaTv' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'temp_upload' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_TEMP_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_TEMP_KEY'),
            'secret' => env('AWS_TEMP_SECRET'),
            'region' => env('AWS_TEMP_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_TEMP_ENDPOINT'),
            'bucket' => env('AWS_TEMP_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'alaa' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_DEFAULT_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_ALAA_BUCKET'),
            'path' => '',
            'throw' => true,
        ],

        'ticketFileMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'ticketFiles/',
            'throw' => true,
        ],

        'batchContentInsertMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'batchContentInsert/',
            'throw' => true,
        ],

        'LiveConductorReportMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'liveConductorReport/',
            'throw' => true,
        ],

        'entekhabReshteMinio' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'tempPrivate/userEntekhabReshte/',
            'throw' => true,
        ],

        'alaaPages' => [
            'driver' => 's3',
            'endpoint' => env('MINIO_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'download_endpoint' => env('DOWNLOAD_MINIO_ENDPOINT'),
            'bucket' => env('AWS_PUBLIC_BUCKET'),
            'path' => 'alaaPages/',
            'throw' => true,
        ],
    ],
];

