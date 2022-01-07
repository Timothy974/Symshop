const stripe = Stripe(stripePublicKey);

const elements = stripe.elements();

		const card = elements.create("card");
		card.mount("#card-element");

		card.on('change', ({error}) => {
			let displayError = document.getElementById('card-errors');
			if (error) {
				displayError.textContent = error.message;
			} else {
				displayError.textContent = '';
			}
		});

const form = document.getElementById('payment-form');

form.addEventListener('submit', function(ev) {
  ev.preventDefault();
  stripe.confirmCardPayment(clientSecret, {
    payment_method: {
      card: card,
      billing_details: {
        name: user
      }
    }
  }).then(function(result) {
    if (result.error) {
      // Show error to your customer (for example, insufficient funds)
      console.log(result.error.message);
    } else {
      // The payment has been processed!
		 window.location.href = redirectAfterSuccessUrl;
    }
  });
});