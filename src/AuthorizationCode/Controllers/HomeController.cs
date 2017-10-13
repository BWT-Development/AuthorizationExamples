using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Web;
using System.Web.Mvc;

namespace AuthorizationCode.Controllers
{
    /**
     * This Authentication is an example based on:
     * https://openid.net/specs/openid-connect-core-1_0.html#CodeFlowAuth
     */
    public class HomeController : Controller
    {
        // specific values
        private string AuthenticationEndpoint = "<openid_provider_url";
        private string ClientId = "<your client_id>";
        private string ClientSecret = "<your client_secret>";
        private string CallbackUrl = "https://localhost:44326/Home/Callback";

        // endpoint under which the server hosts the openid connect endpoint
        private const string AuthenticationPath = "<openid_connect_endpoint>";

        // open id constants
        private const string AuthorizeEndpoint = "/authorize";
        private const string TokenEndpoint = "/token";

        private const string AuthorizationCodeResponseType = "code";
        private const string ScopeOpenId = "openid";

        // !!!! do not use this method in a productive environment!
        // !!!! this is only for this showcase
        private static string State = string.Empty;

        public ActionResult Index()
        {
            return View();
        }

        public ActionResult Login()
        {
            // https://openid.net/specs/openid-connect-core-1_0.html#AuthRequest
            State = Guid.NewGuid().ToString().Replace("-", "");

            var authenticationRequest = 
                $"{this.AuthenticationEndpoint}{AuthenticationPath}{AuthorizeEndpoint}"
                + "?response_type=" + AuthorizationCodeResponseType
                + "&client_id=" + ClientId
                + "&scope=" + ScopeOpenId
                + "&state=" + State
                + "&redirect_uri=" + this.CallbackUrl;

            return this.Redirect(authenticationRequest);
        }

        /// <summary>
        /// This action is invoked by the OpenId Provider
        /// and provides you with the authorization code, the session_state
        /// and the stat if you sent one in the request
        /// </summary>
        public ActionResult Callback(string code, string session_state, string state)
        {
            // note: errors are not handled in this example

            // check if the response matches with the clients state
            // otherwise the authentication request is not made from your client and should be revoked!
            if (State != state)
            {
                throw new InvalidOperationException("Response not from my request, state is invalid");
            }
            State = string.Empty;

            // https://openid.net/specs/openid-connect-core-1_0.html#TokenRequest
            var tokenRequest = $"{this.AuthenticationEndpoint}{AuthenticationPath}{TokenEndpoint}";
            using (var client = new WebClient())
            {
                client.Headers.Add(HttpRequestHeader.ContentType, "application/x-www-form-urlencoded");
                var body = 
                    "grant_type=authorization_code"
                    + "&code=" + code
                    + "&client_id=" + this.ClientId
                    + "&client_secret=" + this.ClientSecret
                    + "&redirect_uri=" + HttpUtility.UrlEncode(this.CallbackUrl);

                // https://openid.net/specs/openid-connect-core-1_0.html#TokenResponse
                var response = client.UploadString(tokenRequest, body);
            }
            
            return this.Content("done");
        }
    }
}