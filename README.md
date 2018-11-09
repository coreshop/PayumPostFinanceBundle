# CoreShop PostFinance Payum Connector
This Bundle activates the PostFinance PaymentGateway in CoreShop.
It requires the [dachcom-digital/payum-postfinance](https://github.com/dachcom-digital/payum-postfinance) repository which will be installed automatically.

## Important Information
There is nasty behaviour if a customer returns from the PostFinance payment page: PostFinance fires the callback page **twice** if the user clicks the *"abort"* or *"ok"* button. This leads to a "token not fond" since the token gets invalidated at the first request.
This Bundle will partly override the `HttpRequestVerifierBuilder` to prevent this invalidation (and triggers only if the paymentgateway matches with PostFinance.)
To prevent tokens to stay forever, there is also a Command and Maintenance Script which removes outdated "Capture" and "After-Pay" tokens older than two days.

## Requirements
CoreShop >= 2.0.0-beta.1

## Installation

#### 1. Composer
```json
    "coreshop/payum-postfinance-bundle": "~1.0.0"
```

#### 2. Activate
Enable the Bundle in Pimcore Extension Manager

#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `postfinance` from `type` and fill out the required fields.

## Maintenance Listener
Every 24h a maintenance script will remove `capture` and `after-pay` tokens older than two days.
If you want to change the amount, just override the parameter:

```yml
parameters:
    post_finance_token_expired_days: 20
```

## Command
Remove outdated tokens
```bash
$ bin/console postfinance:invalidate-expired-tokens --days=20
```

## Changelog

### v1.0.3
- HOMEURL added (set to "NONE" if you don't want to display a "Back to Merchant Shop" Button on PostFinance Checkout)
