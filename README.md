# whats360.live PHP SDK (PHP WhatsApp API client)

Small PHP client (7.4+, cURL) for the [whats360.live](https://whats360.live) WhatsApp API: send messages, manage devices, create and start campaigns.

```php
require 'Client.php';
$w = new Whats360\Client(getenv('WHATS360_TOKEN'), getenv('WHATS360_INSTANCE_ID'));
$w->sendText('201234567890@s.whatsapp.net', 'Hello');
```

Methods: `sendText, sendImage, sendVideo, sendAudio, sendDoc, listDevices, createDevice, connectDevice, deviceStatus, deviceQr, createCampaign, startCampaign, campaignStatus`. For pause, resume, stop, delete and recipients, see the [API reference](https://github.com/whats360-api/whats360-api-docs/blob/main/api-reference.md).

## Status
Written from the official reference. **Not executed**: PHP was not available in the build environment, so there is neither a syntax check nor a live test. The equivalent [Node.js SDK](https://github.com/whats360-api/whats360-nodejs-sdk) has mock tests. Treat this as an example until you run it.

Never commit your token. WhatsApp: https://wa.me/whats360.live · Direct chat support: https://wa.me/201030741766 · info@whats360.live
