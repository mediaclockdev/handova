<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscription Plans</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&family=Inter:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="global.css">
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #4ea882;
      background-image: url('https://powderblue-raccoon-276071.hostingersite.com/images/login-bg.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      color: #1b1202;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(78, 168, 130, 0.8);
      z-index: 0;
    }

    .page-container {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    /* CSS for section section:pricing */
    .pricing-container {
      background-color: #fff7e9;
      border-radius: 16px;
      padding: 48px;
      width: 100%;
      max-width: 1016px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 25px;
    }

    .pricing-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 25px;
      width: 100%;
    }

    .logo {
      max-width: 191px;
      height: auto;
    }

    .main-title {
      font-family: 'Inter', sans-serif;
      font-weight: 500;
      font-size: 20px;
      line-height: 1.2;
      color: #000000;
      text-align: center;
      margin: 0;
    }

    .billing-toggle {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .toggle-label {
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      font-size: 16px;
      color: #1b1202;
      cursor: pointer;
    }

    .toggle-label-active {
      font-weight: 600;
      color: #f9b13b;
    }

    /* Toggle Switch */
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 26px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      border-radius: 30px;
      transition: .4s;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      border-radius: 50%;
      transition: .4s;
    }

    input:checked+.slider {
      background-color: #f9b13b;
    }

    input:checked+.slider:before {
      transform: translateX(24px);
    }

    .plans-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 52px;
      width: 100%;
    }

    .plan-card {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .plan-content {
      border: 1px solid #b0b0b0;
      border-radius: 8px;
      padding: 10px 16px;
      display: flex;
      flex-direction: column;
      gap: 11px;
      flex-grow: 1;
    }

    .plan-name {
      font-weight: 500;
      font-size: 16px;
      line-height: 24px;
      text-align: center;
      margin: 0;
      padding: 10px;
      border-bottom: 1px solid #b0b0b0;
    }

    .plan-price {
      font-weight: 400;
      font-size: 24px;
      line-height: 36px;
      text-align: center;
      margin: 10px 0;
    }

    .benefits-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 11px;
    }

    .benefit-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      line-height: 21px;
    }

    .checkbox {
      position: relative;
      width: 16px;
      height: 16px;
      flex-shrink: 0;
    }

    .checkbox-box {
      width: 100%;
      height: 100%;
      border-radius: 4px;
      border: 1.5px solid #595d62;
    }

    .checkbox.checked .checkbox-box {
      background-color: #f9b13b;
      border: none;
    }

    .checkbox-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100%;
      height: 100%;
      display: none;
    }

    .checkbox.checked .checkbox-icon {
      display: block;
    }

    .buy-button {
      background-color: #f9b13b;
      color: #ffffff;
      font-weight: 500;
      font-size: 16px;
      line-height: 24px;
      text-decoration: none;
      text-align: center;
      padding: 10px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .buy-button:hover {
      background-color: #e09e28;
    }

    .hidden {
      display: none;
    }


    @media (max-width: 992px) {
      .plans-grid {
        grid-template-columns: 1fr;
        gap: 32px;
      }

      .pricing-container {
        padding: 32px;
      }
    }

    @media (max-width: 480px) {
      .pricing-container {
        padding: 20px;
      }

      .billing-toggle {
        gap: 16px;
      }
    }
  </style>
</head>

<body>
  <section id="pricing" class="page-container">
    <div class="pricing-container">
      <header class="pricing-header">
        <img src="{{ asset('images/handova.svg') }}" alt="Handova Logo" class="logo">
        <h1 class="main-title">Choose a subscription plan to move forward</h1>
        <div class="billing-toggle">
          <span class="toggle-label toggle-label-active" data-plan="monthly">Monthly</span>
          <label class="switch">
            <input type="checkbox" id="billingToggle">
            <span class="slider"></span>
          </label>
          <span class="toggle-label" data-plan="annually">Annually</span>
        </div>
      </header>

      <div class="plans-grid">
        @forelse($subscriptionPlans as $plan)
        <article class="plan-card plan-{{ $plan->plan_type }} {{ $plan->plan_type == 'monthly' ? '' : 'hidden' }}">
          <div class="plan-content">
            <h2 class="plan-name">{{ $plan->plan_name }}</h2>
            <p class="plan-price">${{ $plan->plan_price }}</p>
            <ul class="benefits-list">
              <li class="benefit-item">
                <div class="checkbox checked">
                  <div class="checkbox-box"></div>
                  <img src="{{ asset('images/I384_22024_41_6659.svg') }}" alt="Checked" class="checkbox-icon">
                </div>
                <span>{{ $plan->plan_allowed_listing }} Allowed Listing</span>
              </li>
              <li class="benefit-item">
                <div class="checkbox checked">
                  <div class="checkbox-box"></div>
                  <img src="{{ asset('images/I384_22024_41_6659.svg') }}" alt="Checked" class="checkbox-icon">
                </div>
                <span>{{ $plan->plan_featured_properties }} Featured Properties</span>
              </li>
              <li class="benefit-item">
                <div class="checkbox checked">
                  <div class="checkbox-box"></div>
                  <img src="{{ asset('images/I384_22024_41_6659.svg') }}" alt="Checked" class="checkbox-icon">
                </div>
                <span>{{ $plan->plan_photo_upload_limit }} Photo Upload Limit</span>
              </li>
              <li class="benefit-item">
                <div class="checkbox checked">
                  <div class="checkbox-box"></div>
                  <img src="{{ asset('images/I384_22024_41_6659.svg') }}" alt="Checked" class="checkbox-icon">
                </div>
                <span>{{ $plan->plan_video_upload_limit }} Video Upload Limit</span>
              </li>
            </ul>
          </div>
          <a href="#" class="buy-button" data-plantype="{{ $plan->plan_type }}" data-planprice="{{ $plan->plan_price }}">Buy now</a>
        </article>
        @empty
        <p>No subscription plans available.</p>
        @endforelse
      </div>

    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const toggle = document.getElementById("billingToggle");
      const labels = document.querySelectorAll(".toggle-label");

      toggle.addEventListener("change", function() {
        const planType = this.checked ? "yearly" : "monthly";

        labels.forEach(label => {
          if (label.dataset.plan === planType) {
            label.classList.add("toggle-label-active");
          } else {
            label.classList.remove("toggle-label-active");
          }
        });

        document.querySelectorAll(".plan-card").forEach(card => {
          if (card.classList.contains(`plan-${planType}`)) {
            card.classList.remove("hidden");
          } else {
            card.classList.add("hidden");
          }
        });
      });
    });
  </script>
</body>

</html>