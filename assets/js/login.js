const signUpButton = document.getElementById('signUp');
		const signInButton = document.getElementById('signIn');
		const main = document.getElementById('main');

		signUpButton.addEventListener('click', () => {
			main.classList.add("right-panel-active");
		});
		signInButton.addEventListener('click', () => {
			main.classList.remove("right-panel-active");
		});
    let currentIndex = 0;
    const items = document.querySelectorAll('.slider .item');
    const totalItems = items.length;

    function showNextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        const newTransformValue = `translateX(-${currentIndex * 100}%)`;
        document.querySelector('.slider .list').style.transform = newTransformValue;
    }

    setInterval(showNextSlide, 5000); // Change image every 5 seconds;