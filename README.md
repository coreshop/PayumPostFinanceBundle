# CoreShop PostFinance Payum Connector
This Bundle activates the PostFinance PaymentGateway in CoreShop.
It requires the [dachcom-digital/payum-postfinance](https://github.com/dachcom-digital/payum-postfinance) repository which will be installed automatically.

## Important Information
There is nasty behaviour if a customer returns from the PostFinance payment page:
PostFinance fires the callback page **twice** if the user clicks the *"abort"* or *"ok"* button. This leads to a "token not fond" since the token gets invalidated at the first request.
This Bundle will override the `HttpRequestVerifierBuilder` to prevent this invalidation.
Since this would lead the tokens to stay forever, there is also a Command and Maintenance Script which removes outdated "Capture" and "After-Pay" Tokens older than two days.

## Installation

#### 1. Composer
    ```json
    "coreshop/payum-postfinance-bundle": "dev-master"
    ```
#### 2. Activate
Enable the Bundle in Pimcore Extension Manager
#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `postfinance` from `type` and fill out the required fields.

