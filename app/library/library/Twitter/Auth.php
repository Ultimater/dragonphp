<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The BSD License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license https://github.com/phanbook/phanbook/blob/master/LICENSE.txt
 */
namespace Phanbook\Twitter;

use Phalcon\Config;
use Phalcon\DI\Injectable;
use Guzzle\Http\Client as HttpClient;
use League\OAuth2\Client\Provider\Twitter;

/**
 * Class Auth
 * @todo later
 * @package Phanbook\Twitter
 */
class Auth extends Injectable
{
    protected $endPointAuthorize = 'https://api.twitter.com/1.1/';

    protected $redirectUriAuthorize;

    protected $clientId;

    protected $clientSecret;


    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->redirectUriAuthorize = $config->redirectUri;
        $this->clientId             = $config->clientId;
        $this->clientSecret         = $config->clientSecret;
    }

    /**
     * It will return uid, token and information user to save database
     *
     * @return array
     */
    public function authorize()
    {
        $this->view->disable();
        $provider = new Twitter(
            [
            'clientId'      => $this->clientId,
            'clientSecret'  => $this->clientSecret,
            'redirectUri'   => $this->redirectUriAuthorize,
            ]
        );

        $code      = $this->request->getQuery('code');
        $state     = $this->request->getQuery('state');
        if (!isset($code)) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $this->session->set('oauth2state', $provider->state);
            return $this->response->redirect($authUrl);
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($state) || ($state !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            exit('Invalid state');
        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken(
                'authorization_code',
                [
                'code' => $code
                ]
            );
            $uid = $provider->getUserUid($token);
            $userDetails = $provider->getUserDetails($token);
            return array($uid, $token, $userDetails);
        }
    }
}
