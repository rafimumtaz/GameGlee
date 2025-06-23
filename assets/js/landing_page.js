let currentIndex = 0;
    const items = document.querySelectorAll('.slider .item');
    const totalItems = items.length;

    function showNextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        const newTransformValue = `translateX(-${currentIndex * 100}%)`;
        document.querySelector('.slider .list').style.transform = newTransformValue;
    }

    setInterval(showNextSlide, 5000); // Change image every 5 seconds