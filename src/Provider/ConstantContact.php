<?php

namespace Chadhutchins\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class ConstantContact extends AbstractProvider
{

    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
     *     Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    public function getBaseAuthorizationUrl()
    {
        return 'https://api.cc.email/v3/idfed';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://idfed.constantcontact.com/as/token.oauth2';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.cc.email/v3/account/summary';
    }

    public function getDefaultScopes()
    {
        return ['account_read','contact_data','campaign_data'];
    }

    public function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['errors'])) {
            throw new IdentityProviderException($data['errors'], 0, $data);
        }

        return $data;
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ConstantContactAccount($response);
    }
}
