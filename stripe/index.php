<?php
require_once('config.php');
?>
<form action="charge.php" method="post" name="test">
  <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
          data-key="<?php echo $stripe['publishable_key']; ?>"
          data-name="Breeze Carpet Cleaners"
          data-description="Breeze Carpet Cleaners"
          data-amount="5000"
          data-image="stripe.png"
          data-currency="gbp"
          data-email="softratina@gmail.com"
          data-locale="auto"></script>
</form>