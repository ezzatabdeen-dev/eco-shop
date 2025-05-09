document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    
    paymentOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        
        radio.addEventListener('change', function() {
            paymentOptions.forEach(opt => {
                if (opt.querySelector('input[type="radio"]').checked) {
                    opt.classList.add('active');
                } else {
                    opt.classList.remove('active');
                }
            });
        });
        
        option.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'A') {
                radio.checked = true;
                paymentOptions.forEach(opt => {
                    if (opt.querySelector('input[type="radio"]').checked) {
                        opt.classList.add('active');
                    } else {
                        opt.classList.remove('active');
                    }
                });
            }
        });
    });
    
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === 'credit_card') {
                const cardNumber = document.querySelector('input[name="card_number"]').value;
                const expMonth = document.querySelector('select[name="exp_month"]').value;
                const expYear = document.querySelector('select[name="exp_year"]').value;
                const cvv = document.querySelector('input[name="cvv"]').value;
                
                if (!cardNumber || !expMonth || !expYear || !cvv) {
                    e.preventDefault();
                    alert('Please complete all credit card details');
                    return;
                }
                
                if (cardNumber.replace(/\s/g, '').length !== 16) {
                    e.preventDefault();
                    alert('Please enter a valid 16-digit card number');
                    return;
                }
                
                if (cvv.length !== 3) {
                    e.preventDefault();
                    alert('Please enter a valid 3-digit CVV');
                    return;
                }
            }
            
        });
    }
    
    const cardNumberInput = document.querySelector('.card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 16) {
                value = value.substr(0, 16);
            }
            
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            e.target.value = formattedValue;
        });
    }
});