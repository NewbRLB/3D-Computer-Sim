document.addEventListener('DOMContentLoaded', () => {

    // --- UPWARD MOTION FOR ALL BUTTONS ---
    const allButtons = document.querySelectorAll('.btn');

    allButtons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.transform = 'translateY(-3px)';
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translateY(0)';
        });
    });

    // --- 3D FLIP CARD HANDLER ---
    const flipScene = document.getElementById('flipScene');
    const toSignup = document.getElementById('toSignup');
    const toLogin = document.getElementById('toLogin');

    if (toSignup && flipScene) {
        toSignup.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            flipScene.classList.add('flipped');
            
            setTimeout(() => {
                const signupUser = document.getElementById('signupUser');
                if (signupUser) signupUser.focus();
            }, 450);
        });
    }

    if (toLogin && flipScene) {
        toLogin.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            flipScene.classList.remove('flipped');
            
            setTimeout(() => {
                const loginEmail = document.getElementById('loginEmail');
                if (loginEmail) loginEmail.focus();
            }, 450);
        });
    }

    // --- LOGIN FORM ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const btn = document.getElementById('loginBtn');
            
            btn.disabled = true;
            btn.textContent = 'Logging in...';

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('/api/login_process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    btn.textContent = 'Success!';
                    btn.style.background = '#00ff88';
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 400);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.textContent = 'Login';
                    btn.style.background = '';
                }
            } catch (error) {
                console.error('Login Error:', error);
                alert('An error occurred during login.');
                btn.disabled = false;
                btn.textContent = 'Login';
            }
        });
    }

    // --- IN-CARD SIGNUP FORM (FLIP CARD BACK SIDE) ---
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('signupUser').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const btn = document.getElementById('signupBtn');

            btn.disabled = true;
            btn.textContent = 'Registering...';

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('/api/signup_process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    btn.textContent = 'Account Created!';
                    btn.style.background = '#00ff88';
                    setTimeout(() => {
                        if (flipScene) flipScene.classList.remove('flipped');
                        signupForm.reset();
                        btn.disabled = false;
                        btn.textContent = 'Register';
                        btn.style.background = '#0ef';
                        btn.style.transform = 'translateY(0)';
                    }, 600);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.textContent = 'Register';
                }
            } catch (error) {
                console.error('Signup Error:', error);
                alert('An error occurred during registration.');
                btn.disabled = false;
                btn.textContent = 'Register';
            }
        });
    }

    // --- STANDALONE SIGNUP FORM ---
    const standaloneSignupForm = document.getElementById('standaloneSignupForm');
    if (standaloneSignupForm) {
        standaloneSignupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password')?.value;
            const confirmPassword = document.getElementById('confirmPassword')?.value;
            const btn = document.getElementById('signupSubmitBtn');

            if (password && confirmPassword && password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Registering...';

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch('/api/signup_process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    btn.textContent = 'Account Created!';
                    btn.style.background = '#00ff88';
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 500);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.textContent = 'Register';
                }
            } catch (error) {
                console.error('Standalone Signup Error:', error);
                alert('An error occurred during registration.');
                btn.disabled = false;
                btn.textContent = 'Register';
            }
        });
    }

    // --- RESET PASSWORD FORM ---
    const resetForm = document.getElementById('resetForm');
    if (resetForm) {
        resetForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = document.getElementById('resetEmail');
            const email = emailInput ? emailInput.value.trim() : '';
            const btn = document.getElementById('resetSubmitBtn');

            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {
                const formData = new FormData();
                formData.append('email', email);

                const response = await fetch('/api/forgot_process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    btn.textContent = 'Link Sent!';
                    btn.style.background = '#00ff88';
                    setTimeout(() => {
                        alert(data.message);
                        window.location.href = 'login.html';
                    }, 500);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                    btn.textContent = 'Send Reset Link';
                }
            } catch (error) {
                console.error('Reset Error:', error);
                alert('An error occurred.');
                btn.disabled = false;
                btn.textContent = 'Send Reset Link';
            }
        });
    }
});
