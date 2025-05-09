document.getElementById('offer_form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('subscripeEmail').value;
    const alertBox = document.getElementById('alertBox');
    
    try {
        const response = await fetch('./api/subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alertBox.textContent = data.message;
            alertBox.classList.remove('alert-hidden');
            alertBox.classList.add('alert-success');
            
            setTimeout(() => {
                alertBox.classList.remove('alert-success');
                alertBox.classList.add('alert-hidden');
            }, 3000);
            
            document.getElementById('subscripeEmail').value = '';
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        alertBox.textContent = error.message;
        alertBox.classList.remove('alert-hidden');
        alertBox.classList.add('alert-error');
        
        setTimeout(() => {
            alertBox.classList.remove('alert-error');
            alertBox.classList.add('alert-hidden');
        }, 3000);
    }
});