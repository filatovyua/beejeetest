const auth = () => {
    
    let loginEl = document.querySelector('.input__login input');
    let passwordEl = document.querySelector('.input__password input'); 
    document.querySelectorAll('.input--error').forEach((cur) => {
        cur.classList.remove('input--error');
    })
       
    let erEl = [];
    if (!loginEl.value || loginEl.value.length === 0){
        erEl.push(loginEl);
    }
    if (!passwordEl.value || passwordEl.value.length === 0){
        erEl.push(passwordEl);
    }
    if (erEl.length > 0) {
        return erEl.map((cur) => {
            cur.classList.add('input--error');
        })
    }    
    fetch('auth/auth', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({login: loginEl.value, password: passwordEl.value})
    })
            .then((response) => response.json())
            .then((data) => {
                if (data.error){
                    return document.querySelector('.error-message').innerHTML = data.message || 'error';
                }                
                window.location.href = '/';
            })
}


document.addEventListener('click', function (event) {
    let el = event.target;
    if (el.classList.contains('auth__button-auth')){
        return auth();
    }
});


