# Toggl OAuth2 Setup Guide

This guide will walk you through setting up OAuth2 authentication with Toggl Track.

## Prerequisites

- A Toggl Track account (free or paid)
- Access to Toggl Track settings

## Steps to Create OAuth Application

### 1. Log in to Toggl Track

Go to https://track.toggl.com and log in with your account.

### 2. Navigate to Profile Settings

1. Click on your profile icon in the top right corner
2. Select "Profile settings" from the dropdown menu

### 3. Access API Token Section

1. Scroll down to find the "API Token" section
2. You'll see your API token (which is different from OAuth)

### 4. Register OAuth Application

**Note**: As of the latest Toggl Track updates, OAuth2 app registration might require contacting Toggl support or using their developer portal.

For development purposes, you can:

1. Contact Toggl support at support@toggl.com
2. Request OAuth2 credentials for your application
3. Provide the following information:
   - Application Name: "Toggl Report Generator"
   - Description: "Generate styled XLSX reports from Toggl time entries"
   - Redirect URI: `http://localhost:8080/oauth/check/toggl` (or your production URL)
   - Website: Your website URL (if applicable)

### 5. Alternative: Use API Token for Development

For development and testing, you can use Toggl's API token authentication:

1. In Toggl Track, go to Profile Settings
2. Find your API Token
3. Note: This approach doesn't use OAuth2 but can be used for initial testing

To use API token instead of OAuth2:
- Modify the `TogglApiService` to accept API token
- Update authentication to use Basic Auth with token as username

## Configuration in Application

Once you have your OAuth credentials:

### 1. Update .env.local

```bash
OAUTH_TOGGL_CLIENT_ID=your_client_id_here
OAUTH_TOGGL_CLIENT_SECRET=your_client_secret_here
```

### 2. For Production

Update your production environment variables:

```bash
# In your production .env or server environment variables
OAUTH_TOGGL_CLIENT_ID=production_client_id
OAUTH_TOGGL_CLIENT_SECRET=production_client_secret
```

### 3. Update Redirect URI

If deploying to production, update the redirect URI in:

1. Your Toggl OAuth application settings
2. Match it to your domain: `https://yourdomain.com/oauth/check/toggl`

## OAuth Flow

The application uses the Authorization Code flow:

1. **User clicks "Connect Toggl"**: Redirects to `/oauth/connect/toggl`
2. **Redirect to Toggl**: Application redirects to Toggl authorization page
3. **User authorizes**: User logs in and authorizes the application
4. **Callback**: Toggl redirects back to `/oauth/check/toggl` with authorization code
5. **Exchange code for token**: Application exchanges code for access token
6. **Store token**: Token is stored in database
7. **Redirect to dashboard**: User is redirected to dashboard

## Scopes

Toggl OAuth2 typically includes these scopes:
- Read time entries
- Read projects
- Read workspaces
- Read user profile

The application requests minimal necessary scopes.

## Token Management

- **Access tokens** are valid for a limited time (typically 1 hour)
- **Refresh tokens** can be used to obtain new access tokens
- The application stores both tokens in the database
- Token refresh is not currently implemented but can be added

## Troubleshooting

### "Invalid client_id"
- Check that your client ID is correct in .env.local
- Ensure there are no extra spaces or quotes

### "Redirect URI mismatch"
- Verify the redirect URI in Toggl matches exactly
- Include protocol (http/https)
- Check for trailing slashes

### "Access denied"
- User clicked "Deny" on authorization page
- User needs to try connecting again

### "Token expired"
- Implement token refresh logic
- Or ask user to reconnect their account

## Security Best Practices

1. **Never commit** OAuth credentials to git
2. Use **different credentials** for development and production
3. **Rotate secrets** regularly in production
4. Consider **encrypting tokens** in the database
5. Implement **token refresh** to avoid requiring re-authentication
6. Use **HTTPS** in production
7. Validate **state parameter** to prevent CSRF attacks (handled by bundle)

## Additional Resources

- [Toggl Track API Documentation](https://developers.track.toggl.com/docs/)
- [OAuth2 RFC](https://tools.ietf.org/html/rfc6749)
- [KnpUOAuth2ClientBundle Documentation](https://github.com/knpuniversity/oauth2-client-bundle)

## Support

If you encounter issues:

1. Check Toggl Track API status
2. Verify your OAuth credentials
3. Check application logs in `var/log/`
4. Contact Toggl support for OAuth-specific issues
