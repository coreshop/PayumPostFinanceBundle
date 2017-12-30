# CoreShop PostFinance Payum Connector
This Bundle activates the PostFinance PaymentGateway in CoreShop.
It requires the [dachcom-digital/payum-postfinance](https://github.com/dachcom-digital/payum-postfinance) repository which will be installed automatically.

## Installation

#### 1. Composer
    ```json
    "coreshop/payum-postfinance-bundle": "dev-master"
    ```
#### 2. Activate
Enable the Bundle in Pimcore Extension Manager
#### 3. Setup
Go to Coreshop -> PaymentProvider and add a new Provider. Choose `postfinance` from `type` and fill out the required fields.

