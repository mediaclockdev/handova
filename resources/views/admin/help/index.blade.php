<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
        <div class="main-content">
            <div class="content-wrapper">
                @include('partials.navbar')
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What is a Payment Gateway?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-body-content">
                                    <strong>Do I need to pay to Instapay even when there is no transaction going on in
                                        my business?</strong>
                                </div>
                                <div class="accordion-body-content">A payment gateway is an ecommerce service that
                                    processes online payments for online
                                    as well as offline businesses. Payment gateways help accept payments by transferring
                                    key information from their merchant websites to issuing banks, card associations and
                                    online wallet players.</div>

                                <div class="accordion-body-content">Payment gateways play a vital role in the online
                                    transaction process, which is the
                                    realisation of value, and hence are seen as an important pillar of ecommerce.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Do I need to pay to Instapay even when there is no transaction going on in my business?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-body-content">
                                    <strong>Do I need to pay to Instapay even when there is no transaction going on in
                                        my business?</strong>
                                </div>
                                <div class="accordion-body-content">A payment gateway is an ecommerce service that
                                    processes online payments for online
                                    as well as offline businesses. Payment gateways help accept payments by transferring
                                    key information from their merchant websites to issuing banks, card associations and
                                    online wallet players.</div>

                                <div class="accordion-body-content">Payment gateways play a vital role in the online
                                    transaction process, which is the
                                    realisation of value, and hence are seen as an important pillar of ecommerce.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                What platforms does Instapay payment gateway support?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-body-content">
                                    <strong>Do I need to pay to Instapay even when there is no transaction going on in
                                        my business?</strong>
                                </div>
                                <div class="accordion-body-content">A payment gateway is an ecommerce service that
                                    processes online payments for online
                                    as well as offline businesses. Payment gateways help accept payments by transferring
                                    key information from their merchant websites to issuing banks, card associations and
                                    online wallet players.</div>

                                <div class="accordion-body-content">Payment gateways play a vital role in the online
                                    transaction process, which is the
                                    realisation of value, and hence are seen as an important pillar of ecommerce.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFourth" aria-expanded="false" aria-controls="collapseFourth">
                                Does Instapay provide international payments support?
                            </button>
                        </h2>
                        <div id="collapseFourth" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-body-content">
                                    <strong>Do I need to pay to Instapay even when there is no transaction going on in
                                        my business?</strong>
                                </div>
                                <div class="accordion-body-content">A payment gateway is an ecommerce service that
                                    processes online payments for online
                                    as well as offline businesses. Payment gateways help accept payments by transferring
                                    key information from their merchant websites to issuing banks, card associations and
                                    online wallet players.</div>

                                <div class="accordion-body-content">Payment gateways play a vital role in the online
                                    transaction process, which is the
                                    realisation of value, and hence are seen as an important pillar of ecommerce.</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFifth" aria-expanded="false" aria-controls="collapseFifth">
                                Is there any setup fee or annual maintainance fee that I need to pay regularly?
                            </button>
                        </h2>
                        <div id="collapseFifth" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="accordion-body-content">
                                    <strong>Do I need to pay to Instapay even when there is no transaction going on in
                                        my business?</strong>
                                </div>
                                <div class="accordion-body-content">A payment gateway is an ecommerce service that
                                    processes online payments for online
                                    as well as offline businesses. Payment gateways help accept payments by transferring
                                    key information from their merchant websites to issuing banks, card associations and
                                    online wallet players.</div>

                                <div class="accordion-body-content">Payment gateways play a vital role in the online
                                    transaction process, which is the
                                    realisation of value, and hence are seen as an important pillar of ecommerce.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>

</html>
