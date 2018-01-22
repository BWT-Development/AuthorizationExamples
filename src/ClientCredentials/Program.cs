using System;
using System.Collections.Generic;
using System.Net.Http;
using System.Threading.Tasks;

namespace ClientCredentials
{
    /**
     * This an example implementation of the OAuth 2.0 Client Credentials Grant
     * https://tools.ietf.org/html/rfc6749#section-4.4
     */
    class Program
    {
        // specific values
        private const string AuthenticationServerUrl = "<openid_provider_url>";
        private const string ClientId = "<your client_id>";
        private const string ClientSecret = "<your client_secret>";

        // endpoint under which the server hosts the openid connect endpoint
        private const string AuthenticationPath = "<openid_connect_endpoint>";

        // open id constants
        private const string TokenEndpoint = "/token";
        private const string GrantType = "client_credentials";

        static void Main(string[] args)
        {
            var authenticationTask = Task.Factory.StartNew(async () =>
            {
                var httpClient = new HttpClient();

                var tokenEndpointUrl = $"{AuthenticationServerUrl}{AuthenticationPath}{TokenEndpoint}";
                var formBody = new FormUrlEncodedContent(new Dictionary<string, string>()
                {
                    { "grant_type", GrantType },
                    { "client_id", ClientId },
                    { "client_secret", ClientSecret },
                    // { "scope", "contact_read" } // may be optional, depending on the server
                });

                var response = await httpClient.PostAsync(tokenEndpointUrl, formBody);
                return await response.Content.ReadAsStringAsync();
            });

            Task.WaitAll(authenticationTask);

            Console.WriteLine(authenticationTask.Result);
        }
    }
}
