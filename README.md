# community_store_authorize_net_accept_hosted
Authorize.net (Accept Hosted) payment add-on for Community Store for Concrete CMS
http://www.authorize.net/

This add-on uses the 'Accept Hosted' integration method, which is a mobile-optimized payment form hosted by Authorize.net.
The payment form is embedded in the checkout page (as a modal overlay), but is directly provided by Authorize.net, meaning that SAQ-A level PCI compliance can be maintained.

## Setup
Install Community Store First.

Download a 'release' zip of the add-on, unzip this to the packages folder of your Concrete CMS install (alongside the community_store folder) and install via the dashboard.

The payment method is configured via Community Store's Setting page under the Payments section.
Notes are provided where to find the Login ID, Transaction Key and Public Client Key required.
