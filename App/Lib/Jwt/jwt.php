<?php


namespace App\Lib\Jwt;

use EasySwoole\Jwt\Jwt;

/**
 * Class AccessToken
 * @package App\Lib\Jwt
 */
class AccessToken
{
    const iss             = 'www.baidu.com';
    const expire_interval = 7 * 24 * 60 * 60;
    const alg             = 'HS256';
    const key             = 'S0&sK#xo';
    protected $config;
    // 错误信息
    private $message;

    /**
     * AccessToken constructor.
     */
    public function __construct()
    {
        $this->config = [
            'iss'             => self::iss,
            'alg'             => self::alg,
            'key'             => self::key,
            'expire_interval' => self::expire_interval,
        ];
    }

    /**
     * @param string $access_token
     * @return bool
     */
    public function checkAccessToken( string $access_token ) : bool
    {
        if( !empty( $access_token ) ){
            $jwt = $this->decode( $access_token );
            if( empty( $jwt ) || !isset( $jwt['jti'] ) ){
                return false;
            }
            $find = model( 'AccessToken' )->getAccessTokenInfo( [
                'jti'        => $jwt['jti'],
                'sub'        => $jwt['sub'],
                'exp'        => ['egt', $jwt['exp']],
                'is_invalid' => 0,
                'is_logout'  => 0,
            ] );

            if( $find ){
                return true;
            } else{
                return false;
            }
        } else{
            return false;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function encode( array $data ) : string
    {
        return Jwt::encode( $data, $this->config['key'], $this->config['alg'] );
    }

    /**
     * @param string $access_token
     * @return array|null
     */
    public function decode( string $access_token ) : ?  array
    {
        try{
            $access_token_data = (array)Jwt::decode( $access_token, $this->config['key'], [$this->config['alg']] );
        } catch( \Firebase\JWT\ExpiredException $e ){
            $this->message = $e->getMessage();
            return null;
        } catch( \Firebase\JWT\SignatureInvalidException $e ){
            $this->message = $e->getMessage();
            return null;
        } catch( \Exception $e ){
            $this->message = $e->getMessage();
            return null;
        }
        return $access_token_data;
    }

    public function getMessage(){
        return $this->message;
    }

    /**
     * @param int $user_id
     * @param int $start_time
     * @return array|null
     */
    public function createAccessToken( int $user_id, int $start_time ) : ? array
    {

        $jti = model( 'AccessToken' )->addAccessToken( [
            'sub' => $user_id,
            'iat' => $start_time,
            'exp' => $start_time + $this->config['expire_interval'],
        ] );

        $access_token = $this->encode( [
            'jti' => $jti,
            'iss' => 'api.fashop.cn',
            'sub' => $user_id,
            'iat' => $start_time,
            'exp' => $start_time + $this->config['expire_interval'],
        ] );

        return [
            'access_token' => $access_token,
            'expires_in'   => $this->config['expire_interval'],
        ];
    }


    /**
     * @param string $access_token
     * @param int $start_time
     * @return array|null
     */
    public function refreshAccessToken( string $access_token, int $start_time ) : ? array
    {
        $jwt = $this->decode( $access_token );

        $access_token_data = [
            'jti' => $jwt['jti'],
            'sub' => $jwt['sub'],
            'iat' => $start_time,
            'exp' => $start_time + $this->config['expire_interval'],
        ];

        $refresh_access_token = $this->encode( $jwt );

        if( $refresh_access_token ){
            // 修改之前秘钥id的过期时间
            $state = model( 'AccessToken' )->editAccessToken( ['jti' => $access_token_data['jti']], [
                'exp' => $start_time + $this->config['expire_interval'],
            ] );
            if( $state ){
                return [
                    'access_token' => $refresh_access_token,
                    'expires_in'   => $this->config['expire_interval'],
                ];
            } else{
                return null;
            }
        } else{
            return null;
        }
    }

}